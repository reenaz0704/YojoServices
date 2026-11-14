/* Yojo Solutions — hero side models.
   One crisp 3D model per hero slide (chosen by the slide's icon). When the
   slide changes, the model flips edge-on and turns into the next one. */
import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const hs = document.querySelector('[data-hero-slider]');
const canvas = hs && hs.querySelector('.hs3d');
if (canvas) init().catch(() => canvas.remove());

async function init() {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.05;
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  const scene = new THREE.Scene();
  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.03).texture;
  const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 50);
  camera.position.set(0, 0.9, 10.5); camera.lookAt(0, -0.3, 0);
  const key = new THREE.DirectionalLight('#ffffff', 1.6); key.position.set(4, 6, 5); scene.add(key);
  scene.add(new THREE.HemisphereLight('#ffffff', '#f3d6e4', 0.55));
  const rim = new THREE.DirectionalLight('#ff4fc4', 1.4); rim.position.set(-5, 2, -4); scene.add(rim);

  const C = { mag: '#ff2fb9', ora: '#ff7a1a', yel: '#ffd21f', lime: '#9be22d', ink: '#2a0f1c', white: '#fff7f2' };
  const mat = (color, o = {}) => new THREE.MeshPhysicalMaterial({ color, roughness: 0.22, metalness: 0.25, clearcoat: 1, clearcoatRoughness: 0.08, ...o });
  // Professional finish: graphite metal bodies, brushed silver, frosted glass, thin glowing accents
  const glow = (c, k = 1.2) => new THREE.MeshStandardMaterial({ color: c, emissive: c, emissiveIntensity: k, roughness: 0.4, metalness: 0 });
  const M = {
    mag: mat('#2a1520', { metalness: 0.85, roughness: 0.3, clearcoat: 1, clearcoatRoughness: 0.12 }),
    white: mat('#d9d4d8', { metalness: 1, roughness: 0.28 }),
    lime: glow('#ff2fb9', 1.3),
    ora: new THREE.MeshPhysicalMaterial({ color: '#f6e6ee', roughness: 0.12, metalness: 0, transmission: 0.92, thickness: 0.8, ior: 1.45, clearcoat: 1 }),
    yel: mat('#c9a24a', { metalness: 1, roughness: 0.22 }),
    ink: mat('#140810', { metalness: 0.7, roughness: 0.35 }),
    gold: mat('#c9a24a', { metalness: 1, roughness: 0.2 }),
  };
  const edgeMat = new THREE.LineBasicMaterial({ color: '#ff7ac8', transparent: true, opacity: 0.32 });
  // mesh + crisp outline so every model reads sharp on the light background
  const mesh = (geo, m, parent, edges = true) => {
    const o = new THREE.Mesh(geo, m); parent.add(o);
    if (edges) o.add(new THREE.LineSegments(new THREE.EdgesGeometry(geo, 28), edgeMat));
    return o;
  };
  const extrude = (shape, depth, bevel = 0.06) => { const g = new THREE.ExtrudeGeometry(shape, { depth, bevelEnabled: true, bevelThickness: bevel, bevelSize: bevel, bevelSegments: 4, curveSegments: 24 }); g.center(); return g; };
  const rrect = (w, h, r) => { const s = new THREE.Shape(); const x = -w / 2, y = -h / 2; s.moveTo(x + r, y); s.lineTo(x + w - r, y); s.quadraticCurveTo(x + w, y, x + w, y + r); s.lineTo(x + w, y + h - r); s.quadraticCurveTo(x + w, y + h, x + w - r, y + h); s.lineTo(x + r, y + h); s.quadraticCurveTo(x, y + h, x, y + h - r); s.lineTo(x, y + r); s.quadraticCurveTo(x, y, x + r, y); return s; };
  const check = (w = 1) => { const s = new THREE.Shape(); s.moveTo(-0.5 * w, 0.02 * w); s.lineTo(-0.32 * w, 0.2 * w); s.lineTo(-0.12 * w, 0); s.lineTo(0.38 * w, 0.5 * w); s.lineTo(0.56 * w, 0.32 * w); s.lineTo(-0.12 * w, -0.36 * w); s.closePath(); return s; };

  const models = {};
  /* SHIELD — consulting / GRC */
  {
    const g = new THREE.Group();
    const s = new THREE.Shape(); s.moveTo(0, 1.55); s.bezierCurveTo(0.6, 1.25, 1.05, 1.2, 1.3, 1.2); s.bezierCurveTo(1.3, 0.1, 1.05, -0.95, 0, -1.6); s.bezierCurveTo(-1.05, -0.95, -1.3, 0.1, -1.3, 1.2); s.bezierCurveTo(-1.05, 1.2, -0.6, 1.25, 0, 1.55);
    // dark armoured shield with a glowing rim and a lit padlock (matches the reference artwork, in theme colours)
    mesh(extrude(s, 0.35, 0.1), M.mag, g);
    const rimPts = s.getSpacedPoints(160).map((p) => new THREE.Vector3(p.x * 1.02, p.y * 1.02, 0.3));
    const rimLine = new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3(rimPts, true), 240, 0.035, 8, true), glow('#ff2fb9', 1.6)); g.add(rimLine);
    const inner = mesh(extrude(s, 0.08, 0.03), new THREE.MeshPhysicalMaterial({ color: '#1b0d15', metalness: 0.6, roughness: 0.25, clearcoat: 1 }), g); inner.scale.setScalar(0.8); inner.position.z = 0.26;
    const innerRim = new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3(s.getSpacedPoints(160).map((p) => new THREE.Vector3(p.x * 0.8, p.y * 0.8, 0.34)), true), 240, 0.012, 6, true), glow('#ff7a1a', 1.2)); g.add(innerRim);
    const lock = new THREE.Group(); lock.position.set(0, -0.05, 0.42); g.add(lock);
    const lockMat = new THREE.MeshPhysicalMaterial({ color: '#ffd6ec', emissive: '#ff4fc4', emissiveIntensity: 0.55, metalness: 0.3, roughness: 0.18, clearcoat: 1 });
    mesh(extrude(rrect(0.95, 0.75, 0.14), 0.22, 0.05), lockMat, lock).position.y = -0.22;
    const shk = mesh(new THREE.TorusGeometry(0.3, 0.075, 16, 48, Math.PI), lockMat, lock, false); shk.position.y = 0.2;
    [-0.3, 0.3].forEach((x) => mesh(new THREE.CylinderGeometry(0.075, 0.075, 0.22, 16), lockMat, lock, false).position.set(x, 0.1, 0));
    const kh = new THREE.Shape(); kh.absarc(0, 0.06, 0.1, 0, Math.PI * 2);
    const khm = mesh(extrude(kh, 0.03, 0.01), M.ink, lock, false); khm.position.set(0, -0.18, 0.16);
    mesh(new THREE.BoxGeometry(0.07, 0.2, 0.03), M.ink, lock, false).position.set(0, -0.33, 0.16);
    // two crossing glowing orbits with travelling sparks
    const ring = mesh(new THREE.TorusGeometry(2.1, 0.022, 8, 200), glow('#ff2fb9', 1.5), g, false); ring.rotation.set(1.2, 0.35, 0.25);
    const ring2 = mesh(new THREE.TorusGeometry(1.95, 0.014, 8, 200), glow('#ff9a3c', 1.3), g, false); ring2.rotation.set(1.35, -0.45, -0.35);
    for (let i = 0; i < 3; i++) { const n = mesh(new THREE.SphereGeometry(0.055, 16, 16), glow('#ffd21f', 1.6), g, false); n.userData.orbit = i * 2.1; }
    models['shield-check'] = g;
  }
  /* COMPLIANCE — clipboard report with ticks + gold seal, shield with check in front, justice scales behind */
  {
    const g = new THREE.Group();
    // paper face drawn on a canvas so the heading and lines stay crisp
    const cv = document.createElement('canvas'); cv.width = 512; cv.height = 680; const cx = cv.getContext('2d');
    cx.fillStyle = '#fffafc'; cx.fillRect(0, 0, 512, 680);
    cx.fillStyle = '#2a0f1c'; cx.font = '800 64px Syne, Arial Black, sans-serif'; cx.textAlign = 'center'; cx.fillText('COMPLIANCE', 256, 130);
    const grd = cx.createLinearGradient(60, 0, 452, 0); grd.addColorStop(0, '#ff2fb9'); grd.addColorStop(1, '#ff7a1a'); cx.fillStyle = grd; cx.fillRect(150, 152, 212, 6);
    [250, 360, 470].forEach((y) => {
      cx.strokeStyle = '#ff2fb9'; cx.lineWidth = 16; cx.lineCap = 'round'; cx.lineJoin = 'round';
      cx.beginPath(); cx.moveTo(70, y); cx.lineTo(96, y + 26); cx.lineTo(146, y - 28); cx.stroke();
      cx.fillStyle = '#d9c9d2'; cx.fillRect(180, y - 18, 250, 14); cx.fillRect(180, y + 10, 170, 10);
    });
    const tex = new THREE.CanvasTexture(cv); tex.colorSpace = THREE.SRGBColorSpace; tex.anisotropy = 8;
    const board = new THREE.Group(); board.position.set(0.35, 0.15, -0.2); board.rotation.y = -0.22; g.add(board);
    mesh(extrude(rrect(2.3, 3.0, 0.2), 0.16), M.mag, board);
    const paper = new THREE.Mesh(new THREE.PlaneGeometry(1.95, 2.55), new THREE.MeshStandardMaterial({ map: tex, roughness: 0.6 })); paper.position.set(0, -0.12, 0.24); board.add(paper);
    const clip = mesh(extrude(rrect(0.95, 0.38, 0.12), 0.2), M.white, board); clip.position.set(0, 1.45, 0.3);
    const ring = mesh(new THREE.TorusGeometry(0.11, 0.035, 10, 24), M.white, board, false); ring.position.set(0, 1.7, 0.3);
    // gold certification seal with ribbons on the report
    const seal = new THREE.Group(); seal.position.set(0.55, -0.95, 0.4); board.add(seal);
    const rosette = new THREE.Shape(); for (let k = 0; k <= 32; k++) { const ang = k / 32 * Math.PI * 2, r = k % 2 ? 0.4 : 0.46; k ? rosette.lineTo(Math.cos(ang) * r, Math.sin(ang) * r) : rosette.moveTo(r, 0); }
    mesh(extrude(rosette, 0.08, 0.02), M.gold, seal);
    mesh(new THREE.TorusGeometry(0.28, 0.03, 8, 40), mat('#e8c46a', { metalness: 1, roughness: 0.15 }), seal, false).position.z = 0.07;
    const sck = mesh(extrude(check(0.34), 0.03, 0.01), mat('#a67c22', { metalness: 1, roughness: 0.3 }), seal, false); sck.position.z = 0.08;
    [-1, 1].forEach((d) => { const rb = mesh(new THREE.BoxGeometry(0.17, 0.55, 0.03), M.mag, seal); rb.position.set(d * 0.15, -0.5, -0.05); rb.rotation.z = d * 0.28; });
    // shield with check in front-left
    const sh = new THREE.Shape(); sh.moveTo(0, 0.95); sh.bezierCurveTo(0.38, 0.77, 0.65, 0.74, 0.8, 0.74); sh.bezierCurveTo(0.8, 0.06, 0.65, -0.58, 0, -0.98); sh.bezierCurveTo(-0.65, -0.58, -0.8, 0.06, -0.8, 0.74); sh.bezierCurveTo(-0.65, 0.74, -0.38, 0.77, 0, 0.95);
    const shield = new THREE.Group(); shield.position.set(-0.95, -0.35, 0.65); shield.rotation.y = 0.25; g.add(shield);
    mesh(extrude(sh, 0.26, 0.07), M.mag, shield);
    const rimPts = sh.getSpacedPoints(120).map((q) => new THREE.Vector3(q.x * 1.02, q.y * 1.02, 0.22));
    shield.add(new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3(rimPts, true), 180, 0.026, 8, true), glow('#ff2fb9', 1.6)));
    const face = mesh(extrude(sh, 0.06, 0.02), new THREE.MeshPhysicalMaterial({ color: '#ffd6ec', emissive: '#ff4fc4', emissiveIntensity: 0.35, metalness: 0.2, roughness: 0.2, clearcoat: 1 }), shield); face.scale.setScalar(0.78); face.position.z = 0.22;
    const ck = mesh(extrude(check(0.9), 0.08, 0.02), mat('#ffffff', { metalness: 0.1, roughness: 0.2 }), shield, false); ck.position.set(0, 0.02, 0.32);
    // justice scales behind, to the right
    const sc = new THREE.Group(); sc.position.set(1.95, -0.25, -0.9); sc.scale.setScalar(0.62); g.add(sc);
    mesh(new THREE.CylinderGeometry(0.55, 0.7, 0.18, 40), M.gold, sc).position.y = -1.55;
    mesh(new THREE.CylinderGeometry(0.07, 0.09, 2.9, 20), M.gold, sc).position.y = -0.05;
    mesh(new THREE.SphereGeometry(0.15, 24, 24), M.gold, sc, false).position.y = 1.45;
    const beam = new THREE.Group(); beam.position.y = 1.2; sc.add(beam); g.userData.beam = beam; g.userData.pans = [];
    mesh(new THREE.BoxGeometry(2.8, 0.08, 0.1), M.gold, beam);
    [-1.3, 1.3].forEach((x) => {
      const pan = new THREE.Group(); pan.position.x = x; beam.add(pan); g.userData.pans.push(pan);
      const lm = new THREE.LineBasicMaterial({ color: '#b8913a' });
      [-0.38, 0.38].forEach((dx) => pan.add(new THREE.Line(new THREE.BufferGeometry().setFromPoints([new THREE.Vector3(0, 0, 0), new THREE.Vector3(dx, -1.05, 0)]), lm)));
      mesh(new THREE.SphereGeometry(0.5, 40, 16, 0, Math.PI * 2, Math.PI * 0.62, Math.PI * 0.38), M.gold, pan, false).position.y = -0.6;
    });
    // glowing orbit with sparks
    const orb = mesh(new THREE.TorusGeometry(2.25, 0.02, 8, 200), glow('#ff2fb9', 1.5), g, false); orb.rotation.set(1.25, 0.2, 0.35);
    for (let k = 0; k < 3; k++) { const n = mesh(new THREE.SphereGeometry(0.055, 16, 16), glow('#ffd21f', 1.6), g, false); n.userData.orbit = k * 2.1; }
    g.scale.setScalar(0.92);
    models.badge = g;
  }
  /* THREAT DETECTION — shield + padlock, magnifier revealing a warning, floating threat cards */
  {
    const g = new THREE.Group();
    const sh = new THREE.Shape(); sh.moveTo(0, 1.4); sh.bezierCurveTo(0.55, 1.13, 0.95, 1.08, 1.18, 1.08); sh.bezierCurveTo(1.18, 0.08, 0.95, -0.86, 0, -1.45); sh.bezierCurveTo(-0.95, -0.86, -1.18, 0.08, -1.18, 1.08); sh.bezierCurveTo(-0.95, 1.08, -0.55, 1.13, 0, 1.4);
    const shield = new THREE.Group(); shield.position.set(-0.75, 0.15, 0); g.add(shield);
    mesh(extrude(sh, 0.32, 0.09), M.mag, shield);
    shield.add(new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3(sh.getSpacedPoints(160).map((q) => new THREE.Vector3(q.x * 1.02, q.y * 1.02, 0.27)), true), 240, 0.032, 8, true), glow('#ff2fb9', 1.6)));
    const face = mesh(extrude(sh, 0.06, 0.02), new THREE.MeshPhysicalMaterial({ color: '#1b0d15', metalness: 0.6, roughness: 0.25, clearcoat: 1 }), shield); face.scale.setScalar(0.8); face.position.z = 0.26;
    const lock = new THREE.Group(); lock.position.set(0, -0.05, 0.38); shield.add(lock);
    const lockMat = new THREE.MeshPhysicalMaterial({ color: '#ffd6ec', emissive: '#ff4fc4', emissiveIntensity: 0.55, metalness: 0.3, roughness: 0.18, clearcoat: 1 });
    mesh(extrude(rrect(0.85, 0.66, 0.12), 0.2, 0.04), lockMat, lock).position.y = -0.2;
    mesh(new THREE.TorusGeometry(0.27, 0.065, 16, 48, Math.PI), lockMat, lock, false).position.y = 0.18;
    [-0.27, 0.27].forEach((x) => mesh(new THREE.CylinderGeometry(0.065, 0.065, 0.2, 16), lockMat, lock, false).position.set(x, 0.09, 0));
    const kh = new THREE.Shape(); kh.absarc(0, 0.05, 0.09, 0, Math.PI * 2); mesh(extrude(kh, 0.03, 0.01), M.ink, lock, false).position.set(0, -0.16, 0.14);
    // magnifier with a red warning triangle
    const mag = new THREE.Group(); mag.position.set(0.35, -0.55, 0.75); mag.rotation.set(0.1, -0.35, 0); g.add(mag); g.userData.mag = mag;
    mesh(new THREE.TorusGeometry(0.62, 0.09, 20, 64), M.white, mag, false);
    mag.add(new THREE.Mesh(new THREE.CircleGeometry(0.6, 48), new THREE.MeshPhysicalMaterial({ color: '#3a0d1a', transmission: 0.5, roughness: 0.1, transparent: true, opacity: 0.85 })));
    const tri = new THREE.Shape(); tri.moveTo(0, 0.36); tri.lineTo(0.36, -0.26); tri.lineTo(-0.36, -0.26); tri.closePath();
    const hole = new THREE.Path(); hole.moveTo(0, 0.24); hole.lineTo(0.25, -0.19); hole.lineTo(-0.25, -0.19); hole.closePath(); tri.holes.push(hole);
    const warn = new THREE.Mesh(new THREE.ShapeGeometry(tri), glow('#ff3b4f', 2.2)); warn.position.z = 0.03; mag.add(warn);
    const bar = new THREE.Mesh(new THREE.BoxGeometry(0.05, 0.16, 0.01), glow('#ff3b4f', 2.2)); bar.position.set(0, 0.03, 0.03); mag.add(bar);
    const dot = new THREE.Mesh(new THREE.CircleGeometry(0.03, 16), glow('#ff3b4f', 2.2)); dot.position.set(0, -0.11, 0.03); mag.add(dot);
    const handle = mesh(new THREE.CylinderGeometry(0.08, 0.1, 1.0, 20), M.ink, mag); handle.position.set(0.68, -0.68, 0); handle.rotation.z = Math.PI / 4;
    // floating glass threat cards with drawn glyphs
    const card = (title, glyph) => {
      const cv = document.createElement('canvas'); cv.width = 512; cv.height = 220; const c = cv.getContext('2d');
      const gr = c.createLinearGradient(0, 0, 512, 220); gr.addColorStop(0, 'rgba(255,255,255,.92)'); gr.addColorStop(1, 'rgba(255,226,240,.92)');
      c.fillStyle = gr; c.beginPath(); c.roundRect(4, 4, 504, 212, 34); c.fill(); c.strokeStyle = 'rgba(255,47,185,.55)'; c.lineWidth = 5; c.stroke();
      c.strokeStyle = '#2a0f1c'; c.fillStyle = '#2a0f1c'; c.lineWidth = 9; c.lineCap = 'round'; c.lineJoin = 'round'; glyph(c);
      c.fillStyle = '#2a0f1c'; c.font = '700 46px Inter, Arial, sans-serif'; c.textBaseline = 'middle';
      title.split('\n').forEach((t, k, arr) => c.fillText(t, 190, 110 + (k - (arr.length - 1) / 2) * 54));
      const tx = new THREE.CanvasTexture(cv); tx.colorSpace = THREE.SRGBColorSpace; tx.anisotropy = 8;
      const m = new THREE.Mesh(new THREE.PlaneGeometry(1.5, 0.645), new THREE.MeshBasicMaterial({ map: tx, transparent: true, toneMapped: false }));
      return m;
    };
    const cards = [
      card('Malware\nThreats', (c) => { c.beginPath(); c.arc(100, 80, 34, 0, Math.PI * 2); c.fill(); c.beginPath(); c.moveTo(48, 180); c.quadraticCurveTo(100, 100, 152, 180); c.fill(); c.fillStyle = '#ff2fb9'; c.fillRect(78, 72, 44, 10); }),
      card('Phishing\nAttacks', (c) => { c.strokeRect(46, 70, 110, 80); c.beginPath(); c.moveTo(46, 70); c.lineTo(101, 118); c.lineTo(156, 70); c.stroke(); c.strokeStyle = '#ff2fb9'; c.beginPath(); c.moveTo(130, 30); c.lineTo(130, 70); c.arc(118, 70, 12, 0, Math.PI); c.stroke(); }),
      card('Vulnerabilities', (c) => { c.beginPath(); c.ellipse(100, 116, 32, 44, 0, 0, Math.PI * 2); c.fill(); c.beginPath(); c.arc(100, 64, 18, 0, Math.PI * 2); c.fill(); [[-1, 90], [-1, 120], [-1, 150], [1, 90], [1, 120], [1, 150]].forEach(([d, y]) => { c.beginPath(); c.moveTo(100 + d * 30, y); c.lineTo(100 + d * 58, y - 12); c.stroke(); }); }),
    ];
    cards.forEach((m, k) => { m.position.set(1.7, 1.0 - k * 0.85, -0.2 + k * 0.1); m.rotation.y = -0.32; m.scale.setScalar(0.92); m.userData.bob = k; g.add(m); });
    g.userData.cards = cards;
    const orb = mesh(new THREE.TorusGeometry(2.25, 0.02, 8, 200), glow('#ff2fb9', 1.5), g, false); orb.rotation.set(1.25, 0.15, 0.3);
    for (let k = 0; k < 3; k++) { const n = mesh(new THREE.SphereGeometry(0.055, 16, 16), glow('#ffd21f', 1.6), g, false); n.userData.orbit = k * 2.1; }
    g.scale.setScalar(0.9);
    models.radar = g;
  }
  /* INFRASTRUCTURE — shield + padlock before a dotted globe, server racks either side, floating glass tiles */
  {
    const g = new THREE.Group();
    // dotted globe with a few glowing links
    const globe = new THREE.Group(); globe.position.set(0.15, 0.2, -1.6); globe.scale.setScalar(0.9); g.add(globe); g.userData.globe = globe;
    const gp = []; for (let k = 0; k < 900; k++) { const y = 1 - (k / 899) * 2, r = Math.sqrt(1 - y * y), th = k * 2.39996; gp.push(Math.cos(th) * r * 1.9, y * 1.9, Math.sin(th) * r * 1.9); }
    const gg = new THREE.BufferGeometry(); gg.setAttribute('position', new THREE.Float32BufferAttribute(gp, 3));
    globe.add(new THREE.Points(gg, new THREE.PointsMaterial({ color: '#7a2a5a', size: 0.035, transparent: true, opacity: 0.75 })));
    globe.add(new THREE.LineSegments(new THREE.WireframeGeometry(new THREE.SphereGeometry(1.9, 18, 12)), new THREE.LineBasicMaterial({ color: '#ff2fb9', transparent: true, opacity: 0.12 })));
    // server racks with LED rows
    const rackFace = (() => { const cv = document.createElement('canvas'); cv.width = 128; cv.height = 512; const c = cv.getContext('2d'); c.fillStyle = '#1a0b13'; c.fillRect(0, 0, 128, 512);
      for (let y = 14; y < 500; y += 30) { c.fillStyle = '#2c1520'; c.fillRect(10, y, 108, 22); c.fillStyle = y % 60 ? '#ff2fb9' : '#ff9a3c'; c.fillRect(16, y + 8, 6, 6); c.fillStyle = 'rgba(255,120,200,.55)'; c.fillRect(30, y + 9, 60, 4); }
      const t = new THREE.CanvasTexture(cv); t.colorSpace = THREE.SRGBColorSpace; return t; })();
    const rackMat = [M.ink, M.ink, M.ink, M.ink, new THREE.MeshStandardMaterial({ map: rackFace, emissive: '#ffffff', emissiveMap: rackFace, emissiveIntensity: 0.6, roughness: 0.4 }), M.ink];
    [[-2.05, 2.3, -0.7], [-1.45, 1.8, -0.35], [1.75, 2.5, -0.7], [2.3, 2.0, -0.35]].forEach(([x, h, z]) => {
      const r = new THREE.Mesh(new THREE.BoxGeometry(0.55, h, 0.6), rackMat); r.position.set(x, -1.4 + h / 2, z); r.rotation.y = x < 0 ? 0.25 : -0.25;
      r.add(new THREE.LineSegments(new THREE.EdgesGeometry(r.geometry), edgeMat)); g.add(r);
    });
    // shield + padlock
    const sh = new THREE.Shape(); sh.moveTo(0, 1.3); sh.bezierCurveTo(0.5, 1.05, 0.88, 1.0, 1.08, 1.0); sh.bezierCurveTo(1.08, 0.08, 0.88, -0.8, 0, -1.35); sh.bezierCurveTo(-0.88, -0.8, -1.08, 0.08, -1.08, 1.0); sh.bezierCurveTo(-0.88, 1.0, -0.5, 1.05, 0, 1.3);
    const shield = new THREE.Group(); shield.position.set(0.1, 0.0, 0.6); g.add(shield);
    mesh(extrude(sh, 0.3, 0.09), M.mag, shield);
    shield.add(new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3(sh.getSpacedPoints(160).map((q) => new THREE.Vector3(q.x * 1.02, q.y * 1.02, 0.26)), true), 240, 0.032, 8, true), glow('#ff7a1a', 1.6)));
    const face = mesh(extrude(sh, 0.06, 0.02), new THREE.MeshPhysicalMaterial({ color: '#2a1030', metalness: 0.5, roughness: 0.25, clearcoat: 1 }), shield); face.scale.setScalar(0.8); face.position.z = 0.25;
    const lock = new THREE.Group(); lock.position.set(0, -0.05, 0.37); shield.add(lock);
    const lockMat = new THREE.MeshPhysicalMaterial({ color: '#ffe0f0', emissive: '#ff4fc4', emissiveIntensity: 0.6, metalness: 0.3, roughness: 0.18, clearcoat: 1 });
    mesh(extrude(rrect(0.8, 0.62, 0.12), 0.2, 0.04), lockMat, lock).position.y = -0.2;
    mesh(new THREE.TorusGeometry(0.25, 0.062, 16, 48, Math.PI), lockMat, lock, false).position.y = 0.16;
    [-0.25, 0.25].forEach((x) => mesh(new THREE.CylinderGeometry(0.062, 0.062, 0.2, 16), lockMat, lock, false).position.set(x, 0.07, 0));
    const kh = new THREE.Shape(); kh.absarc(0, 0.05, 0.085, 0, Math.PI * 2); mesh(extrude(kh, 0.03, 0.01), M.ink, lock, false).position.set(0, -0.16, 0.14);
    // floating glass tiles
    const tile = (draw) => {
      const cv = document.createElement('canvas'); cv.width = 256; cv.height = 256; const c = cv.getContext('2d');
      c.fillStyle = 'rgba(255,240,248,.9)'; c.beginPath(); c.roundRect(6, 6, 244, 244, 40); c.fill(); c.strokeStyle = 'rgba(255,47,185,.6)'; c.lineWidth = 6; c.stroke();
      c.strokeStyle = '#ff2fb9'; c.fillStyle = '#ff2fb9'; c.lineWidth = 12; c.lineCap = 'round'; c.lineJoin = 'round'; draw(c);
      const tx = new THREE.CanvasTexture(cv); tx.colorSpace = THREE.SRGBColorSpace;
      return new THREE.Mesh(new THREE.PlaneGeometry(0.72, 0.72), new THREE.MeshBasicMaterial({ map: tx, transparent: true, toneMapped: false }));
    };
    const tiles = [
      tile((c) => { c.beginPath(); c.moveTo(128, 50); c.quadraticCurveTo(170, 72, 196, 72); c.quadraticCurveTo(196, 160, 128, 206); c.quadraticCurveTo(60, 160, 60, 72); c.quadraticCurveTo(86, 72, 128, 50); c.stroke(); c.beginPath(); c.moveTo(98, 128); c.lineTo(120, 150); c.lineTo(160, 108); c.stroke(); }),
      tile((c) => { [[64, 150], [104, 120], [144, 90], [184, 60]].forEach(([x, y]) => c.fillRect(x - 12, y, 24, 196 - y)); }),
      tile((c) => { for (let r = 22; r <= 82; r += 15) { c.beginPath(); c.arc(128, 140, r, Math.PI * 1.08, Math.PI * 1.92 + (r % 2) * 0.3); c.stroke(); } c.beginPath(); c.moveTo(128, 140); c.lineTo(128, 200); c.stroke(); }),
    ];
    [[-1.75, 1.05, 0.2], [2.05, 0.6, 0.15], [1.75, -0.45, 0.5]].forEach(([x, y, z], k) => { const m = tiles[k]; m.position.set(x, y, z); m.rotation.y = x < 0 ? 0.35 : -0.35; m.userData.bob = k; g.add(m); });
    g.userData.cards = tiles;
    const orb = mesh(new THREE.TorusGeometry(1.9, 0.024, 8, 200), glow('#ff7a1a', 1.6), g, false); orb.rotation.set(1.3, 0.1, 0.2); orb.position.z = 0.4;
    for (let k = 0; k < 3; k++) { const n = mesh(new THREE.SphereGeometry(0.055, 16, 16), glow('#ffd21f', 1.6), g, false); n.userData.orbit = k * 2.1; }
    g.scale.setScalar(0.88);
    models.lock = g;
  }
  /* CYBER LEGAL — bronze scales over a shield + padlock, laptop, law books, document, floating legal tiles */
  {
    const g = new THREE.Group();
    const bronze = mat('#c9874a', { metalness: 1, roughness: 0.22 });
    const cnv = (w, h, draw) => { const cv = document.createElement('canvas'); cv.width = w; cv.height = h; draw(cv.getContext('2d')); const t = new THREE.CanvasTexture(cv); t.colorSpace = THREE.SRGBColorSpace; t.anisotropy = 8; return t; };
    // dotted globe behind
    const globe = new THREE.Group(); globe.position.set(0.9, 0.6, -2.0); g.add(globe); g.userData.globe = globe;
    const gp = []; for (let k = 0; k < 800; k++) { const y = 1 - (k / 799) * 2, r = Math.sqrt(1 - y * y), th = k * 2.39996; gp.push(Math.cos(th) * r * 1.7, y * 1.7, Math.sin(th) * r * 1.7); }
    const gg = new THREE.BufferGeometry(); gg.setAttribute('position', new THREE.Float32BufferAttribute(gp, 3));
    globe.add(new THREE.Points(gg, new THREE.PointsMaterial({ color: '#7a2a5a', size: 0.034, transparent: true, opacity: 0.7 })));
    // scales
    const sc = new THREE.Group(); sc.position.set(0, 0.25, 0); g.add(sc);
    mesh(new THREE.CylinderGeometry(0.07, 0.09, 2.6, 24), bronze, sc, false).position.y = -0.05;
    mesh(new THREE.SphereGeometry(0.16, 24, 24), bronze, sc, false).position.y = 1.35;
    mesh(new THREE.CylinderGeometry(0.12, 0.16, 0.25, 24), bronze, sc, false).position.y = 1.1;
    const beam = new THREE.Group(); beam.position.y = 1.0; sc.add(beam); g.userData.beam = beam; g.userData.pans = [];
    const arm = new THREE.Mesh(new THREE.TubeGeometry(new THREE.QuadraticBezierCurve3(new THREE.Vector3(-1.3, -0.12, 0), new THREE.Vector3(0, 0.25, 0), new THREE.Vector3(1.3, -0.12, 0)), 40, 0.05, 10), bronze); beam.add(arm);
    [-1.3, 1.3].forEach((x) => {
      const pan = new THREE.Group(); pan.position.set(x, -0.12, 0); beam.add(pan); g.userData.pans.push(pan);
      [[-0.32, 0.12], [0.32, 0.12], [0, -0.3]].forEach(([dx, dz]) => { const c = new THREE.Mesh(new THREE.CylinderGeometry(0.008, 0.008, 1.0, 6), bronze); c.position.set(dx / 2, -0.5, dz / 2); c.rotation.z = -dx * 0.6; c.rotation.x = dz * 0.6; pan.add(c); });
      mesh(new THREE.SphereGeometry(0.42, 40, 16, 0, Math.PI * 2, Math.PI * 0.6, Math.PI * 0.4), bronze, pan, false).position.y = -0.62;
      mesh(new THREE.TorusGeometry(0.4, 0.02, 8, 48), bronze, pan, false).rotation.x = Math.PI / 2;
    });
    // shield + padlock at the base of the scales
    const sh = new THREE.Shape(); sh.moveTo(0, 0.82); sh.bezierCurveTo(0.32, 0.66, 0.56, 0.63, 0.68, 0.63); sh.bezierCurveTo(0.68, 0.05, 0.56, -0.5, 0, -0.84); sh.bezierCurveTo(-0.56, -0.5, -0.68, 0.05, -0.68, 0.63); sh.bezierCurveTo(-0.56, 0.63, -0.32, 0.66, 0, 0.82);
    const shield = new THREE.Group(); shield.position.set(0.05, -0.85, 0.55); g.add(shield);
    mesh(extrude(sh, 0.24, 0.07), M.mag, shield);
    shield.add(new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3(sh.getSpacedPoints(120).map((q) => new THREE.Vector3(q.x * 1.02, q.y * 1.02, 0.2)), true), 180, 0.026, 8, true), glow('#ff2fb9', 1.6)));
    const lockMat = new THREE.MeshPhysicalMaterial({ color: '#ffe0f0', emissive: '#ff4fc4', emissiveIntensity: 0.6, metalness: 0.3, roughness: 0.18, clearcoat: 1 });
    const lock = new THREE.Group(); lock.position.set(0, -0.05, 0.28); shield.add(lock);
    mesh(extrude(rrect(0.5, 0.4, 0.08), 0.14, 0.03), lockMat, lock).position.y = -0.12;
    mesh(new THREE.TorusGeometry(0.16, 0.042, 12, 32, Math.PI), lockMat, lock, false).position.y = 0.1;
    [-0.16, 0.16].forEach((x) => mesh(new THREE.CylinderGeometry(0.042, 0.042, 0.13, 12), lockMat, lock, false).position.set(x, 0.04, 0));
    const kh = new THREE.Shape(); kh.absarc(0, 0.03, 0.055, 0, Math.PI * 2); mesh(extrude(kh, 0.02, 0.01), M.ink, lock, false).position.set(0, -0.1, 0.1);
    // laptop on the left
    const lap = new THREE.Group(); lap.position.set(-1.6, -1.25, 0.25); lap.rotation.y = 0.45; g.add(lap);
    mesh(new THREE.BoxGeometry(1.25, 0.05, 0.85), M.white, lap);
    const scr = new THREE.Group(); scr.position.set(0, 0.03, -0.42); scr.rotation.x = -0.22; lap.add(scr);
    mesh(new THREE.BoxGeometry(1.25, 0.82, 0.04), M.ink, scr).position.y = 0.41;
    const screenTex = cnv(512, 330, (c) => { const gr = c.createLinearGradient(0, 0, 512, 330); gr.addColorStop(0, '#3a0d2e'); gr.addColorStop(1, '#7a1f5a'); c.fillStyle = gr; c.fillRect(0, 0, 512, 330);
      c.strokeStyle = 'rgba(255,120,200,.35)'; c.lineWidth = 2; for (let k = 0; k < 9; k++) { c.beginPath(); c.moveTo(0, 40 + k * 32); c.lineTo(140, 40 + k * 32); c.lineTo(170, 60 + k * 26); c.stroke(); c.beginPath(); c.moveTo(512, 40 + k * 32); c.lineTo(370, 40 + k * 32); c.lineTo(340, 60 + k * 26); c.stroke(); }
      c.strokeStyle = '#ffd6ec'; c.lineWidth = 10; c.lineJoin = 'round'; c.beginPath(); c.moveTo(256, 70); c.quadraticCurveTo(296, 92, 326, 92); c.quadraticCurveTo(326, 200, 256, 255); c.quadraticCurveTo(186, 200, 186, 92); c.quadraticCurveTo(216, 92, 256, 70); c.stroke();
      c.beginPath(); c.moveTo(226, 160); c.lineTo(250, 184); c.lineTo(292, 136); c.stroke(); });
    const screen = new THREE.Mesh(new THREE.PlaneGeometry(1.15, 0.74), new THREE.MeshBasicMaterial({ map: screenTex, toneMapped: false })); screen.position.set(0, 0.41, 0.025); scr.add(screen);
    // law books + document on the right
    const books = new THREE.Group(); books.position.set(1.85, -1.3, 0.2); books.rotation.y = -0.4; g.add(books);
    ['REGULATIONS', 'DATA PRIVACY', 'CYBER LAW', 'LAW'].forEach((title, k) => {
      const spine = cnv(512, 96, (c) => { c.fillStyle = ['#3a1030', '#2a0f1c', '#4a1438', '#2a0f1c'][k]; c.fillRect(0, 0, 512, 96); c.fillStyle = '#e8c46a'; c.fillRect(0, 10, 512, 4); c.fillRect(0, 82, 512, 4); c.font = '700 44px Georgia, serif'; c.textAlign = 'center'; c.textBaseline = 'middle'; c.fillText(title, 256, 50); });
      const cover = new THREE.MeshStandardMaterial({ color: ['#3a1030', '#2a0f1c', '#4a1438', '#2a0f1c'][k], roughness: 0.5 });
      const pages = new THREE.MeshStandardMaterial({ color: '#f6efe6', roughness: 0.8 });
      const bk = new THREE.Mesh(new THREE.BoxGeometry(1.35, 0.24, 0.85), [pages, cover, cover, cover, new THREE.MeshStandardMaterial({ map: spine, roughness: 0.5 }), cover]);
      bk.position.set((k % 2) * 0.06 - 0.03, 0.12 + k * 0.25, 0); bk.rotation.y = (k % 2 ? 0.05 : -0.04); books.add(bk);
    });
    const docTex = cnv(360, 460, (c) => { c.fillStyle = '#fffafc'; c.fillRect(0, 0, 360, 460); c.fillStyle = '#d9c9d2'; for (let k = 0; k < 9; k++) c.fillRect(30, 60 + k * 40, k % 3 === 2 ? 150 : 210, 12);
      c.fillStyle = '#2a0f1c'; c.beginPath(); c.moveTo(285, 210); c.quadraticCurveTo(315, 226, 335, 226); c.quadraticCurveTo(335, 300, 285, 335); c.quadraticCurveTo(235, 300, 235, 226); c.quadraticCurveTo(255, 226, 285, 210); c.fill();
      c.strokeStyle = '#ff2fb9'; c.lineWidth = 9; c.lineCap = 'round'; c.beginPath(); c.moveTo(265, 270); c.lineTo(281, 286); c.lineTo(306, 256); c.stroke(); });
    const doc = new THREE.Mesh(new THREE.PlaneGeometry(0.85, 1.08), new THREE.MeshStandardMaterial({ map: docTex, roughness: 0.6, side: THREE.DoubleSide })); doc.position.set(0.15, 1.55, -0.15); doc.rotation.set(-0.08, 0, -0.08); books.add(doc);
    // floating legal tiles
    const tile = (draw) => { const tx = cnv(256, 256, (c) => { c.fillStyle = 'rgba(255,240,248,.92)'; c.beginPath(); c.roundRect(6, 6, 244, 244, 40); c.fill(); c.strokeStyle = 'rgba(255,47,185,.6)'; c.lineWidth = 6; c.stroke(); c.strokeStyle = '#ff2fb9'; c.fillStyle = '#ff2fb9'; c.lineWidth = 12; c.lineCap = 'round'; c.lineJoin = 'round'; draw(c); });
      return new THREE.Mesh(new THREE.PlaneGeometry(0.62, 0.62), new THREE.MeshBasicMaterial({ map: tx, transparent: true, toneMapped: false })); };
    const tiles = [
      tile((c) => { for (let r = 22; r <= 82; r += 15) { c.beginPath(); c.arc(128, 140, r, Math.PI * 1.08, Math.PI * 1.92); c.stroke(); } c.beginPath(); c.moveTo(128, 140); c.lineTo(128, 200); c.stroke(); }),
      tile((c) => { c.save(); c.translate(128, 118); c.rotate(-0.7); c.fillRect(-58, -22, 116, 44); c.fillRect(-10, 22, 20, 80); c.restore(); c.fillRect(70, 190, 116, 16); }),
      tile((c) => { c.beginPath(); c.moveTo(128, 56); c.lineTo(128, 196); c.moveTo(60, 86); c.lineTo(196, 86); c.stroke(); [[78, 1], [178, 1]].forEach(([x]) => { c.beginPath(); c.moveTo(x - 30, 140); c.lineTo(x, 90); c.lineTo(x + 30, 140); c.stroke(); c.beginPath(); c.arc(x, 140, 30, 0, Math.PI); c.fill(); }); c.fillRect(90, 196, 76, 14); }),
    ];
    [[-1.15, 1.75, -0.4], [-1.85, 0.55, -0.2], [2.25, 1.35, -0.6]].forEach(([x, y, z], k) => { const m = tiles[k]; m.position.set(x, y, z); m.rotation.y = x < 0 ? 0.3 : -0.3; m.userData.bob = k; g.add(m); });
    g.userData.cards = tiles;
    g.scale.setScalar(0.78);
    models.scale = g;
  }

  Object.entries(models).forEach(([k, m]) => { m.name = k; m.visible = false; scene.add(m); });
  const pick = (icon) => models[icon] || models['shield-check'];
  const rig = new THREE.Group(); scene.add(rig);
  // holographic plinth shared by every model
  const plinth = new THREE.Group(); plinth.position.y = -2.15; scene.add(plinth);
  [1.7, 2.1, 2.5].forEach((r, i) => { const t = new THREE.Mesh(new THREE.TorusGeometry(r, i === 1 ? 0.014 : 0.007, 6, 160), glow('#ff2fb9', i === 1 ? 1.4 : 0.8)); t.rotation.x = Math.PI / 2; plinth.add(t); });
  // stepped pedestal: graphite tiers with glowing edges
  [[1.55, 0.16, -0.1], [1.2, 0.14, 0.05], [0.85, 0.1, 0.17]].forEach(([r, h, y], i) => {
    const tier = new THREE.Mesh(new THREE.CylinderGeometry(r, r * 1.04, h, 96), mat('#24121c', { metalness: 0.85, roughness: 0.3 })); tier.position.y = y; plinth.add(tier);
    const edge = new THREE.Mesh(new THREE.TorusGeometry(r * 1.01, 0.012, 6, 160), glow(i === 2 ? '#ff7a1a' : '#ff2fb9', 1.5)); edge.rotation.x = Math.PI / 2; edge.position.y = y + h / 2; plinth.add(edge);
  });
  const beamUp = new THREE.Mesh(new THREE.CylinderGeometry(0.75, 0.85, 1.2, 48, 1, true), new THREE.MeshBasicMaterial({ color: '#ff2fb9', transparent: true, opacity: 0.08, side: THREE.DoubleSide, depthWrite: false })); beamUp.position.y = 0.82; plinth.add(beamUp);
  const disc = new THREE.Mesh(new THREE.CircleGeometry(2.1, 64), new THREE.MeshBasicMaterial({ color: '#ff2fb9', transparent: true, opacity: 0.07 })); disc.rotation.x = -Math.PI / 2; plinth.add(disc);
  for (let i = 0; i < 24; i++) { const a = i / 24 * Math.PI * 2; const tick = new THREE.Mesh(new THREE.BoxGeometry(0.02, 0.01, i % 6 ? 0.12 : 0.26), glow('#ff2fb9', 1)); tick.position.set(Math.cos(a) * 2.3, 0, Math.sin(a) * 2.3); tick.rotation.y = -a; plinth.add(tick); }
  let current = pick(hs.querySelector('.hs__slide.is-active')?.dataset.icon);
  let pending = null, flipT = 1, flipStart = 0;
  rig.add(current); current.visible = true;

  hs.addEventListener('hs:change', (e) => {
    const next = pick(e.detail.icon);
    if (next === current && !pending) return;
    pending = next; flipT = 0; flipStart = performance.now();
  });

  const ptr = new THREE.Vector2();
  addEventListener('pointermove', (e) => ptr.set(e.clientX / innerWidth - 0.5, e.clientY / innerHeight - 0.5), { passive: true });
  const size = () => { const r = canvas.getBoundingClientRect(); renderer.setSize(r.width, r.height, false); camera.aspect = r.width / Math.max(1, r.height); camera.updateProjectionMatrix(); };
  new ResizeObserver(size).observe(canvas); size();

  let visible = true;
  new IntersectionObserver(([en]) => { visible = en.isIntersecting; }).observe(hs);
  const clock = new THREE.Clock();
  const ease = (x) => (x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2);
  renderer.setAnimationLoop(() => {
    if (!visible || document.hidden) return;
    const dt = Math.min(clock.getDelta(), 0.05), t = clock.elapsedTime;
    let flip = 0;
    if (flipT < 1) {
      flipT = Math.min(1, (performance.now() - flipStart) / (reduce ? 200 : 800));
      if (pending && flipT >= 0.5) { rig.remove(current); current.visible = false; current = pending; pending = null; rig.add(current); current.visible = true; hs.dataset.model = current.name; }
      flip = ease(flipT) * Math.PI; // one half-turn; the swap happens edge-on
    }
    const idle = reduce ? 0 : Math.sin(t * 0.6) * 0.35;
    rig.rotation.set(0.08 + ptr.y * 0.2, idle + ptr.x * 0.5 + flip + (flipT >= 0.5 && flipT < 1 ? -Math.PI : 0), 0);
    rig.position.y = reduce ? 0 : Math.sin(t * 1.1) * 0.08;
    plinth.rotation.y = t * 0.15;
    const ud = current.userData;
    if (ud.sweep) ud.sweep.rotation.z = -t * 1.6;
    if (ud.globe) ud.globe.rotation.y = t * 0.12;
    if (ud.cards) ud.cards.forEach((m) => { m.position.y += Math.sin(t * 1.4 + m.userData.bob * 1.7) * 0.0015; });
    if (ud.mag) { ud.mag.position.x = 0.35 + Math.sin(t * 0.9) * 0.12; ud.mag.position.y = -0.55 + Math.cos(t * 0.7) * 0.08; }
    if (ud.beam) { const a = Math.sin(t * 1.2) * 0.12; ud.beam.rotation.z = a; ud.pans.forEach((p) => { p.rotation.z = -a; }); }
    current.children.forEach((c) => { if (c.userData.orbit !== undefined) { const a = t * 0.8 + c.userData.orbit; c.position.set(Math.cos(a) * 2.05, Math.sin(a) * 0.6, Math.sin(a) * 0.9); c.rotation.set(t, t, 0); } });
    renderer.render(scene, camera);
  });
  hs.classList.add('has-3d');
  hs.dataset.model = current.name;
}
