/* Yojo Solutions — "Clay studio" 3D stage for light templates.
   Soft, matte pastel shapes with studio lighting and real shadows.
   Six compositions cross-fade per section ([data-scene] tags):
   shield (inflated shield + check + padlock), blocks (rounded data cubes),
   orbit (rings + pebbles), knot (soft torus knot + capsules),
   stairs (iso audit steps), stack (floating rounded slabs).
   Everything is generated in code — no external models. */
import * as THREE from 'three';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

export function start(canvas, opts = {}) {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const small = innerWidth < 768;
  const sentinel = opts.palette === 'sentinel';
  const gilt = opts.palette === 'gilt';
  const BG = sentinel ? '#ffffff' : gilt ? '#fffaf2' : '#fbf6ef';
  // pastel versions of each template's colours + one deep accent
  const P = gilt
    ? ['#f28aa6', '#ffc2d1', '#e9b949', '#ffd66b', '#e85d5d', '#7a1f3d']
    : sentinel
    ? ['#4fd98c', '#9fe9bf', '#ffd23f', '#ff9a5c', '#ff6f84', '#12c96a']
    : ['#ff7cc8', '#ffb0d8', '#ff9f52', '#ffd25e', '#9feb72', '#ff2fb9'];
  const col = (i) => new THREE.Color(P[i % P.length]);

  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, powerPreference: 'high-performance' });
  const dpr = Math.min(devicePixelRatio || 1, small ? 1.25 : 1.5);
  renderer.setPixelRatio(dpr);
  renderer.setSize(innerWidth, innerHeight, false);
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 0.88;
  renderer.shadowMap.enabled = !small;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(BG);
  scene.fog = new THREE.Fog(BG, 20, 40);
  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;

  const camera = new THREE.PerspectiveCamera(35, innerWidth / innerHeight, 0.1, 100);
  camera.position.set(0, 0.6, 11);

  scene.add(new THREE.HemisphereLight('#ffffff', P[1], 0.9));
  const sun = new THREE.DirectionalLight('#ffffff', 2.2);
  sun.position.set(4, 8, 6); sun.castShadow = true;
  sun.shadow.mapSize.set(512, 512); sun.shadow.radius = 6;
  Object.assign(sun.shadow.camera, { left: -8, right: 8, top: 8, bottom: -8, near: 1, far: 30 });
  scene.add(sun);

  // soft shadow catcher floor
  const floor = new THREE.Mesh(new THREE.PlaneGeometry(60, 60), new THREE.ShadowMaterial({ color: sentinel ? '#0b3d22' : gilt ? '#5a1626' : '#5a2040', opacity: 0.12 }));
  floor.rotation.x = -Math.PI / 2; floor.position.y = -2.4; floor.receiveShadow = true;
  scene.add(floor);

  // Shared matte materials (one per colour) keep the GPU load low.
  const matCache = {};
  const clay = (c, extra = {}) => { const k = c.getHexString() + JSON.stringify(extra); return matCache[k] || (matCache[k] = new THREE.MeshStandardMaterial({ color: c, roughness: 0.5, metalness: 0, envMapIntensity: 0.9, transparent: true, ...extra })); };
  const mesh = (geo, mat) => { const m = new THREE.Mesh(geo, mat); m.castShadow = true; m.receiveShadow = true; return m; };
  const scenes = {};

  /* SHIELD — inflated shield + check, padlock, floating pebbles */
  {
    const g = new THREE.Group();
    const sh = new THREE.Shape();
    sh.moveTo(0, 1.55); sh.bezierCurveTo(0.55, 1.2, 0.95, 1.15, 1.25, 1.15); sh.lineTo(1.25, 0.1);
    sh.bezierCurveTo(1.25, -0.85, 0.55, -1.35, 0, -1.65); sh.bezierCurveTo(-0.55, -1.35, -1.25, -0.85, -1.25, 0.1);
    sh.lineTo(-1.25, 1.15); sh.bezierCurveTo(-0.95, 1.15, -0.55, 1.2, 0, 1.55);
    const sg = new THREE.ExtrudeGeometry(sh, { depth: 0.25, bevelEnabled: true, bevelThickness: 0.32, bevelSize: 0.28, bevelSegments: 12, curveSegments: 64 }); sg.center();
    const emblem = new THREE.Group();
    emblem.add(mesh(sg, clay(col(0))));
    const tick = mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3([new THREE.Vector3(-0.5, 0.0, 0.45), new THREE.Vector3(-0.12, -0.42, 0.5), new THREE.Vector3(0.62, 0.52, 0.45)], false, 'catmullrom', 0), 48, 0.13, 16), clay(col(5), { roughness: 0.35 }));
    emblem.add(tick);
    g.add(emblem);
    const lock = new THREE.Group();
    lock.add(mesh(new RoundedBoxGeometry(0.75, 0.62, 0.38, 6, 0.14), clay(col(4))));
    const sk = mesh(new THREE.TorusGeometry(0.22, 0.07, 16, 40, Math.PI), clay(col(3))); sk.position.y = 0.31; lock.add(sk);
    lock.position.set(1.9, -1.1, 0.8); g.add(lock);
    const pebbles = [];
    for (let i = 0; i < 6; i++) { const p = mesh(new THREE.SphereGeometry(0.16 + Math.random() * 0.2, 32, 32), clay(col(i + 1))); p.userData = { a: i / 6 * Math.PI * 2, r: 2.4 + Math.random() * 0.6, y: (Math.random() - 0.5) * 2 }; pebbles.push(p); g.add(p); }
    scenes.shield = { g, x: 2.6, y: 0.1, update(t, ptr) {
      emblem.rotation.y = Math.sin(t * 0.6) * 0.35 + ptr.x * 0.5; emblem.rotation.x = -ptr.y * 0.25; emblem.position.y = Math.sin(t * 1.2) * 0.12;
      lock.rotation.set(Math.sin(t) * 0.15, t * 0.6, Math.sin(t * 0.8) * 0.1); lock.position.y = -1.1 + Math.sin(t * 1.4 + 1) * 0.15;
      pebbles.forEach((p) => { const u = p.userData, a = u.a + t * 0.25; p.position.set(Math.cos(a) * u.r, u.y + Math.sin(t * 1.3 + u.a) * 0.2, Math.sin(a) * u.r * 0.5); });
    } };
  }

  /* BLOCKS — rounded data cubes that assemble and breathe */
  {
    const g = new THREE.Group(); const n = 4, step = 0.62, items = [];
    const geo = new RoundedBoxGeometry(0.52, 0.52, 0.52, 4, 0.12);
    for (let x = 0; x < n; x++) for (let y = 0; y < n; y++) for (let z = 0; z < n; z++) {
      const m = mesh(geo, clay(col(x + y + z)));
      const h = new THREE.Vector3(x - (n - 1) / 2, y - (n - 1) / 2, z - (n - 1) / 2).multiplyScalar(step);
      m.userData = { h, d: h.clone().normalize(), r: Math.random() }; items.push(m); g.add(m);
    }
    scenes.blocks = { g, x: 2.6, y: 0, update(t, ptr) {
      const ex = (Math.sin(t * 0.8) * 0.5 + 0.5) * 0.55 + Math.abs(ptr.x) * 0.25;
      items.forEach((m) => { const u = m.userData; m.position.copy(u.h).addScaledVector(u.d, ex * (0.4 + u.r)); m.rotation.set(ex * u.r, ex * u.r * 1.3, 0); });
      g.rotation.set(0.45 + ptr.y * 0.2, t * 0.3 + ptr.x * 0.4, 0);
    } };
  }

  /* ORBIT — soft rings with travelling pebbles around a core sphere */
  {
    const g = new THREE.Group();
    g.add(mesh(new THREE.SphereGeometry(0.85, 64, 64), clay(col(2))));
    const rings = [];
    [[1.7, 0.5, 0.2], [2.25, -0.4, 0.9], [2.8, 1.1, -0.3]].forEach(([r, rx, rz], i) => {
      const ring = new THREE.Group(); ring.rotation.set(rx, 0, rz);
      ring.add(mesh(new THREE.TorusGeometry(r, 0.07, 20, 160), clay(col(i + 3))));
      const bead = mesh(new THREE.SphereGeometry(0.2, 32, 32), clay(col(i))); ring.add(bead);
      rings.push({ ring, bead, r, s: 0.4 + i * 0.15 }); g.add(ring);
    });
    scenes.orbit = { g, x: -2.6, y: 0, update(t, ptr) {
      rings.forEach((o, i) => { const a = t * o.s + i; o.bead.position.set(Math.cos(a) * o.r, Math.sin(a) * o.r, 0); o.ring.rotation.z += 0.002 * (i % 2 ? -1 : 1); });
      g.rotation.set(ptr.y * 0.3, ptr.x * 0.5 + t * 0.1, 0);
    } };
  }

  /* KNOT — soft torus knot with orbiting capsules */
  {
    const g = new THREE.Group();
    const knot = mesh(new THREE.TorusKnotGeometry(1.05, 0.34, 240, 40, 2, 3), clay(col(0)));
    g.add(knot);
    const caps = [];
    for (let i = 0; i < 5; i++) { const c = mesh(new THREE.CapsuleGeometry(0.14, 0.42, 8, 16), clay(col(i + 2))); c.userData = { a: i / 5 * Math.PI * 2 }; caps.push(c); g.add(c); }
    scenes.knot = { g, x: -2.6, y: 0, update(t, ptr) {
      knot.rotation.set(t * 0.25 + ptr.y * 0.3, t * 0.35 + ptr.x * 0.5, 0);
      caps.forEach((c) => { const a = c.userData.a + t * 0.4; c.position.set(Math.cos(a) * 2.3, Math.sin(a * 2) * 0.6, Math.sin(a) * 1.2); c.rotation.set(a, a * 0.5, 0); });
    } };
  }

  /* STAIRS — iso audit steps (each step = one stage of the methodology) */
  {
    const g = new THREE.Group(); const steps = [];
    for (let i = 0; i < 7; i++) {
      const s = mesh(new RoundedBoxGeometry(2.2, 0.32, 0.9, 4, 0.08), clay(col(i)));
      s.userData = { i }; steps.push(s); g.add(s);
    }
    const ball = mesh(new THREE.SphereGeometry(0.22, 32, 32), clay(col(5), { roughness: 0.3 })); g.add(ball);
    g.rotation.set(0.35, -0.7, 0);
    scenes.stairs = { g, x: 2.5, y: -0.4, update(t, ptr) {
      steps.forEach((s, i) => s.position.set(0, i * 0.36 - 1.1 + Math.sin(t * 1.5 + i * 0.5) * 0.03, -i * 0.55 + 1.6));
      const k = (t * 0.6) % 7, i0 = Math.floor(k), f = k - i0, hop = Math.sin(f * Math.PI) * 0.5;
      ball.position.set(0, (i0 + f) * 0.36 - 1.1 + 0.4 + hop, -(i0 + f) * 0.55 + 1.6);
      g.rotation.y = -0.7 + ptr.x * 0.4; g.rotation.x = 0.35 + ptr.y * 0.15;
    } };
  }

  /* STACK — floating rounded slabs (documents / evidence) */
  {
    const g = new THREE.Group(); const slabs = [];
    for (let i = 0; i < 6; i++) { const s = mesh(new RoundedBoxGeometry(1.8, 0.16, 2.3, 4, 0.07), clay(col(i))); slabs.push(s); g.add(s); }
    g.rotation.set(0.5, 0.6, 0.1);
    scenes.stack = { g, x: -2.5, y: 0, update(t, ptr) {
      slabs.forEach((s, i) => { const spread = 0.32 + (Math.sin(t * 0.9) * 0.5 + 0.5) * 0.25; s.position.set(Math.sin(t * 0.7 + i) * 0.12, (i - 2.5) * spread, 0); s.rotation.y = Math.sin(t * 0.5 + i * 0.4) * 0.25; });
      g.rotation.y = 0.6 + ptr.x * 0.5 + t * 0.08;
    } };
  }

  // map the shared section tags onto clay compositions
  const MAP = { shield: 'shield', voxel: 'blocks', vortex: 'orbit', galaxy: 'orbit', rift: 'knot', wave: 'stairs', stack: 'stack' };
  const order = ['shield', 'blocks', 'stack', 'knot', 'stairs', 'orbit'];

  Object.values(scenes).forEach((s) => { s.fade = 0; s.g.visible = false; s.g.traverse((o) => { if (o.material) o.material.userData.base = 1; }); scene.add(s.g); });

  let tagged = [];
  const pick = () => {
    if (!tagged.length) tagged = [...document.querySelectorAll('[data-scene]')];
    if (!tagged.length) { // pages without tags: rotate through compositions by scroll depth
      const p = scrollY / Math.max(1, document.documentElement.scrollHeight - innerHeight);
      return order[Math.min(order.length - 1, Math.floor(p * order.length))];
    }
    let best = 'shield', bd = Infinity; const mid = innerHeight / 2;
    tagged.forEach((el) => { const r = el.getBoundingClientRect(); const d = r.top > mid ? r.top - mid : (r.bottom < mid ? mid - r.bottom : 0); if (d < bd) { bd = d; best = el.dataset.scene; } });
    return MAP[best] || 'shield';
  };
  setTimeout(() => { tagged = []; }, 1200);

  const ptr = new THREE.Vector2(), ps = new THREE.Vector2();
  addEventListener('pointermove', (e) => ptr.set(e.clientX / innerWidth * 2 - 1, -(e.clientY / innerHeight * 2 - 1)), { passive: true });
  addEventListener('resize', () => { camera.aspect = innerWidth / innerHeight; camera.updateProjectionMatrix(); renderer.setSize(innerWidth, innerHeight, false); });

  let t = 0, running = true;
  const clock = new THREE.Clock();
  document.addEventListener('visibilitychange', () => { running = !document.hidden; if (running) { clock.getDelta(); loop(); } });

  function frame() {
    const dt = Math.min(clock.getDelta(), 0.05);
    if (!reduce) t += dt;
    ps.lerp(ptr, Math.min(1, dt * 2.5));
    const active = pick(), side = innerWidth < 900 ? 0 : Math.min(1, innerWidth / 1400);
    Object.entries(scenes).forEach(([name, s]) => {
      s.fade += ((name === active ? 1 : 0) - s.fade) * (reduce ? 1 : Math.min(1, dt * 2.4));
      s.g.visible = s.fade > 0.01;
      if (!s.g.visible) return;
      s.g.position.set(s.x * side, s.y + (1 - s.fade) * -1.2, small ? -2 : 0);
      s.g.scale.setScalar((small ? 0.7 : 1) * (0.15 + 0.85 * s.fade * s.fade * (3 - 2 * s.fade)));
      s.update(t, ps);
    });
    camera.position.x += (ps.x * 0.6 - camera.position.x) * Math.min(1, dt * 2);
    camera.position.y += (0.6 + ps.y * 0.35 - camera.position.y) * Math.min(1, dt * 2);
    camera.lookAt(0, 0, 0);
    renderer.render(scene, camera);
  }
  function loop() { if (!running) return; frame(); if (!reduce) requestAnimationFrame(loop); }
  if (reduce) { frame(); addEventListener('scroll', () => requestAnimationFrame(frame), { passive: true }); } else loop();
  document.documentElement.classList.add('has-3d');
}
