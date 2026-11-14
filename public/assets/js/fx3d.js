/* Yojo Solutions — fixed WebGL stage behind every page.
   Procedural only (no external models): fluid gradient backdrop, morphing core,
   orbit rings with "security nodes", particles. Shape + colours follow the
   active template (<html data-template>). Scroll + pointer drive the camera. */
import * as THREE from 'three';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';

const canvas = document.querySelector('.fx3d');
const root = document.documentElement;
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
const isHome = document.body.classList.contains('is-home');
const small = innerWidth < 768;
const template = root.dataset.template || 'emerald';

function fail() { canvas && canvas.remove(); const d = document.createElement('div'); d.className = 'fx3d-fallback'; document.body.prepend(d); }


function cssColor(name) {
  const v = getComputedStyle(root).getPropertyValue(name).trim().split(/\s+/).map(Number);
  return new THREE.Color(v[0] / 255, v[1] / 255, v[2] / 255);
}

const NOISE = /* glsl */`
vec4 permute(vec4 x){return mod(((x*34.0)+1.0)*x,289.0);}
vec4 taylorInvSqrt(vec4 r){return 1.79284291400159-0.85373472095314*r;}
float snoise(vec3 v){
  const vec2 C=vec2(1.0/6.0,1.0/3.0); const vec4 D=vec4(0.0,0.5,1.0,2.0);
  vec3 i=floor(v+dot(v,C.yyy)); vec3 x0=v-i+dot(i,C.xxx);
  vec3 g=step(x0.yzx,x0.xyz); vec3 l=1.0-g; vec3 i1=min(g.xyz,l.zxy); vec3 i2=max(g.xyz,l.zxy);
  vec3 x1=x0-i1+C.xxx; vec3 x2=x0-i2+2.0*C.xxx; vec3 x3=x0-1.0+3.0*C.xxx;
  i=mod(i,289.0);
  vec4 p=permute(permute(permute(i.z+vec4(0.0,i1.z,i2.z,1.0))+i.y+vec4(0.0,i1.y,i2.y,1.0))+i.x+vec4(0.0,i1.x,i2.x,1.0));
  float n_=1.0/7.0; vec3 ns=n_*D.wyz-D.xzx;
  vec4 j=p-49.0*floor(p*ns.z*ns.z); vec4 x_=floor(j*ns.z); vec4 y_=floor(j-7.0*x_);
  vec4 x=x_*ns.x+ns.yyyy; vec4 y=y_*ns.x+ns.yyyy; vec4 h=1.0-abs(x)-abs(y);
  vec4 b0=vec4(x.xy,y.xy); vec4 b1=vec4(x.zw,y.zw);
  vec4 s0=floor(b0)*2.0+1.0; vec4 s1=floor(b1)*2.0+1.0; vec4 sh=-step(h,vec4(0.0));
  vec4 a0=b0.xzyw+s0.xzyw*sh.xxyy; vec4 a1=b1.xzyw+s1.xzyw*sh.zzww;
  vec3 p0=vec3(a0.xy,h.x); vec3 p1=vec3(a0.zw,h.y); vec3 p2=vec3(a1.xy,h.z); vec3 p3=vec3(a1.zw,h.w);
  vec4 norm=taylorInvSqrt(vec4(dot(p0,p0),dot(p1,p1),dot(p2,p2),dot(p3,p3)));
  p0*=norm.x; p1*=norm.y; p2*=norm.z; p3*=norm.w;
  vec4 m=max(0.5-vec4(dot(x0,x0),dot(x1,x1),dot(x2,x2),dot(x3,x3)),0.0); m=m*m;
  return 42.0*dot(m*m,vec4(dot(p0,x0),dot(p1,x1),dot(p2,x2),dot(p3,x3)));
}`;

function init() {
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: !small, alpha: false, powerPreference: 'high-performance' });
  const dpr = Math.min(devicePixelRatio || 1, small ? 1.5 : 1.75);
  renderer.setPixelRatio(dpr);
  renderer.setSize(innerWidth, innerHeight, false);
  renderer.outputColorSpace = THREE.SRGBColorSpace;

  const C1 = cssColor('--a1'), C2 = cssColor('--a2'), C3 = cssColor('--a3'), BG = cssColor('--deep-rgb');
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(40, innerWidth / innerHeight, 0.1, 200);
  camera.position.set(0, 0, 9);

  const uTime = { value: 0 };

  /* ---- fluid gradient backdrop (full-screen, behind everything) ---- */
  const bg = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), new THREE.ShaderMaterial({
    depthWrite: false, depthTest: false,
    uniforms: { uTime, uA: { value: C1 }, uB: { value: C2 }, uC: { value: C3 }, uBg: { value: BG }, uScroll: { value: 0 } },
    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = vec4(position.xy, 0.0, 1.0); }',
    fragmentShader: /* glsl */`
      uniform float uTime; uniform float uScroll; uniform vec3 uA; uniform vec3 uB; uniform vec3 uC; uniform vec3 uBg; varying vec2 vUv;
      ${NOISE}
      void main(){
        vec2 uv = vUv * 2.0 - 1.0;
        float t = uTime * 0.05;
        float n1 = snoise(vec3(uv * 0.9 + vec2(t, -t * 0.6), t + uScroll * 0.8));
        float n2 = snoise(vec3(uv * 1.6 - vec2(t * 0.7, t), t * 1.3 + 4.0));
        float flow = smoothstep(-0.2, 1.0, n1 * 0.65 + n2 * 0.35);
        vec3 col = uBg * 0.55;
        float edge = smoothstep(0.2, 1.35, length(uv * vec2(0.85, 1.0)));
        col += uA * flow * 0.18 * (0.35 + edge);
        col += uB * smoothstep(0.35, 1.0, n2) * 0.08 * edge;
        col += uC * smoothstep(0.5, 1.0, -n1) * 0.07;
        col *= 1.0 - 0.35 * length(uv);
        gl_FragColor = vec4(col, 1.0);
      }`,
  }));
  bg.frustumCulled = false; bg.renderOrder = -10;
  scene.add(bg);

  const world = new THREE.Group();
  scene.add(world);

  /* ---- morphing core ---- */
  const coreUniforms = { uTime, uA: { value: C1 }, uB: { value: C2 }, uC: { value: C3 }, uAmp: { value: template === 'ember' ? 0.12 : 0.28 }, uPointer: { value: new THREE.Vector2() }, uSize: { value: small ? 2.2 : 2.8 } };
  const coreVert = /* glsl */`
    uniform float uTime; uniform float uAmp; uniform vec2 uPointer; uniform float uSize;
    varying vec3 vN; varying vec3 vView; varying float vNoise;
    ${NOISE}
    void main(){
      float t = uTime * 0.28;
      float n = snoise(normal * 1.4 + vec3(t, t * 0.8, -t)) * 0.6 + snoise(normal * 3.2 - vec3(t * 1.4)) * 0.4;
      n += dot(normal.xy, uPointer) * 0.25;
      vNoise = n;
      vec3 p = position + normal * n * uAmp;
      vec4 mv = modelViewMatrix * vec4(p, 1.0);
      vN = normalize(normalMatrix * normal); vView = -mv.xyz;
      gl_PointSize = uSize * (8.0 / -mv.z);
      gl_Position = projectionMatrix * mv;
    }`;
  const coreFrag = /* glsl */`
    uniform vec3 uA; uniform vec3 uB; uniform vec3 uC; uniform float uTime;
    varying vec3 vN; varying vec3 vView; varying float vNoise;
    void main(){
      vec3 v = normalize(vView);
      float fres = pow(1.0 - max(dot(normalize(vN), v), 0.0), 2.2);
      float k = 0.5 + 0.5 * sin(vNoise * 3.0 + uTime * 0.4);
      vec3 base = mix(mix(uA, uB, k), uC, smoothstep(0.35, 1.0, vNoise));
      vec3 col = base * (0.04 + 0.32 * smoothstep(-0.2, 1.0, vNoise)) + mix(uA, uB, fres) * fres * 1.15;
      gl_FragColor = vec4(col, 1.0);
    }`;
  let core, shell;
  const extras = [];
  if (template === 'sentinel') {
    // Cyber-audit emblem: extruded shield + check mark, padlock, scanner ring, audit data blocks, grid floor.
    core = new THREE.Group();
    const sh = new THREE.Shape();
    sh.moveTo(0, 1.55); sh.bezierCurveTo(0.55, 1.2, 0.95, 1.15, 1.25, 1.15); sh.lineTo(1.25, 0.1);
    sh.bezierCurveTo(1.25, -0.85, 0.55, -1.35, 0, -1.65); sh.bezierCurveTo(-0.55, -1.35, -1.25, -0.85, -1.25, 0.1);
    sh.lineTo(-1.25, 1.15); sh.bezierCurveTo(-0.95, 1.15, -0.55, 1.2, 0, 1.55);
    const sg = new THREE.ExtrudeGeometry(sh, { depth: 0.32, bevelEnabled: true, bevelThickness: 0.08, bevelSize: 0.06, bevelSegments: 4, curveSegments: 48 });
    sg.center();
    const shield = new THREE.Mesh(sg, new THREE.ShaderMaterial({ uniforms: { ...coreUniforms, uAmp: { value: 0.0 } }, vertexShader: coreVert, fragmentShader: coreFrag }));
    const edges = new THREE.LineSegments(new THREE.EdgesGeometry(sg, 25), new THREE.LineBasicMaterial({ color: C1, transparent: true, opacity: 0.9 }));
    const tick = new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3([new THREE.Vector3(-0.5, 0.05, 0.3), new THREE.Vector3(-0.12, -0.38, 0.3), new THREE.Vector3(0.62, 0.55, 0.3)], false, 'catmullrom', 0), 40, 0.09, 12), new THREE.MeshBasicMaterial({ color: C2 }));
    core.add(shield, edges, tick);
    // padlock
    const lock = new THREE.Group();
    const body = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.42, 0.18), new THREE.MeshBasicMaterial({ color: C3 }));
    const shackle = new THREE.Mesh(new THREE.TorusGeometry(0.16, 0.045, 10, 30, Math.PI), new THREE.MeshBasicMaterial({ color: C3 }));
    shackle.position.y = 0.21;
    lock.add(body, shackle, new THREE.LineSegments(new THREE.EdgesGeometry(body.geometry), new THREE.LineBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.6 })));
    world.add(lock);
    extras.push((t) => { lock.position.set(Math.cos(t * 0.45) * 2.6, 0.9 + Math.sin(t * 0.9) * 0.25, Math.sin(t * 0.45) * 1.4); lock.rotation.y = t * 1.2; });
    // scanner ring sweeping the shield
    const scan = new THREE.Mesh(new THREE.TorusGeometry(1.75, 0.012, 8, 120), new THREE.MeshBasicMaterial({ color: C2, transparent: true, opacity: 0.9 }));
    const scanDisc = new THREE.Mesh(new THREE.CircleGeometry(1.75, 64), new THREE.MeshBasicMaterial({ color: C2, transparent: true, opacity: 0.06, side: THREE.DoubleSide, blending: THREE.AdditiveBlending, depthWrite: false }));
    scan.rotation.x = scanDisc.rotation.x = Math.PI / 2;
    world.add(scan, scanDisc);
    extras.push((t) => { const y = Math.sin(t * 0.8) * 1.6; scan.position.y = scanDisc.position.y = y; });
    // audit data blocks
    const cols = [C1, C2, C3];
    for (let i = 0; i < 14; i++) {
      const sz = 0.12 + Math.random() * 0.16;
      const cube = new THREE.LineSegments(new THREE.EdgesGeometry(new THREE.BoxGeometry(sz, sz, sz)), new THREE.LineBasicMaterial({ color: cols[i % 3] }));
      const r = 2.3 + Math.random() * 1.6, a0 = Math.random() * Math.PI * 2, yy = (Math.random() - 0.5) * 2.6, sp = 0.15 + Math.random() * 0.25;
      world.add(cube);
      extras.push((t) => { const a = a0 + t * sp; cube.position.set(Math.cos(a) * r, yy + Math.sin(t + a0) * 0.15, Math.sin(a) * r * 0.6); cube.rotation.set(t * sp * 2, t * sp * 3, 0); });
    }
    // grid floor
    const grid = new THREE.GridHelper(16, 32, C1, C1);
    grid.material.transparent = true; grid.material.opacity = 0.12; grid.position.y = -2.6;
    world.add(grid);
  } else if (template === 'nebula') {
    const N = small ? 6000 : 12000, pos = new Float32Array(N * 3), nor = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) {
      const y = 1 - (i / (N - 1)) * 2, r = Math.sqrt(1 - y * y), th = i * 2.399963;
      const x = Math.cos(th) * r, z = Math.sin(th) * r;
      nor.set([x, y, z], i * 3); pos.set([x * 1.55, y * 1.55, z * 1.55], i * 3);
    }
    const g = new THREE.BufferGeometry();
    g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    g.setAttribute('normal', new THREE.BufferAttribute(nor, 3));
    core = new THREE.Points(g, new THREE.ShaderMaterial({
      uniforms: coreUniforms, vertexShader: coreVert, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
      fragmentShader: /* glsl */`uniform vec3 uA; uniform vec3 uB; uniform vec3 uC; varying float vNoise;
        void main(){ float d = length(gl_PointCoord - 0.5); if (d > 0.5) discard;
          vec3 c = mix(uA, uB, smoothstep(-0.5, 0.8, vNoise)); c = mix(c, uC, smoothstep(0.6, 1.0, vNoise));
          gl_FragColor = vec4(c * 0.8, smoothstep(0.5, 0.0, d) * 0.7); }`,
    }));
    const inner = new THREE.Mesh(new THREE.IcosahedronGeometry(1.2, 30), new THREE.ShaderMaterial({ uniforms: { ...coreUniforms, uAmp: { value: 0.18 } }, vertexShader: coreVert, fragmentShader: coreFrag }));
    core.add(inner);
  } else {
    const geo = template === 'ember' ? new THREE.TorusKnotGeometry(1.05, 0.36, small ? 220 : 360, small ? 32 : 56, 2, 3) : new THREE.IcosahedronGeometry(1.45, small ? 32 : 60);
    core = new THREE.Mesh(geo, new THREE.ShaderMaterial({ uniforms: coreUniforms, vertexShader: coreVert, fragmentShader: coreFrag }));
  }
  shell = new THREE.Mesh(template === 'sentinel' ? new THREE.IcosahedronGeometry(2.25, 1) : new THREE.IcosahedronGeometry(2.05, 2), new THREE.MeshBasicMaterial({ color: C1, wireframe: true, transparent: true, opacity: 0.09, blending: THREE.AdditiveBlending, depthWrite: false }));
  const coreGroup = new THREE.Group();
  coreGroup.add(core, shell);
  world.add(coreGroup);

  /* glow halo billboard */
  const halo = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), new THREE.ShaderMaterial({
    transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
    uniforms: { uA: { value: C1 }, uB: { value: C3 } },
    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }',
    fragmentShader: 'uniform vec3 uA; uniform vec3 uB; varying vec2 vUv; void main(){ float d = length(vUv - 0.5) * 2.0; float a = pow(clamp(1.0 - d, 0.0, 1.0), 2.4); gl_FragColor = vec4(mix(uA, uB, d) * a * 0.32, a); }',
  }));
  halo.scale.setScalar(4.6);
  world.add(halo);

  /* ---- orbit rings with travelling nodes ---- */
  const rings = [];
  const ringCols = [C1, C2, C3];
  [[2.7, 0.35, 0.2], [3.2, -0.6, 1.1], [3.75, 1.15, -0.4]].forEach(([r, rx, rz], i) => {
    const g = new THREE.Group(); g.rotation.set(rx, 0, rz);
    const curve = new THREE.EllipseCurve(0, 0, r, r, 0, Math.PI * 2);
    const pts = curve.getPoints(200).map((p) => new THREE.Vector3(p.x, p.y, 0));
    const line = new THREE.LineLoop(new THREE.BufferGeometry().setFromPoints(pts), new THREE.LineBasicMaterial({ color: ringCols[i], transparent: true, opacity: 0.28, blending: THREE.AdditiveBlending, depthWrite: false }));
    g.add(line);
    const nodes = [];
    for (let k = 0; k < 3; k++) {
      const n = new THREE.Mesh(new THREE.SphereGeometry(0.055, 12, 12), new THREE.MeshBasicMaterial({ color: ringCols[(i + k) % 3] }));
      n.userData = { r, a: (k / 3) * Math.PI * 2 + i, s: 0.25 + i * 0.08 };
      g.add(n); nodes.push(n);
    }
    rings.push({ g, nodes, spin: (i % 2 ? -1 : 1) * (0.05 + i * 0.02) });
    world.add(g);
  });

  /* ---- particles: starfield + drifting motes ---- */
  function points(count, spread, size, colorA, colorB, sphere) {
    const pos = new Float32Array(count * 3), seed = new Float32Array(count), mix = new Float32Array(count);
    for (let i = 0; i < count; i++) {
      if (sphere) { const u = Math.random() * 2 - 1, th = Math.random() * Math.PI * 2, r = spread * (0.6 + Math.random() * 0.4), s = Math.sqrt(1 - u * u); pos.set([Math.cos(th) * s * r, u * r, Math.sin(th) * s * r], i * 3); }
      else pos.set([(Math.random() - 0.5) * spread * 1.6, (Math.random() - 0.5) * spread, (Math.random() - 0.5) * spread * 0.6], i * 3);
      seed[i] = Math.random() * 100; mix[i] = Math.random();
    }
    const g = new THREE.BufferGeometry();
    g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    g.setAttribute('seed', new THREE.BufferAttribute(seed, 1));
    g.setAttribute('mixv', new THREE.BufferAttribute(mix, 1));
    const p = new THREE.Points(g, new THREE.ShaderMaterial({
      transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
      uniforms: { uTime, uSize: { value: size }, uA: { value: colorA }, uB: { value: colorB }, uRes: { value: innerHeight } },
      vertexShader: /* glsl */`attribute float seed; attribute float mixv; uniform float uTime; uniform float uSize; uniform float uRes; varying float vA; varying float vM;
        void main(){ vec3 p = position; p.y += sin(uTime * 0.2 + seed) * 0.25; p.x += cos(uTime * 0.15 + seed) * 0.25;
          vec4 mv = modelViewMatrix * vec4(p, 1.0); vA = 0.45 + 0.55 * sin(uTime * 1.3 + seed); vM = mixv;
          gl_PointSize = max(uSize * uRes / 900.0 * (10.0 / -mv.z), 1.0); gl_Position = projectionMatrix * mv; }`,
      fragmentShader: 'uniform vec3 uA; uniform vec3 uB; varying float vA; varying float vM; void main(){ float d = length(gl_PointCoord - 0.5); if (d > 0.5) discard; gl_FragColor = vec4(mix(uA, uB, vM), smoothstep(0.5, 0.0, d) * vA * 0.8); }',
    }));
    p.frustumCulled = false;
    return p;
  }
  const stars = points(small ? 700 : 1500, 60, 1.3, new THREE.Color(1, 1, 1), C1, true);
  const motes = points(small ? 140 : 320, 18, 5.5, C1, C3, false);
  scene.add(stars, motes);

  /* ---- post-processing ---- */
  let composer = null;
  const bloom = new UnrealBloomPass(new THREE.Vector2(innerWidth, innerHeight), small ? 0.45 : 0.6, 0.55, 0.32);
  try {
    composer = new EffectComposer(renderer);
    composer.setPixelRatio(dpr);
    composer.setSize(innerWidth, innerHeight);
    composer.addPass(new RenderPass(scene, camera));
    composer.addPass(bloom);
    composer.addPass(new OutputPass());
  } catch (e) { composer = null; }

  /* ---- scroll choreography ---- */
  // Home: big core on the right of the hero, then swings left/right as sections pass.
  const STOPS = isHome
    ? { x: [[0, 3.05], [0.18, -2.6], [0.42, 2.8], [0.68, -2.4], [1, 0]], y: [[0, 0.1], [0.18, 0.2], [0.42, -0.1], [0.68, 0.2], [1, 0.3]], s: [[0, 1.0], [0.18, 0.72], [0.42, 0.68], [0.68, 0.7], [1, 0.95]] }
    : { x: [[0, 3.1], [0.5, -3.2], [1, 2.6]], y: [[0, 0.9], [0.5, 0.1], [1, 0.4]], s: [[0, 0.62], [0.5, 0.55], [1, 0.62]] };
  const sample = (stops, p) => {
    if (p <= stops[0][0]) return stops[0][1];
    for (let i = 1; i < stops.length; i++) {
      if (p <= stops[i][0]) { const [p0, v0] = stops[i - 1], [p1, v1] = stops[i]; let t = (p - p0) / (p1 - p0); t = t * t * (3 - 2 * t); return v0 + (v1 - v0) * t; }
    }
    return stops[stops.length - 1][1];
  };

  let pT = 0, p = 0, cx = sample(STOPS.x, 0), cy = sample(STOPS.y, 0) - (reduce ? 0 : 5), cs = sample(STOPS.s, 0);
  const pointer = new THREE.Vector2(), pSmooth = new THREE.Vector2();
  addEventListener('pointermove', (e) => { pointer.set(e.clientX / innerWidth * 2 - 1, -(e.clientY / innerHeight * 2 - 1)); }, { passive: true });

  function resize() {
    const w = innerWidth, h = innerHeight;
    camera.aspect = w / h; camera.updateProjectionMatrix();
    renderer.setSize(w, h, false);
    if (composer) { composer.setSize(w, h); bloom.resolution.set(w, h); }
    stars.material.uniforms.uRes.value = h; motes.material.uniforms.uRes.value = h;
  }
  addEventListener('resize', resize);

  const clock = new THREE.Clock();
  let running = true;
  document.addEventListener('visibilitychange', () => { running = !document.hidden; if (running) { clock.getDelta(); loop(); } });

  function frame() {
    const dt = Math.min(clock.getDelta(), 0.05);
    uTime.value += reduce ? 0 : dt;
    const max = Math.max(1, document.documentElement.scrollHeight - innerHeight);
    pT = Math.min(1, Math.max(0, scrollY / max));
    p += (pT - p) * Math.min(1, dt * 4);
    const side = Math.min(1, Math.max(0.35, innerWidth / 1300)) * (innerWidth < 768 ? 0.55 : 1);
    const k = reduce ? 1 : Math.min(1, dt * 3);
    cx += (sample(STOPS.x, p) * side - cx) * k;
    cy += (sample(STOPS.y, p) + (innerWidth < 768 && p < 0.1 ? 1.4 : 0) - cy) * k;
    cs += (sample(STOPS.s, p) * (innerWidth < 768 ? 0.8 : 1) - cs) * k;
    world.position.set(cx, cy, 0);
    world.scale.setScalar(cs);

    pSmooth.lerp(pointer, Math.min(1, dt * 2.5));
    coreUniforms.uPointer.value.copy(pSmooth);
    bg.material.uniforms.uScroll.value = p;
    coreGroup.rotation.y = template === 'sentinel' ? Math.sin(uTime.value * 0.4) * 0.6 + p * Math.PI * 2 + pSmooth.x * 0.5 : uTime.value * 0.12 + p * Math.PI * 1.6 + pSmooth.x * 0.4;
    coreGroup.rotation.x = (template === 'sentinel' ? 0 : 0.25) + pSmooth.y * -0.3;
    shell.rotation.y = -uTime.value * 0.05; shell.rotation.z = uTime.value * 0.03;
    halo.quaternion.copy(camera.quaternion);
    extras.forEach((f) => f(uTime.value));
    rings.forEach((r) => {
      r.g.rotation.z += dt * r.spin * (reduce ? 0 : 1);
      r.nodes.forEach((n) => { n.userData.a += dt * n.userData.s * (reduce ? 0 : 1); n.position.set(Math.cos(n.userData.a) * n.userData.r, Math.sin(n.userData.a) * n.userData.r, 0); });
    });
    stars.rotation.y = uTime.value * 0.004 + p * 0.6;
    motes.position.y = p * 3;
    camera.position.x += (pSmooth.x * 0.6 - camera.position.x) * Math.min(1, dt * 2);
    camera.position.y += (pSmooth.y * 0.4 - camera.position.y) * Math.min(1, dt * 2);
    camera.lookAt(0, 0, 0);

    if (composer) composer.render(); else renderer.render(scene, camera);
  }
  function loop() { if (!running) return; frame(); if (!reduce) requestAnimationFrame(loop); }
  if (reduce) { frame(); addEventListener('scroll', () => requestAnimationFrame(frame), { passive: true }); }
  else loop();
  root.classList.add('has-3d');
}

const sceneStyle = root.dataset.sceneStyle || '';
if (canvas && sceneStyle === 'off') {
  fail();
} else if (canvas && sceneStyle === 'clay') {
  import('./fx3d-clay.js').then((m) => m.start(canvas, { light: root.dataset.tone === 'light', palette: template })).catch((e) => { console.warn('3D scene disabled:', e); fail(); });
} else if (canvas && (template === 'prism' || root.dataset.tone === 'light' || sceneStyle === 'neon' && root.dataset.layout)) {
  import('./fx3d-prism.js').then((m) => m.start(canvas, { light: root.dataset.tone === 'light', palette: template })).catch((e) => { console.warn('3D scene disabled:', e); fail(); });
} else if (canvas) {
  try { init(); } catch (e) { console.warn('3D scene disabled:', e); fail(); }
}
