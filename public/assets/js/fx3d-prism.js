/* Yojo Solutions — "Prism Pulse" 3D stage.
   One fixed WebGL canvas; five procedural scenes that cross-fade as the visitor
   scrolls through sections tagged with data-scene="vortex|voxel|rift|wave|galaxy".
   All geometry is generated in code (no external models). */
import * as THREE from 'three';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';

let PAL = ['#ff2fb9', '#ff4d6d', '#ff7a1a', '#ffb31a', '#ffe53b', '#3bff7a'].map((c) => new THREE.Color(c));
const grad = (t) => { // t 0..1 across the palette
  t = Math.min(0.9999, Math.max(0, t)) * (PAL.length - 1);
  const i = Math.floor(t); return PAL[i].clone().lerp(PAL[i + 1], t - i);
};

export function start(canvas, opts = {}) {
  const LIGHT = !!opts.light;
  if (opts.palette === 'sentinel') PAL = ['#0bb85c', '#12c96a', '#9ad81a', '#f5b400', '#ff7a2e', '#ff2e4d'].map((c) => new THREE.Color(c));
  const BG = LIGHT ? (opts.palette === 'sentinel' ? '#ffffff' : '#fbf6ef') : '#0a0507';
  const BLEND = LIGHT ? THREE.NormalBlending : THREE.AdditiveBlending;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const small = innerWidth < 768;
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: !small, powerPreference: 'high-performance' });
  const dpr = Math.min(devicePixelRatio || 1, small ? 1.4 : 1.75);
  renderer.setPixelRatio(dpr);
  renderer.setSize(innerWidth, innerHeight, false);
  renderer.outputColorSpace = THREE.SRGBColorSpace;

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(BG);
  scene.fog = new THREE.FogExp2(BG, LIGHT ? 0.03 : 0.018);
  const camera = new THREE.PerspectiveCamera(45, innerWidth / innerHeight, 0.1, 200);
  camera.position.set(0, 0, 9);
  scene.add(new THREE.AmbientLight(0xffffff, LIGHT ? 1.1 : 0.35));
  const key = new THREE.PointLight('#ff7a1a', 60, 30); key.position.set(4, 3, 6); scene.add(key);
  const rim = new THREE.PointLight('#ff2fb9', 60, 30); rim.position.set(-5, -2, 3); scene.add(rim);

  const uTime = { value: 0 };
  const scenes = {};
  const fadeMats = (group) => { group.traverse((o) => { if (o.material) { const ms = [].concat(o.material); ms.forEach((m) => { m.transparent = true; m.userData.base = m.userData.base ?? (m.uniforms && m.uniforms.uOpacity ? m.uniforms.uOpacity.value : m.opacity); }); } }); };
  const setFade = (group, f) => { group.traverse((o) => { if (o.material) [].concat(o.material).forEach((m) => { if (m.uniforms && m.uniforms.uOpacity) m.uniforms.uOpacity.value = m.userData.base * f; else m.opacity = m.userData.base * f; }); }); };

  const pointsMat = (size) => new THREE.ShaderMaterial({
    transparent: true, depthWrite: false, blending: BLEND, vertexColors: true,
    uniforms: { uSize: { value: size * dpr }, uOpacity: { value: 1 } },
    vertexShader: 'uniform float uSize; varying vec3 vC; void main(){ vC = color; vec4 mv = modelViewMatrix * vec4(position,1.0); gl_PointSize = uSize * (10.0 / -mv.z); gl_Position = projectionMatrix * mv; }',
    fragmentShader: 'uniform float uOpacity; varying vec3 vC; void main(){ float d = length(gl_PointCoord - 0.5); if (d > 0.5) discard; gl_FragColor = vec4(vC, smoothstep(0.5, 0.0, d) * uOpacity); }',
  });

  /* 1 ─ VORTEX: particle tunnel rushing toward the camera */
  {
    const g = new THREE.Group(); const N = small ? 5000 : 11000;
    const pos = new Float32Array(N * 3), col = new Float32Array(N * 3), meta = [];
    for (let i = 0; i < N; i++) {
      const a = Math.random() * Math.PI * 2, r = 1.6 + Math.pow(Math.random(), 0.6) * 3.4, z = -70 + Math.random() * 78;
      meta.push({ a, r, z, s: 0.6 + Math.random() });
      const c = grad((a / (Math.PI * 2) + r / 10) % 1); col.set([c.r, c.g, c.b], i * 3);
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3)); geo.setAttribute('color', new THREE.BufferAttribute(col, 3));
    const pts = new THREE.Points(geo, pointsMat(6)); pts.frustumCulled = false; g.add(pts);
    const core = new THREE.Mesh(new THREE.SphereGeometry(0.9, 32, 32), new THREE.MeshBasicMaterial({ color: LIGHT ? PAL[0] : '#000000' })); core.position.z = -40; g.add(core);
    scenes.vortex = { g, x: 0, y: 0, update(t, dt, speed) {
      for (let i = 0; i < N; i++) {
        const m = meta[i]; m.z += dt * (6 + speed * 60) * m.s; if (m.z > 8) m.z -= 78;
        const a = m.a + t * 0.25 + m.z * 0.04; pos[i * 3] = Math.cos(a) * m.r; pos[i * 3 + 1] = Math.sin(a) * m.r; pos[i * 3 + 2] = m.z;
      }
      geo.attributes.position.needsUpdate = true;
    } };
  }

  /* 2 ─ RISK HEAT-MAP: 5x5 likelihood × impact towers (sharp, outlined) with a
         shield + padlock hovering over the highest-risk cell */
  {
    const g = new THREE.Group(); const N = 5, step = 0.62;
    const sev = ['#9be22d', '#ffd21f', '#ff9a1a', '#ff5a3c', '#ff2fb9'];
    const edge = new THREE.LineBasicMaterial({ color: LIGHT ? '#3a0d22' : '#ffffff', transparent: true, opacity: LIGHT ? 0.4 : 0.25 });
    const box = new THREE.BoxGeometry(0.5, 1, 0.5); box.translate(0, 0.5, 0);
    const boxEdges = new THREE.EdgesGeometry(box);
    const towers = [];
    const base = new THREE.Mesh(new THREE.BoxGeometry(N * step + 0.3, 0.12, N * step + 0.3), new THREE.MeshStandardMaterial({ color: LIGHT ? '#fff3ea' : '#1a0b12', roughness: 0.5, metalness: 0.2 }));
    base.position.y = -0.06; base.add(new THREE.LineSegments(new THREE.EdgesGeometry(base.geometry), edge)); g.add(base);
    for (let x = 0; x < N; x++) for (let z = 0; z < N; z++) {
      const level = Math.min(4, Math.round((x + (N - 1 - z)) / 2)); // likelihood + impact
      const m = new THREE.Mesh(box, new THREE.MeshStandardMaterial({ color: sev[level], roughness: 0.28, metalness: 0.35, emissive: sev[level], emissiveIntensity: LIGHT ? 0.08 : 0.3 }));
      m.add(new THREE.LineSegments(boxEdges, edge));
      m.position.set((x - (N - 1) / 2) * step, 0, (z - (N - 1) / 2) * step);
      const h = 0.25 + level * 0.42 + ((x * 7 + z * 3) % 4) * 0.06;
      towers.push({ m, h, ph: x * 0.6 + z * 0.4 }); g.add(m);
    }
    // shield + lock emblem over the hot corner
    const sh = new THREE.Shape(); sh.moveTo(0, 0.62); sh.bezierCurveTo(0.25, 0.5, 0.42, 0.48, 0.52, 0.48); sh.bezierCurveTo(0.52, 0.04, 0.42, -0.38, 0, -0.64); sh.bezierCurveTo(-0.42, -0.38, -0.52, 0.04, -0.52, 0.48); sh.bezierCurveTo(-0.42, 0.48, -0.25, 0.5, 0, 0.62);
    const shGeo = new THREE.ExtrudeGeometry(sh, { depth: 0.14, bevelEnabled: true, bevelSize: 0.04, bevelThickness: 0.04, bevelSegments: 3 }); shGeo.center();
    const emb = new THREE.Group();
    const shield = new THREE.Mesh(shGeo, new THREE.MeshStandardMaterial({ color: '#ff2fb9', roughness: 0.25, metalness: 0.5, emissive: '#ff2fb9', emissiveIntensity: LIGHT ? 0.1 : 0.4 }));
    shield.add(new THREE.LineSegments(new THREE.EdgesGeometry(shGeo, 30), edge)); emb.add(shield);
    const lockBody = new THREE.Mesh(new THREE.BoxGeometry(0.34, 0.26, 0.08), new THREE.MeshStandardMaterial({ color: '#ffffff', roughness: 0.3, metalness: 0.2 })); lockBody.position.set(0, -0.06, 0.12); emb.add(lockBody);
    const shackle = new THREE.Mesh(new THREE.TorusGeometry(0.11, 0.03, 10, 24, Math.PI), lockBody.material); shackle.position.set(0, 0.07, 0.12); emb.add(shackle);
    emb.position.set(((N - 1) / 2) * step, 3.0, -((N - 1) / 2) * step); emb.scale.setScalar(1.3); g.add(emb);
    g.scale.setScalar(small ? 0.8 : 1);
    scenes.voxel = { g, x: 2.7, y: -0.9, update(t, dt, speed, ptr) {
      towers.forEach((o) => { const k = o.h * (0.82 + 0.18 * Math.sin(t * 1.3 + o.ph)) + speed * 2; o.m.scale.y = Math.max(0.08, k); });
      emb.rotation.y = t * 0.9; emb.position.y = 3.0 + Math.sin(t * 1.4) * 0.15;
      g.rotation.set(0.55 + ptr.y * 0.2, -0.7 + t * 0.12 + ptr.x * 0.4, 0);
    } };
  }

  /* 3 ─ CYBER / AUDIT model — alternates on every page load:
         cyber = wireframe globe with a shield core and orbiting data packets
         audit = stacked audit reports scanned by a magnifying lens */
  {
    const g = new THREE.Group();
    let mode = 'cyber';
    try { mode = localStorage.getItem('yojo_m3d') === 'cyber' ? 'audit' : 'cyber'; localStorage.setItem('yojo_m3d', mode); } catch (e) { mode = Math.random() < 0.5 ? 'cyber' : 'audit'; }
    const ink = LIGHT ? '#2a1520' : '#e9dde4';
    const metal = new THREE.MeshStandardMaterial({ color: LIGHT ? '#2a1520' : '#3a2230', roughness: 0.3, metalness: 0.85 });
    const silver = new THREE.MeshStandardMaterial({ color: '#d9d4d8', roughness: 0.28, metalness: 1 });
    const glowM = (c, k = 1.2) => new THREE.MeshStandardMaterial({ color: c, emissive: c, emissiveIntensity: k });
    const line = (c, o = 0.6) => new THREE.LineBasicMaterial({ color: c, transparent: true, opacity: o });
    const parts = {};
    if (mode === 'cyber') {
      const globeGeo = new THREE.IcosahedronGeometry(1.7, 3);
      const globe = new THREE.LineSegments(new THREE.WireframeGeometry(globeGeo), line('#ff2fb9', LIGHT ? 0.35 : 0.5)); g.add(globe);
      const nodes = new THREE.Points(globeGeo, new THREE.PointsMaterial({ color: '#ff7a1a', size: 0.06 })); g.add(nodes);
      const sh = new THREE.Shape(); sh.moveTo(0, 0.7); sh.bezierCurveTo(0.3, 0.56, 0.48, 0.54, 0.6, 0.54); sh.bezierCurveTo(0.6, 0.05, 0.48, -0.42, 0, -0.72); sh.bezierCurveTo(-0.48, -0.42, -0.6, 0.05, -0.6, 0.54); sh.bezierCurveTo(-0.48, 0.54, -0.3, 0.56, 0, 0.7);
      const sg = new THREE.ExtrudeGeometry(sh, { depth: 0.2, bevelEnabled: true, bevelSize: 0.04, bevelThickness: 0.04, bevelSegments: 3 }); sg.center();
      const shield = new THREE.Mesh(sg, metal); shield.add(new THREE.LineSegments(new THREE.EdgesGeometry(sg, 30), line('#ff7ac8', 0.6))); g.add(shield);
      const tick = new THREE.Mesh(new THREE.TorusGeometry(0.22, 0.035, 8, 32), glowM('#ff2fb9')); tick.position.z = 0.16; shield.add(tick);
      const orbits = [];
      [[1.0, 0.3], [-0.6, 1.1], [0.2, -0.9]].forEach(([rx, rz], i) => {
        const o = new THREE.Group(); o.rotation.set(rx, 0, rz); g.add(o);
        const r = new THREE.Mesh(new THREE.TorusGeometry(2.25 + i * 0.18, 0.008, 6, 180), glowM(['#ff2fb9', '#ff7a1a', '#ffd21f'][i], 0.9)); o.add(r);
        const pk = new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.12, 0.12), silver); o.add(pk); orbits.push({ pk, r: 2.25 + i * 0.18, sp: 0.6 + i * 0.25 });
      });
      Object.assign(parts, { globe, nodes, shield, orbits });
    } else {
      const docs = new THREE.Group(); g.add(docs);
      for (let i = 0; i < 4; i++) {
        const d = new THREE.Mesh(new THREE.BoxGeometry(2.0, 0.05, 2.6), new THREE.MeshStandardMaterial({ color: i === 3 ? '#fffafc' : '#efe4ea', roughness: 0.6 }));
        d.position.set(i * 0.06, i * 0.12, -i * 0.05); d.rotation.y = (i - 1.5) * 0.06;
        d.add(new THREE.LineSegments(new THREE.EdgesGeometry(d.geometry), line(ink, 0.35))); docs.add(d);
      }
      const top = 3 * 0.12 + 0.03;
      for (let r = 0; r < 5; r++) {
        const ok = r !== 3;
        const box = new THREE.Mesh(new THREE.BoxGeometry(0.16, 0.02, 0.16), glowM(ok ? '#ff2fb9' : '#ff7a1a', ok ? 0.8 : 1)); box.position.set(-0.6, top, -0.9 + r * 0.42); docs.add(box);
        const bar = new THREE.Mesh(new THREE.BoxGeometry(0.9 - (r % 2) * 0.25, 0.02, 0.07), metal); bar.position.set(0.05 - (r % 2) * 0.12, top, -0.9 + r * 0.42); docs.add(bar);
      }
      docs.rotation.x = -0.35;
      const lens = new THREE.Group(); g.add(lens);
      const rim = new THREE.Mesh(new THREE.TorusGeometry(0.62, 0.07, 16, 64), silver); lens.add(rim);
      const glass = new THREE.Mesh(new THREE.CircleGeometry(0.6, 48), new THREE.MeshPhysicalMaterial({ color: '#ffe3f1', transmission: 0.9, roughness: 0.05, thickness: 0.3, transparent: true, opacity: 0.55 })); lens.add(glass);
      const handle = new THREE.Mesh(new THREE.CylinderGeometry(0.07, 0.09, 1.1, 20), metal); handle.position.set(0.72, -0.72, 0); handle.rotation.z = Math.PI / 4; lens.add(handle);
      const beam = new THREE.Mesh(new THREE.ConeGeometry(0.6, 1.4, 48, 1, true), new THREE.MeshBasicMaterial({ color: '#ff2fb9', transparent: true, opacity: 0.12, side: THREE.DoubleSide })); beam.rotation.x = Math.PI / 2; beam.position.z = -0.7; lens.add(beam);
      Object.assign(parts, { docs, lens });
    }
    g.scale.setScalar(1.05);
    scenes.rift = { g, x: -2.8, y: 0, update(t, dt, speed, ptr) {
      if (parts.globe) {
        parts.globe.rotation.y = t * 0.18; parts.nodes.rotation.y = t * 0.18;
        parts.shield.rotation.y = Math.sin(t * 0.8) * 0.5;
        parts.orbits.forEach((o) => { const a = t * o.sp; o.pk.position.set(Math.cos(a) * o.r, Math.sin(a) * o.r, 0); o.pk.rotation.set(a, a, 0); });
      } else {
        const a = t * 0.7; parts.lens.position.set(Math.cos(a) * 0.7, 0.9 + Math.sin(a * 1.3) * 0.25, 1.1 + Math.sin(a) * 0.4);
        parts.lens.rotation.set(-0.5, 0.2 * Math.cos(a), 0);
      }
      g.rotation.y = -0.35 + ptr.x * 0.4 + Math.sin(t * 0.3) * 0.15; g.rotation.x = 0.15 + ptr.y * 0.2;
    } };
  }

  /* 4 ─ FLOW WAVE: a gradient field of particles rolling like a liquid surface */
  {
    const g = new THREE.Group(); const W = small ? 90 : 160, H = small ? 40 : 70;
    const pos = new Float32Array(W * H * 3), col = new Float32Array(W * H * 3);
    for (let i = 0; i < W; i++) for (let j = 0; j < H; j++) { const k = (i * H + j) * 3; const c = grad(i / W); col.set([c.r, c.g, c.b], k); }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3)); geo.setAttribute('color', new THREE.BufferAttribute(col, 3));
    const pts = new THREE.Points(geo, pointsMat(3.2)); pts.frustumCulled = false; g.add(pts);
    g.rotation.x = -1.05;
    scenes.wave = { g, x: 0, y: -1.6, update(t, dt, speed, ptr) {
      for (let i = 0; i < W; i++) for (let j = 0; j < H; j++) {
        const k = (i * H + j) * 3, x = (i / W - 0.5) * 20, y = (j / H - 0.5) * 9;
        pos[k] = x; pos[k + 1] = y;
        pos[k + 2] = Math.sin(x * 0.45 + t * 1.2) * 0.6 + Math.cos(y * 0.7 + t * 0.9 + x * 0.2) * 0.45 + Math.sin((x + ptr.x * 6) * 0.2 - t) * 0.4;
      }
      geo.attributes.position.needsUpdate = true;
    } };
  }

  /* 5 ─ SPIRAL GALAXY */
  {
    const g = new THREE.Group(); const N = small ? 7000 : 16000;
    const pos = new Float32Array(N * 3), col = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) {
      const r = Math.pow(Math.random(), 1.6) * 5.5, arm = (i % 4) / 4 * Math.PI * 2, a = arm + r * 1.15;
      const sx = (Math.random() - 0.5) * 0.5 * (1 + r * 0.3), sz = (Math.random() - 0.5) * 0.5 * (1 + r * 0.3);
      pos.set([Math.cos(a) * r + sx, (Math.random() - 0.5) * 0.25, Math.sin(a) * r + sz], i * 3);
      const c = grad(Math.min(1, r / 5.5)); col.set([c.r, c.g, c.b], i * 3);
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3)); geo.setAttribute('color', new THREE.BufferAttribute(col, 3));
    g.add(new THREE.Points(geo, pointsMat(3)));
    g.rotation.x = 0.9;
    scenes.galaxy = { g, x: 2.2, y: 0, update(t, dt, speed, ptr) { g.rotation.y = t * 0.08; g.rotation.x = 0.9 + ptr.y * 0.2; g.rotation.z = ptr.x * 0.2; } };
  }

  /* 6 ─ CYBER SHIELD: extruded shield + check, padlock, scanner ring, data blocks */
  {
    const g = new THREE.Group();
    const sh = new THREE.Shape();
    sh.moveTo(0, 1.55); sh.bezierCurveTo(0.55, 1.2, 0.95, 1.15, 1.25, 1.15); sh.lineTo(1.25, 0.1);
    sh.bezierCurveTo(1.25, -0.85, 0.55, -1.35, 0, -1.65); sh.bezierCurveTo(-0.55, -1.35, -1.25, -0.85, -1.25, 0.1);
    sh.lineTo(-1.25, 1.15); sh.bezierCurveTo(-0.95, 1.15, -0.55, 1.2, 0, 1.55);
    const sg = new THREE.ExtrudeGeometry(sh, { depth: 0.3, bevelEnabled: true, bevelThickness: 0.08, bevelSize: 0.06, bevelSegments: 4, curveSegments: 48 }); sg.center();
    const shield = new THREE.Mesh(sg, new THREE.MeshStandardMaterial({ color: LIGHT ? '#9ff0c2' : '#101412', roughness: 0.3, metalness: LIGHT ? 0.2 : 0.8, emissive: PAL[1], emissiveIntensity: LIGHT ? 0.35 : 0.15 }));
    const edges = new THREE.LineSegments(new THREE.EdgesGeometry(sg, 25), new THREE.LineBasicMaterial({ color: PAL[1] }));
    const tick = new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3([new THREE.Vector3(-0.5, 0.05, 0.3), new THREE.Vector3(-0.12, -0.38, 0.3), new THREE.Vector3(0.62, 0.55, 0.3)], false, 'catmullrom', 0), 40, 0.09, 12), new THREE.MeshStandardMaterial({ color: PAL[3], emissive: PAL[3], emissiveIntensity: 0.5 }));
    const emblem = new THREE.Group(); emblem.add(shield, edges, tick); g.add(emblem);
    const lock = new THREE.Group();
    const lockMat = new THREE.MeshStandardMaterial({ color: PAL[5], emissive: PAL[5], emissiveIntensity: 0.35, roughness: 0.4 });
    const body = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.42, 0.2), lockMat);
    const shackle = new THREE.Mesh(new THREE.TorusGeometry(0.16, 0.045, 10, 30, Math.PI), lockMat); shackle.position.y = 0.21;
    lock.add(body, shackle); g.add(lock);
    const scan = new THREE.Mesh(new THREE.TorusGeometry(1.8, 0.015, 8, 120), new THREE.MeshBasicMaterial({ color: PAL[3] }));
    const disc = new THREE.Mesh(new THREE.CircleGeometry(1.8, 64), new THREE.MeshBasicMaterial({ color: PAL[3], opacity: 0.1, side: THREE.DoubleSide, depthWrite: false }));
    scan.rotation.x = disc.rotation.x = Math.PI / 2; g.add(scan, disc);
    const cubes = [];
    for (let i = 0; i < 14; i++) {
      const sz = 0.12 + Math.random() * 0.16;
      const c = new THREE.Mesh(new THREE.BoxGeometry(sz, sz, sz), new THREE.MeshStandardMaterial({ color: PAL[i % PAL.length], emissive: PAL[i % PAL.length], emissiveIntensity: 0.3 }));
      c.userData = { r: 2.3 + Math.random() * 1.5, a: Math.random() * Math.PI * 2, y: (Math.random() - 0.5) * 2.6, s: 0.15 + Math.random() * 0.25 };
      cubes.push(c); g.add(c);
    }
    scenes.shield = { g, x: 2.6, y: 0, update(t, dt, speed, ptr) {
      emblem.rotation.y = Math.sin(t * 0.5) * 0.5 + ptr.x * 0.6; emblem.rotation.x = ptr.y * -0.3;
      lock.position.set(Math.cos(t * 0.45) * 2.5, 0.9 + Math.sin(t * 0.9) * 0.25, Math.sin(t * 0.45) * 1.3); lock.rotation.y = t * 1.2;
      scan.position.y = disc.position.y = Math.sin(t * 0.8) * 1.6;
      cubes.forEach((c) => { const u = c.userData, a = u.a + t * u.s; c.position.set(Math.cos(a) * u.r, u.y + Math.sin(t + u.a) * 0.15, Math.sin(a) * u.r * 0.6); c.rotation.set(t * u.s * 2, t * u.s * 3, 0); });
    } };
  }

  // background stars (always on)
  {
    const N = small ? 600 : 1400, pos = new Float32Array(N * 3), col = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) { pos.set([(Math.random() - 0.5) * 120, (Math.random() - 0.5) * 80, -20 - Math.random() * 60], i * 3); const c = grad(Math.random()).lerp(new THREE.Color(1, 1, 1), 0.5); col.set([c.r, c.g, c.b], i * 3); }
    const geo = new THREE.BufferGeometry(); geo.setAttribute('position', new THREE.BufferAttribute(pos, 3)); geo.setAttribute('color', new THREE.BufferAttribute(col, 3));
    scene.add(new THREE.Points(geo, pointsMat(9)));
  }

  Object.values(scenes).forEach((s) => { s.fade = 0; fadeMats(s.g); s.g.visible = false; scene.add(s.g); });

  const composer = new EffectComposer(renderer);
  composer.setPixelRatio(dpr); composer.setSize(innerWidth, innerHeight);
  composer.addPass(new RenderPass(scene, camera));
  const bloom = new UnrealBloomPass(new THREE.Vector2(innerWidth, innerHeight), LIGHT ? 0.18 : (small ? 0.6 : 0.85), 0.4, LIGHT ? 0.9 : 0.15);
  if (!LIGHT) composer.addPass(bloom); composer.addPass(new OutputPass());

  // Which scene? The [data-scene] section nearest the viewport centre.
  let tagged = [];
  const pick = () => {
    if (!tagged.length) tagged = [...document.querySelectorAll('[data-scene]')];
    let best = 'vortex', bd = Infinity; const mid = innerHeight / 2;
    tagged.forEach((el) => { const r = el.getBoundingClientRect(); const d = r.top > mid ? r.top - mid : (r.bottom < mid ? mid - r.bottom : 0); if (d < bd) { bd = d; best = el.dataset.scene; } });
    return best === 'none' ? 'none' : (scenes[best] ? best : 'vortex');
  };
  setTimeout(() => { tagged = []; }, 1200);

  const ptr = new THREE.Vector2(), ps = new THREE.Vector2();
  addEventListener('pointermove', (e) => ptr.set(e.clientX / innerWidth * 2 - 1, -(e.clientY / innerHeight * 2 - 1)), { passive: true });
  addEventListener('resize', () => { camera.aspect = innerWidth / innerHeight; camera.updateProjectionMatrix(); renderer.setSize(innerWidth, innerHeight, false); composer.setSize(innerWidth, innerHeight); });

  let lastY = scrollY, speed = 0, running = true;
  const clock = new THREE.Clock();
  document.addEventListener('visibilitychange', () => { running = !document.hidden; if (running) { clock.getDelta(); loop(); } });

  function frame() {
    const dt = Math.min(clock.getDelta(), 0.05);
    if (!reduce) uTime.value += dt;
    const t = uTime.value;
    const v = Math.abs(scrollY - lastY) / Math.max(1, innerHeight); lastY = scrollY;
    speed += (Math.min(v, 0.08) - speed) * 0.1;
    ps.lerp(ptr, Math.min(1, dt * 2.5));
    const active = pick(), side = innerWidth < 900 ? 0 : Math.min(1, innerWidth / 1400);
    Object.entries(scenes).forEach(([name, s]) => {
      s.fade += ((name === active ? 1 : 0) - s.fade) * (reduce ? 1 : Math.min(1, dt * 2.2));
      s.g.visible = s.fade > 0.01;
      if (!s.g.visible) return;
      setFade(s.g, s.fade);
      s.g.position.set(s.x * side, s.y + (1 - s.fade) * -1.5, 0);
      s.g.scale.setScalar((small ? 0.75 : 1) * (0.75 + 0.25 * s.fade));
      s.update(t, dt, reduce ? 0 : speed, ps);
    });
    camera.position.x += (ps.x * 0.7 - camera.position.x) * Math.min(1, dt * 2);
    camera.position.y += (ps.y * 0.45 - camera.position.y) * Math.min(1, dt * 2);
    camera.lookAt(0, 0, 0);
    composer.render();
  }
  function loop() { if (!running) return; frame(); if (!reduce) requestAnimationFrame(loop); }
  if (reduce) { frame(); addEventListener('scroll', () => requestAnimationFrame(frame), { passive: true }); } else loop();
  document.documentElement.classList.add('has-3d');
}
