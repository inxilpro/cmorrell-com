<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<title>CVE Slots - Chris Morrell</title>
	<meta property="og:url" content="{{ url()->current() }}" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="CVE Slots" />
	<meta property="og:description" content="Security Research Made Easy!" />
	<meta property="og:image" content="{{ asset('images/cve-slots-og.png') }}" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta property="twitter:domain" content="cmorrell.com" />
	<meta property="twitter:url" content="{{ url()->current() }}" />
	<meta name="twitter:creator" content="@inxilpro" />
	<meta name="twitter:title" content="CVE Slots" />
	<meta name="twitter:description" content="Security Research Made Easy!" />
	<meta name="twitter:image" content="{{ asset('images/cve-slots-og.png') }}" />
	<style>
	html, body {
		margin: 0;
		height: 100%;
		overflow: hidden;
		background: #06070a;
		overscroll-behavior: none;
		-webkit-tap-highlight-color: transparent;
	}
	
	body {
		font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
		color: #cfd3da;
		user-select: none;
	}
	
	canvas {
		display: block;
		touch-action: none;
	}
	
	#loading {
		position: fixed;
		inset: 0;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #e5484d;
		font-size: 14px;
		letter-spacing: .15em;
		text-align: center;
		padding: 20px;
	}
	
	.hud {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 12px;
		padding: 12px 18px calc(12px + env(safe-area-inset-bottom));
		font-size: 12px;
		pointer-events: none;
		background: linear-gradient(transparent, rgba(0, 0, 0, .7));
	}
	
	.hud b {
		color: #ffcf4a;
	}
	
	.hud button {
		pointer-events: auto;
		font: inherit;
		color: #cfd3da;
		background: rgba(255, 255, 255, .06);
		border: 1px solid rgba(255, 255, 255, .15);
		border-radius: 6px;
		padding: 6px 10px;
		cursor: pointer;
	}
	
	.hud button:hover {
		background: rgba(255, 255, 255, .12);
	}
	
	#history {
		position: fixed;
		top: calc(14px + env(safe-area-inset-top));
		left: calc(14px + env(safe-area-inset-left));
		font-size: 11px;
		line-height: 1.6;
		color: #7d838d;
		pointer-events: none;
		max-width: 40vw;
	}
	
	#history .h-title {
		color: #e5484d;
		letter-spacing: .2em;
		margin-bottom: 4px;
	}
	
	#history div.row {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	
	#history div.row:first-of-type {
		color: #3dd68c;
	}
	
	.touch-hint {
		display: none;
	}
	
	@media (pointer: coarse) {
		.mouse-hint {
			display: none;
		}
		
		.touch-hint {
			display: inline;
		}
	}
	
	@media (max-width: 640px) {
		.hud {
			font-size: 11px;
			padding-left: 12px;
			padding-right: 12px;
		}
		
		.hud .hint {
			display: none;
		}
		
		#history {
			font-size: 9px;
			max-width: 70vw;
			opacity: .8;
		}
		
		#history div.row:nth-of-type(n+4) {
			display: none;
		}
	}
	</style>
	<script type="importmap">
	{
	  "imports": {
		"three": "https://cdn.jsdelivr.net/npm/three@0.170.0/build/three.module.js",
		"three/addons/": "https://cdn.jsdelivr.net/npm/three@0.170.0/examples/jsm/"
	  }
	}
	</script>
</head>
<body>
<div id="loading">LOADING EXPLOITS…<br><br>(needs an internet connection for three.js)</div>
<script>
setTimeout(() => {
	const el = document.getElementById('loading');
	if (el) {
		el.innerHTML = 'COULDN\'T LOAD THREE.JS<br><br>Check your internet connection and reload.';
	}
}, 12000);
</script>
<div id="history"></div>
<div class="hud">
	<span class="hint"><span class="mouse-hint">Drag the lever · click the red button · or press <b>Space</b></span><span class="touch-hint">Pull the lever or tap the machine</span></span>
	<span>Pagers ruined: <b id="count">0</b></span>
	<button id="mute">🔊 Sound on</button>
</div>

<script type="module">
import * as THREE from 'three';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';

document.getElementById('loading').remove();

/* ------------------------------------------------------------------ */
/* Content                                                             */
/* ------------------------------------------------------------------ */
const SOFTWARE = [
	"PHP", "nginx", "curl", "OpenSSL", "OpenSSH", "glibc", "sudo", "systemd",
	"Bash", "Log4j", "Apache", "Redis", "Postgres", "MySQL", "SQLite", "zlib",
	"libxml2", "ImageMagick", "FFmpeg", "Git", "npm", "Docker", "Kubernetes",
	"BIND", "Exim", "Sendmail", "Jenkins", "WordPress", "Composer", "xz",
	"Node", "Python", "Java", "Chrome", "Linux", "Wi-Fi", "Bluetooth", "DNS",
	"BGP", "NTP", "Laravel", "React", "Firefox", "Kafka", "Memcached", "Varnish"
];
const EXPLOITS = [
	"2Shell", "Bleed", "Shock", "Pwn", "Doom", "Spill", "Ghost", "Leak",
	"Hammer", "Drown", "Poodle", "Crash", "Smash", "Jack", "Storm", "Spectre",
	"Meltdown", "Rot", "Fall", "Wreck", "Crypt", "Melt", "Snatch", "Hell",
	"Gate", "Panic", "Gloom", "Break", "Choke", "Root", "Worm", "Nado",
	"Pocalypse", "Geddon", "Flood", "Cry", "Sploit", "Heist", "Rip", "Bomb"
];
const TAGLINES = [
	"Affects every version since 1998.",
	"Patch available. Nobody will apply it.",
	"Exploited in the wild since last Tuesday.",
	"Discovered by a 14-year-old on Discord.",
	"Has a logo and a website before a fix.",
	"Maintained by one guy in Nebraska.",
	"Vendor response: 'working as intended.'",
	"Requires only network access and mild curiosity.",
	"Your SBOM says you're fine. You're not.",
	"Embargo lifted early. Sorry.",
	"Mitigation: unplug everything.",
	"Scored 9.8, disputed down to 9.7.",
	"Found in a dependency of a dependency.",
	"Reported in 2019. Closed as 'won't fix.'",
	"Already in Metasploit. And on TikTok.",
	"Proof of concept fits in a tweet.",
	"Security team is on PTO until Monday.",
	"Default config is vulnerable. Obviously.",
	"Fixed upstream. Your distro ships 2017.",
	"CISA added it to KEV before lunch."
];

const rand = (a, b) => a + Math.random() * (b - a);
const randInt = (n) => Math.floor(Math.random() * n);
const pick = (arr) => arr[randInt(arr.length)];
const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
const TAU = Math.PI * 2;

/* ------------------------------------------------------------------ */
/* Renderer / scene                                                    */
/* ------------------------------------------------------------------ */
const IS_TOUCH = matchMedia('(pointer: coarse)').matches;
let renderer;
try {
	renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: 'high-performance' });
} catch (err) {
	document.body.insertAdjacentHTML('beforeend',
		'<div style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;color:#e5484d;text-align:center;padding:20px">WEBGL ISN\'T AVAILABLE ON THIS DEVICE.<br><br>Ironically, that\'s the most secure configuration.</div>');
	throw err;
}
renderer.setPixelRatio(Math.min(devicePixelRatio, IS_TOUCH ? 1.5 : 2));
renderer.setSize(innerWidth, innerHeight);
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.0;
document.body.prepend(renderer.domElement);

const scene = new THREE.Scene();
scene.background = new THREE.Color(0x06070a);
scene.fog = new THREE.Fog(0x06070a, 14, 30);

const pmrem = new THREE.PMREMGenerator(renderer);
scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
scene.environmentIntensity = 0.55;

const camera = new THREE.PerspectiveCamera(35, innerWidth / innerHeight, 0.1, 100);
const lookTarget = new THREE.Vector3(0.3, 0.85, 0);

scene.add(new THREE.AmbientLight(0xffffff, 0.15));
const key = new THREE.DirectionalLight(0xfff1dd, 0.9);
key.position.set(4, 7, 9);
scene.add(key);
// Colored accent lights: narrow spots aimed upward at the cabinet so they never hit the floor
const RED_BASE = 55;
const redFill = new THREE.SpotLight(0xff2a3a, RED_BASE, 0, 0.3, 0.6, 2);
redFill.position.set(-5, 0.5, 4);
redFill.target.position.set(0, 2.5, 0);
scene.add(redFill, redFill.target);
const blueRim = new THREE.SpotLight(0x3a7bff, 45, 0, 0.3, 0.6, 2);
blueRim.position.set(5, 1, -3);
blueRim.target.position.set(0, 2.5, 0);
scene.add(blueRim, blueRim.target);

const composer = new EffectComposer(renderer);
composer.addPass(new RenderPass(scene, camera));
const bloom = new UnrealBloomPass(new THREE.Vector2(innerWidth, innerHeight), 0.75, 0.5, 1.2);
composer.addPass(bloom);
composer.addPass(new OutputPass());

/* ------------------------------------------------------------------ */
/* Materials                                                           */
/* ------------------------------------------------------------------ */
const paint = new THREE.MeshPhysicalMaterial({
	color: 0x8e0f18, metalness: 0.35, roughness: 0.38, clearcoat: 1, clearcoatRoughness: 0.08
});
const chrome = new THREE.MeshStandardMaterial({ color: 0xe8e8ec, metalness: 1, roughness: 0.14 });
const darkMetal = new THREE.MeshStandardMaterial({ color: 0x1a1c21, metalness: 0.7, roughness: 0.4 });
const blackMat = new THREE.MeshStandardMaterial({ color: 0x050506, roughness: 0.9 });

/* ------------------------------------------------------------------ */
/* Helpers                                                             */
/* ------------------------------------------------------------------ */
function roundedRect(path, x, y, w, h, r) {
	path.moveTo(x + r, y);
	path.lineTo(x + w - r, y);
	path.quadraticCurveTo(x + w, y, x + w, y + r);
	path.lineTo(x + w, y + h - r);
	path.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
	path.lineTo(x + r, y + h);
	path.quadraticCurveTo(x, y + h, x, y + h - r);
	path.lineTo(x, y + r);
	path.quadraticCurveTo(x, y, x + r, y);
	return path;
}

function frameMesh(cx, cy, w, h, border, depth, z, mat) {
	const s = roundedRect(new THREE.Shape(), cx - w / 2 - border, cy - h / 2 - border, w + border * 2, h + border * 2, border * 1.2);
	s.holes.push(roundedRect(new THREE.Path(), cx - w / 2, cy - h / 2, w, h, 0.03));
	const g = new THREE.ExtrudeGeometry(s, { depth, bevelEnabled: false, curveSegments: 8 });
	const m = new THREE.Mesh(g, mat);
	m.position.z = z;
	return m;
}

function canvasTex(canvas) {
	const t = new THREE.CanvasTexture(canvas);
	t.colorSpace = THREE.SRGBColorSpace;
	t.anisotropy = renderer.capabilities.getMaxAnisotropy();
	return t;
}

/* ------------------------------------------------------------------ */
/* Cabinet                                                             */
/* ------------------------------------------------------------------ */
const WIN_Y = 0.95, WIN_H = 1.7, REEL_W = 1.4, WIN_W = 1.46;
const REEL_X = [-0.86, 0.86];
const BEVEL = 0.08;
const FRONT_Z = 1.3 + BEVEL;

const machine = new THREE.Group();
scene.add(machine);

{
	const s = roundedRect(new THREE.Shape(), -2, -2.7, 4, 5.7, 0.25);
	for (const x of REEL_X) {
		s.holes.push(roundedRect(new THREE.Path(),
			x - WIN_W / 2 - BEVEL, WIN_Y - WIN_H / 2 - BEVEL, WIN_W + BEVEL * 2, WIN_H + BEVEL * 2, 0.1));
	}
	const g = new THREE.ExtrudeGeometry(s, {
		depth: 2.5, bevelEnabled: true, bevelThickness: BEVEL, bevelSize: BEVEL, bevelSegments: 4, curveSegments: 12
	});
	const body = new THREE.Mesh(g, paint);
	body.position.z = -1.2;
	machine.add(body);
	
	// dark back plate behind reels
	const back = new THREE.Mesh(new THREE.PlaneGeometry(3.6, 2.2), blackMat);
	back.position.set(0, WIN_Y, -0.2);
	machine.add(back);
	
	// chrome frames around windows
	for (const x of REEL_X) {
		machine.add(frameMesh(x, WIN_Y, WIN_W, WIN_H, 0.07, 0.05, FRONT_Z, chrome));
	}
	
	// payline arrows
	const arrowMat = new THREE.MeshStandardMaterial({ color: 0xff2030, emissive: 0xff2030, emissiveIntensity: 2.5 });
	for (const side of [-1, 1]) {
		const a = new THREE.Mesh(new THREE.ConeGeometry(0.09, 0.18, 3), arrowMat);
		a.rotation.z = side < 0 ? -Math.PI / 2 : Math.PI / 2;
		a.position.set(side * 1.76, WIN_Y, FRONT_Z + 0.06);
		machine.add(a);
	}
	
	// marquee
	const mq = new THREE.Mesh(new RoundedBoxGeometry(4.4, 1.6, 1.4, 4, 0.18), paint);
	mq.position.set(0, 3.8, 0.3);
	machine.add(mq);
	const mqTrim = new THREE.Mesh(new RoundedBoxGeometry(4.5, 0.12, 1.5, 3, 0.05), chrome);
	mqTrim.position.set(0, 3.06, 0.25);
	machine.add(mqTrim);
	
	// control ledge
	const ledge = new THREE.Mesh(new RoundedBoxGeometry(3.8, 0.24, 0.9, 3, 0.07), darkMetal);
	ledge.position.set(0, -1.68, 1.7);
	ledge.rotation.x = 0.18;
	machine.add(ledge);
	
	// coin tray
	const tray = new THREE.Mesh(new RoundedBoxGeometry(1.6, 0.42, 0.5, 3, 0.08), chrome);
	tray.position.set(0, -2.3, 1.45);
	machine.add(tray);
	const trayIn = new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.26, 0.1), blackMat);
	trayIn.position.set(0, -2.28, 1.66);
	machine.add(trayIn);
	
	// plinth + floor
	const plinth = new THREE.Mesh(new RoundedBoxGeometry(4.5, 0.22, 3.0, 3, 0.06), darkMetal);
	plinth.position.set(0, -2.86, 0.05);
	machine.add(plinth);
	// matte floor that fades out into the background
	const fadeCanvas = document.createElement('canvas');
	fadeCanvas.width = fadeCanvas.height = 256;
	const fc = fadeCanvas.getContext('2d');
	const fg = fc.createRadialGradient(128, 128, 0, 128, 128, 128);
	fg.addColorStop(0, '#fff');
	fg.addColorStop(0.35, '#888');
	fg.addColorStop(1, '#000');
	fc.fillStyle = fg;
	fc.fillRect(0, 0, 256, 256);
	const floor = new THREE.Mesh(new THREE.PlaneGeometry(24, 24),
		new THREE.MeshStandardMaterial({
			color: 0x15171c, roughness: 0.7, metalness: 0.1,
			alphaMap: new THREE.CanvasTexture(fadeCanvas), transparent: true, depthWrite: false
		}));
	floor.position.z = 0.5;
	floor.rotation.x = -Math.PI / 2;
	floor.position.y = -2.97;
	scene.add(floor);
}

/* ------------------------------------------------------------------ */
/* Marquee sign                                                        */
/* ------------------------------------------------------------------ */
const marqueeCanvas = document.createElement('canvas');
marqueeCanvas.width = 1024;
marqueeCanvas.height = 300;
{
	const c = marqueeCanvas.getContext('2d');
	c.fillStyle = '#0a0003';
	c.fillRect(0, 0, 1024, 300);
	c.textAlign = 'center';
	c.textBaseline = 'middle';
	c.font = '900 150px "Arial Black", Impact, system-ui, sans-serif';
	c.shadowColor = '#ff2040';
	c.shadowBlur = 30;
	c.fillStyle = '#ff3b4f';
	c.fillText('CVE SLOTS', 512, 140);
	c.shadowBlur = 0;
	c.lineWidth = 3;
	c.strokeStyle = '#ffd0d6';
	c.strokeText('CVE SLOTS', 512, 140);
	c.font = '700 34px ui-monospace, Menlo, monospace';
	c.fillStyle = '#ffcf4a';
	c.fillText('☠  EVERY PULL A ZERO-DAY  ☠', 512, 255);
}
const marquee = new THREE.Mesh(new THREE.PlaneGeometry(3.6, 1.05),
	new THREE.MeshBasicMaterial({ map: canvasTex(marqueeCanvas), color: new THREE.Color(1.7, 1.7, 1.7) }));
marquee.position.set(0, 3.8, 1.01);
machine.add(marquee);

/* ------------------------------------------------------------------ */
/* LED result screen                                                   */
/* ------------------------------------------------------------------ */
const SCR_W = 1024, SCR_H = 340;
const screenCanvas = document.createElement('canvas');
screenCanvas.width = SCR_W;
screenCanvas.height = SCR_H;
const sctx = screenCanvas.getContext('2d');
const screenTex = canvasTex(screenCanvas);
const screen = new THREE.Mesh(new THREE.PlaneGeometry(3.3, 3.3 * SCR_H / SCR_W),
	new THREE.MeshBasicMaterial({ map: screenTex, color: new THREE.Color(1.5, 1.5, 1.5) }));
screen.position.set(0, -0.82, FRONT_Z + 0.005);
machine.add(screen);
machine.add(frameMesh(0, -0.82, 3.3, 3.3 * SCR_H / SCR_W, 0.06, 0.05, FRONT_Z - 0.01, chrome));

/* ------------------------------------------------------------------ */
/* Bulbs                                                               */
/* ------------------------------------------------------------------ */
const bulbs = [];
const bulbGeo = new THREE.SphereGeometry(0.055, 16, 12);

function addBulb(x, y, z, color) {
	const m = new THREE.MeshStandardMaterial({ color, emissive: color, emissiveIntensity: 0.3, roughness: 0.3 });
	const b = new THREE.Mesh(bulbGeo, m);
	b.position.set(x, y, z);
	machine.add(b);
	bulbs.push(m);
}

function bulbRect(x0, x1, y0, y1, z, spacing) {
	const pts = [];
	const along = (ax, ay, bx, by) => {
		const len = Math.hypot(bx - ax, by - ay);
		const n = Math.round(len / spacing);
		for (let i = 0; i < n; i++) {
			pts.push([ax + (bx - ax) * i / n, ay + (by - ay) * i / n]);
		}
	};
	along(x0, y1, x1, y1);
	along(x1, y1, x1, y0);
	along(x1, y0, x0, y0);
	along(x0, y0, x0, y1);
	pts.forEach(([x, y], i) => addBulb(x, y, z, i % 2 ? 0xffb02e : 0xff3040));
}

bulbRect(-1.92, 1.92, WIN_Y - 1.08, WIN_Y + 1.08, FRONT_Z + 0.04, 0.24);
bulbRect(-2.0, 2.0, 3.22, 4.38, 1.04, 0.25);

/* ------------------------------------------------------------------ */
/* Reels                                                               */
/* ------------------------------------------------------------------ */
const N = 16;                         // panels per reel
const STEP = TAU / N;
const R = 1.25;
const PANEL_H = 2 * R * Math.tan(Math.PI / N);
const CW = 640, CH = Math.round(CW * PANEL_H / REEL_W);
const FONT = '"Arial Black", Impact, system-ui, sans-serif';
const panelGeo = new THREE.PlaneGeometry(REEL_W, PANEL_H);

function drawPanel(canvas, label, right) {
	const c = canvas.getContext('2d');
	const g = c.createLinearGradient(0, 0, 0, CH);
	g.addColorStop(0, '#d8ccae');
	g.addColorStop(0.5, '#f7f0dc');
	g.addColorStop(1, '#d8ccae');
	c.fillStyle = g;
	c.fillRect(0, 0, CW, CH);
	c.fillStyle = 'rgba(60,30,10,.35)';
	c.fillRect(0, 0, CW, 3);
	c.fillRect(0, CH - 3, CW, 3);
	let size = 104;
	c.font = `900 ${ size }px ${ FONT }`;
	while (c.measureText(label).width > CW * 0.86 && size > 30) {
		size -= 4;
		c.font = `900 ${ size }px ${ FONT }`;
	}
	c.textAlign = 'center';
	c.textBaseline = 'middle';
	c.fillStyle = 'rgba(0,0,0,.18)';
	c.fillText(label, CW / 2 + 4, CH / 2 + 8);
	c.fillStyle = right ? '#c8102e' : '#141a2e';
	c.fillText(label, CW / 2, CH / 2 + 4);
}

function blurInto(src, dst) {
	const c = dst.getContext('2d');
	c.globalCompositeOperation = 'source-over';
	c.globalAlpha = 1;
	c.fillStyle = '#000';
	c.fillRect(0, 0, CW, CH);
	c.globalCompositeOperation = 'lighter';
	const n = 10;
	c.globalAlpha = 1 / n;
	for (let i = 0; i < n; i++) {
		const off = (i / (n - 1) - 0.5) * CH * 0.9;
		c.drawImage(src, 0, off);
		c.drawImage(src, 0, off - CH);
		c.drawImage(src, 0, off + CH);
	}
	c.globalCompositeOperation = 'source-over';
	c.globalAlpha = 1;
}

class Reel {
	constructor(items, x, right) {
		this.items = items;
		this.right = right;
		this.group = new THREE.Group();
		this.group.position.set(x, WIN_Y, 0);
		machine.add(this.group);
		this.panels = [];
		const offset = randInt(items.length);
		for (let i = 0; i < N; i++) {
			const sharp = document.createElement('canvas');
			sharp.width = CW;
			sharp.height = CH;
			const blur = document.createElement('canvas');
			blur.width = CW;
			blur.height = CH;
			const tS = canvasTex(sharp), tB = canvasTex(blur);
			const mat = new THREE.MeshStandardMaterial({
				map: tS, emissiveMap: tS, emissive: 0xffffff, emissiveIntensity: 0.18, roughness: 0.55
			});
			const mesh = new THREE.Mesh(panelGeo, mat);
			const th = i * STEP;
			mesh.position.set(0, R * Math.sin(th), R * Math.cos(th));
			mesh.rotation.x = -th;
			this.group.add(mesh);
			const p = { sharp, blur, tS, tB, mat };
			this.panels.push(p);
			this.setLabel(i, items[(offset + i) % items.length]);
		}
		// rims
		for (const side of [-1, 1]) {
			const rim = new THREE.Mesh(new THREE.CylinderGeometry(R + 0.03, R + 0.03, 0.035, 64), darkMetal);
			rim.rotation.z = Math.PI / 2;
			rim.position.x = side * (REEL_W / 2 + 0.018);
			this.group.add(rim);
		}
		// window shade + glass
		const shadeCanvas = document.createElement('canvas');
		shadeCanvas.width = 4;
		shadeCanvas.height = 256;
		const sc = shadeCanvas.getContext('2d');
		const sg = sc.createLinearGradient(0, 0, 0, 256);
		sg.addColorStop(0, 'rgba(0,0,0,.92)');
		sg.addColorStop(0.3, 'rgba(0,0,0,0)');
		sg.addColorStop(0.7, 'rgba(0,0,0,0)');
		sg.addColorStop(1, 'rgba(0,0,0,.92)');
		sc.fillStyle = sg;
		sc.fillRect(0, 0, 4, 256);
		const shade = new THREE.Mesh(new THREE.PlaneGeometry(WIN_W, WIN_H),
			new THREE.MeshBasicMaterial({ map: canvasTex(shadeCanvas), transparent: true, depthWrite: false }));
		shade.position.set(x, WIN_Y, 1.29);
		machine.add(shade);
		const glass = new THREE.Mesh(new THREE.PlaneGeometry(WIN_W, WIN_H),
			new THREE.MeshPhysicalMaterial({ color: 0xffffff, transparent: true, opacity: 0.07, roughness: 0.05, metalness: 0, depthWrite: false }));
		glass.position.set(x, WIN_Y, 1.33);
		machine.add(glass);
		
		this.angle = 0;
		this.prevAngle = 0;
		this.speed = 0;
		this.blurred = false;
		this.anim = null;
		this.lastTickIdx = 0;
		this.lastTickTime = 0;
	}
	
	setLabel(i, label) {
		const p = this.panels[i];
		drawPanel(p.sharp, label, this.right);
		blurInto(p.sharp, p.blur);
		p.tS.needsUpdate = true;
		p.tB.needsUpdate = true;
	}
	
	setBlur(on) {
		if (on === this.blurred) {
			return;
		}
		this.blurred = on;
		for (const p of this.panels) {
			const t = on ? p.tB : p.tS;
			p.mat.map = t;
			p.mat.emissiveMap = t;
		}
	}
	
	spin(target, delay, duration, extraTurns, onStop) {
		this.angle = ((this.angle % TAU) + TAU) % TAU;
		this.prevAngle = this.angle;
		const k = randInt(N);
		let end = k * STEP;
		const minEnd = this.angle + TAU * extraTurns;
		end += Math.ceil((minEnd - end) / TAU) * TAU;
		this.anim = { t0: performance.now() + delay * 1000, start: this.angle, end, duration, k, target, relabeled: false, stopped: false, onStop };
	}
	
	update(now, dt) {
		const a = this.anim;
		if (a) {
			const t = (now - a.t0) / 1000;
			const WIND = 0.22, BACK = 0.32, OVER = 0.12;
			let ang;
			if (t < 0) {
				ang = a.start;
			} else if (t < WIND) {
				ang = a.start - BACK * Math.sin((t / WIND) * Math.PI / 2);
			} else if (t < WIND + a.duration) {
				const p = (t - WIND) / a.duration;
				const from = a.start - BACK;
				ang = from + (a.end + OVER - from) * (1 - Math.pow(1 - p, 3));
			} else {
				const s = t - WIND - a.duration;
				if (! a.stopped) {
					a.stopped = true;
					sfx.thunk();
					shake = Math.max(shake, 0.05);
				}
				ang = a.end + OVER * Math.exp(-s * 9) * Math.cos(s * 24);
				if (s > 0.55) {
					ang = a.end;
					this.anim = null;
					a.onStop && a.onStop();
				}
			}
			this.angle = ang;
		}
		this.speed = dt > 0 ? Math.abs(this.angle - this.prevAngle) / dt : 0;
		this.prevAngle = this.angle;
		this.setBlur(this.speed > 7);
		
		if (a && ! a.relabeled && this.speed > 10) {
			a.relabeled = true;
			for (let i = 0; i < N; i++) {
				let label;
				if (i === a.k) {
					label = a.target;
				} else {
					do {
						label = pick(this.items);
					} while (label === a.target);
				}
				this.setLabel(i, label);
			}
		}
		
		const idx = Math.floor((this.angle + STEP / 2) / STEP);
		if (idx !== this.lastTickIdx) {
			this.lastTickIdx = idx;
			if (now - this.lastTickTime > 32) {
				this.lastTickTime = now;
				sfx.tick(this.right ? 1.15 : 1);
			}
		}
		this.group.rotation.x = this.angle;
	}
}

const reels = [new Reel(SOFTWARE, REEL_X[0], false), new Reel(EXPLOITS, REEL_X[1], true)];

/* ------------------------------------------------------------------ */
/* Lever                                                               */
/* ------------------------------------------------------------------ */
const LEVER_REST = -0.12, LEVER_MAX = 1.95, LEVER_FIRE = 1.25;
const lever = { angle: LEVER_REST, vel: 0, dragging: false, auto: null, lastRatchet: 0 };
const leverPivot = new THREE.Group();
leverPivot.position.set(2.42, 0.55, 0.2);
machine.add(leverPivot);
{
	const mount = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.22, 0.42, 32), chrome);
	mount.rotation.z = Math.PI / 2;
	mount.position.set(2.24, 0.55, 0.2);
	machine.add(mount);
	const plate = new THREE.Mesh(new RoundedBoxGeometry(0.1, 0.8, 0.8, 3, 0.04), chrome);
	plate.position.set(2.07, 0.55, 0.2);
	machine.add(plate);
	const hub = new THREE.Mesh(new THREE.SphereGeometry(0.16, 24, 16), chrome);
	leverPivot.add(hub);
}
const leverArm = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.065, 2.0, 20), chrome);
leverArm.position.y = 1.0;
leverPivot.add(leverArm);
const leverBall = new THREE.Mesh(new THREE.SphereGeometry(0.25, 32, 24),
	new THREE.MeshPhysicalMaterial({ color: 0xe0101e, roughness: 0.15, clearcoat: 1, clearcoatRoughness: 0.05, emissive: 0x400000 }));
leverBall.position.y = 2.1;
leverPivot.add(leverBall);
const leverHit = new THREE.Mesh(new THREE.CylinderGeometry(0.55, 0.55, 2.9, 8), new THREE.MeshBasicMaterial({ visible: false }));
leverHit.position.y = 1.3;
leverPivot.add(leverHit);

/* ------------------------------------------------------------------ */
/* Big red button                                                      */
/* ------------------------------------------------------------------ */
const buttonGroup = new THREE.Group();
buttonGroup.position.set(0.95, -1.53, 1.74);
buttonGroup.rotation.x = 0.18;
machine.add(buttonGroup);
const buttonRing = new THREE.Mesh(new THREE.CylinderGeometry(0.34, 0.36, 0.08, 40), chrome);
buttonGroup.add(buttonRing);
const buttonMat = new THREE.MeshStandardMaterial({ color: 0xff1a2a, emissive: 0xff1a2a, emissiveIntensity: 1.2, roughness: 0.25 });
const buttonCap = new THREE.Mesh(new THREE.CylinderGeometry(0.26, 0.28, 0.14, 40), buttonMat);
buttonCap.position.y = 0.1;
buttonGroup.add(buttonCap);
let buttonPress = 0;
// decorative small buttons
for (const [x, col] of [[-1.2, 0xffcf4a], [-0.55, 0x3dd68c]]) {
	const g = new THREE.Group();
	g.position.set(x, -1.53, 1.74);
	g.rotation.x = 0.18;
	const ring = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.21, 0.06, 32), chrome);
	const cap = new THREE.Mesh(new THREE.CylinderGeometry(0.15, 0.16, 0.1, 32),
		new THREE.MeshStandardMaterial({ color: col, emissive: col, emissiveIntensity: 0.7, roughness: 0.3 }));
	cap.position.y = 0.07;
	g.add(ring, cap);
	machine.add(g);
}

/* ------------------------------------------------------------------ */
/* Particles                                                           */
/* ------------------------------------------------------------------ */
const MAXP = 700;
const pPos = new Float32Array(MAXP * 3);
const pCol = new Float32Array(MAXP * 3);
const pVel = new Float32Array(MAXP * 3);
const pLife = new Float32Array(MAXP);
const pBase = new Float32Array(MAXP * 3);
for (let i = 0; i < MAXP; i++) {
	pPos[i * 3 + 1] = -100;
}
const pGeo = new THREE.BufferGeometry();
pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
pGeo.setAttribute('color', new THREE.BufferAttribute(pCol, 3));
const spriteCanvas = document.createElement('canvas');
spriteCanvas.width = spriteCanvas.height = 64;
{
	const c = spriteCanvas.getContext('2d');
	const g = c.createRadialGradient(32, 32, 0, 32, 32, 32);
	g.addColorStop(0, 'rgba(255,255,255,1)');
	g.addColorStop(0.3, 'rgba(255,255,255,.8)');
	g.addColorStop(1, 'rgba(255,255,255,0)');
	c.fillStyle = g;
	c.fillRect(0, 0, 64, 64);
}
const particles = new THREE.Points(pGeo, new THREE.PointsMaterial({
	size: 0.13, map: new THREE.CanvasTexture(spriteCanvas), vertexColors: true,
	transparent: true, depthWrite: false, blending: THREE.AdditiveBlending
}));
particles.frustumCulled = false;
scene.add(particles);
let pNext = 0;
const PCOLORS = [[3, 0.6, 0.1], [3, 0.15, 0.2], [0.3, 2.5, 1.2], [2.5, 2.2, 1.6]];

function burst(n, origin) {
	for (let j = 0; j < n; j++) {
		const i = pNext;
		pNext = (pNext + 1) % MAXP;
		pPos[i * 3] = origin.x + rand(-1.4, 1.4);
		pPos[i * 3 + 1] = origin.y + rand(-0.2, 0.2);
		pPos[i * 3 + 2] = origin.z;
		pVel[i * 3] = rand(-3, 3);
		pVel[i * 3 + 1] = rand(3, 8);
		pVel[i * 3 + 2] = rand(0.5, 3.5);
		const col = pick(PCOLORS);
		pBase.set(col, i * 3);
		pLife[i] = rand(1.2, 2.2);
	}
}

function updateParticles(dt) {
	for (let i = 0; i < MAXP; i++) {
		if (pLife[i] <= 0) {
			continue;
		}
		pLife[i] -= dt;
		pVel[i * 3 + 1] -= 9 * dt;
		pPos[i * 3] += pVel[i * 3] * dt;
		pPos[i * 3 + 1] += pVel[i * 3 + 1] * dt;
		pPos[i * 3 + 2] += pVel[i * 3 + 2] * dt;
		if (pPos[i * 3 + 1] < -2.95) {
			pPos[i * 3 + 1] = -2.95;
			pVel[i * 3 + 1] *= -0.35;
			pVel[i * 3] *= 0.7;
		}
		const f = clamp(pLife[i], 0, 1);
		pCol[i * 3] = pBase[i * 3] * f;
		pCol[i * 3 + 1] = pBase[i * 3 + 1] * f;
		pCol[i * 3 + 2] = pBase[i * 3 + 2] * f;
		if (pLife[i] <= 0) {
			pPos[i * 3 + 1] = -100;
		}
	}
	pGeo.attributes.position.needsUpdate = true;
	pGeo.attributes.color.needsUpdate = true;
}

/* ------------------------------------------------------------------ */
/* Sound                                                               */
/* ------------------------------------------------------------------ */
let actx = null, master = null, muted = false;

function initAudio() {
	if (! actx) {
		const AC = window.AudioContext || window.webkitAudioContext;
		if (! AC) {
			return;
		}
		actx = new AC();
		master = actx.createGain();
		master.gain.value = 0.55;
		master.connect(actx.destination);
	}
	if (actx.state === 'suspended') {
		actx.resume();
	}
	if (! actx.unlocked) {
		// iOS: play a silent buffer inside the gesture to unlock audio
		const src = actx.createBufferSource();
		src.buffer = actx.createBuffer(1, 1, 22050);
		src.connect(actx.destination);
		src.start(0);
		actx.unlocked = true;
	}
}

function tone(freq, dur, type = 'square', vol = 0.1, when = 0, slideTo = null) {
	if (muted || ! actx) {
		return;
	}
	const t = actx.currentTime + when;
	const o = actx.createOscillator(), g = actx.createGain();
	o.type = type;
	o.frequency.setValueAtTime(freq, t);
	if (slideTo) {
		o.frequency.exponentialRampToValueAtTime(slideTo, t + dur);
	}
	g.gain.setValueAtTime(vol, t);
	g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
	o.connect(g).connect(master);
	o.start(t);
	o.stop(t + dur + 0.02);
}

function noise(dur, vol, freq, when = 0) {
	if (muted || ! actx) {
		return;
	}
	const t = actx.currentTime + when;
	const len = Math.ceil(actx.sampleRate * dur);
	const buf = actx.createBuffer(1, len, actx.sampleRate);
	const d = buf.getChannelData(0);
	for (let i = 0; i < len; i++) {
		d[i] = (Math.random() * 2 - 1) * (1 - i / len);
	}
	const src = actx.createBufferSource();
	src.buffer = buf;
	const f = actx.createBiquadFilter();
	f.type = 'lowpass';
	f.frequency.value = freq;
	const g = actx.createGain();
	g.gain.value = vol;
	src.connect(f).connect(g).connect(master);
	src.start(t);
}

const sfx = {
	tick: (p = 1) => tone(2200 * p, 0.012, 'square', 0.025),
	ratchet: () => {
		tone(900, 0.02, 'square', 0.05);
		noise(0.03, 0.15, 3000);
	},
	clunk: () => {
		noise(0.14, 0.5, 700);
		tone(120, 0.18, 'sine', 0.45, 0, 45);
	},
	thunk: () => {
		tone(170, 0.15, 'triangle', 0.5, 0, 55);
		noise(0.07, 0.35, 1600);
	},
	pager: () => {
		for (let r = 0; r < 2; r++) {
			for (let i = 0; i < 3; i++) {
				tone(2900, 0.07, 'square', 0.07, r * 0.55 + i * 0.12);
			}
		}
	},
	jackpot: () => {
		[523, 659, 784, 1047].forEach((f, i) => tone(f, 0.25, 'square', 0.06, 1.1 + i * 0.1));
	}
};

/* ------------------------------------------------------------------ */
/* Game state                                                          */
/* ------------------------------------------------------------------ */
let state = 'idle';           // idle | spinning | result
let result = null;
let resultTime = 0;
let winFlashUntil = 0;
let shake = 0;
let count = 0;
const history = [];
let pendingStops = 0;

// Last 2 results per reel; these can't come up again until 2 more pulls have passed
const recent = { sw: [], ex: [] };

function pickFresh(list, used) {
	const pool = list.filter(x => ! used.includes(x));
	const choice = pick(pool);
	used.push(choice);
	if (used.length > 2) {
		used.shift();
	}
	return choice;
}

function fakeCVE() {
	return `CVE-2026-${ String(10000 + randInt(89999)) }`;
}

function startSpin(fromLever = false) {
	if (state === 'spinning') {
		return;
	}
	initAudio();
	state = 'spinning';
	if (! fromLever) {
		lever.auto = { t: 0 };
	}
	buttonPress = 1;
	sfx.clunk();
	
	const sw = pickFresh(SOFTWARE, recent.sw), ex = pickFresh(EXPLOITS, recent.ex);
	const jackpot = Math.random() < 1 / 12;
	result = {
		name: sw + ex,
		cve: fakeCVE(),
		score: jackpot ? '10.0' : rand(8, 9.9).toFixed(1),
		tagline: jackpot ? 'JACKPOT: unauthenticated RCE. Wormable.' : pick(TAGLINES),
		jackpot
	};
	pendingStops = 2;
	const done = () => {
		if (--pendingStops === 0) {
			finishSpin();
		}
	};
	reels[0].spin(sw, 0.12, 1.7, 3, done);
	reels[1].spin(ex, 0.2, 2.4 + rand(0, 0.7), 4, done);
}

function finishSpin() {
	state = 'result';
	resultTime = performance.now();
	winFlashUntil = resultTime + (result.jackpot ? 3500 : 1800);
	count++;
	document.getElementById('count').textContent = count;
	burst(result.jackpot ? 600 : 260, new THREE.Vector3(0, -0.7, FRONT_Z + 0.2));
	shake = result.jackpot ? 0.18 : 0.08;
	sfx.pager();
	if (result.jackpot) {
		sfx.jackpot();
	}
	history.unshift(`${ result.cve }  ${ result.name }  (${ result.score })`);
	history.length = Math.min(history.length, 8);
	document.getElementById('history').innerHTML =
		'<div class="h-title">INCIDENT LOG</div>' + history.map(h => `<div class="row">${ h }</div>`).join('');
}

/* ------------------------------------------------------------------ */
/* Screen drawing                                                      */
/* ------------------------------------------------------------------ */
let lastScreenDraw = 0;

function fitFont(c, text, weight, maxSize, maxW, family) {
	let s = maxSize;
	c.font = `${ weight } ${ s }px ${ family }`;
	while (c.measureText(text).width > maxW && s > 16) {
		s -= 2;
		c.font = `${ weight } ${ s }px ${ family }`;
	}
	return s;
}

function drawScreen(now) {
	if (now - lastScreenDraw < 40) {
		return;
	}
	lastScreenDraw = now;
	const c = sctx;
	const MONO = 'ui-monospace, SFMono-Regular, Menlo, Consolas, monospace';
	c.fillStyle = '#010302';
	c.fillRect(0, 0, SCR_W, SCR_H);
	c.textAlign = 'center';
	c.textBaseline = 'middle';
	
	if (state === 'idle') {
		c.fillStyle = '#ffcf4a';
		c.font = `700 30px ${ MONO }`;
		c.fillText('SYSTEM SECURE (FOR NOW)', SCR_W / 2, 100);
		if (Math.floor(now / 550) % 2 === 0) {
			c.shadowColor = '#3dd68c';
			c.shadowBlur = 20;
			c.fillStyle = '#3dd68c';
			c.font = `900 64px ${ MONO }`;
			c.fillText('PULL THE LEVER ▸', SCR_W / 2, 200);
			c.shadowBlur = 0;
		}
	} else if (state === 'spinning') {
		c.fillStyle = '#ffcf4a';
		c.font = `700 30px ${ MONO }`;
		const dots = '.'.repeat(1 + Math.floor(now / 250) % 3);
		c.fillText('SCANNING THE INTERNET' + dots.padEnd(3, ' '), SCR_W / 2, 70);
		c.font = `700 26px ${ MONO }`;
		c.fillStyle = 'rgba(61,214,140,.55)';
		for (let row = 0; row < 4; row++) {
			let s = '';
			for (let i = 0; i < 40; i++) {
				s += '0123456789abcdef'[randInt(16)];
			}
			c.fillText(s.replace(/(.{4})/g, '$1 '), SCR_W / 2, 140 + row * 44);
		}
	} else if (state === 'result' && result) {
		const t = now - resultTime;
		const chars = Math.floor(t / 55);
		c.fillStyle = result.jackpot && Math.floor(now / 200) % 2 ? '#ff3b4f' : '#ffcf4a';
		c.font = `700 28px ${ MONO }`;
		c.fillText(result.jackpot ? '★ JACKPOT ★  TODAY\'S ZERO-DAY' : 'TODAY\'S ZERO-DAY', SCR_W / 2, 48);
		const name = result.name.slice(0, chars);
		fitFont(c, result.name, 900, 104, SCR_W * 0.9, FONT);
		c.shadowColor = '#3dd68c';
		c.shadowBlur = 24;
		c.fillStyle = '#5dffb0';
		c.fillText(name + (chars < result.name.length && Math.floor(now / 120) % 2 ? '█' : ''), SCR_W / 2, 135);
		c.shadowBlur = 0;
		if (chars >= result.name.length) {
			const t2 = t - result.name.length * 55;
			c.font = `700 30px ${ MONO }`;
			c.fillStyle = '#ff4d5e';
			c.fillText(`${ result.cve } · CVSS ${ result.score } CRITICAL`, SCR_W / 2, 220);
			const tag = result.tagline.slice(0, Math.max(0, Math.floor((t2 - 300) / 25)));
			c.fillStyle = '#e6e6e6';
			fitFont(c, result.tagline, 'italic 600', 30, SCR_W * 0.92, MONO);
			c.fillText(tag, SCR_W / 2, 284);
		}
	}
	// scanlines + vignette
	c.fillStyle = 'rgba(0,0,0,.28)';
	for (let y = 0; y < SCR_H; y += 4) {
		c.fillRect(0, y, SCR_W, 2);
	}
	const v = c.createRadialGradient(SCR_W / 2, SCR_H / 2, SCR_H * 0.3, SCR_W / 2, SCR_H / 2, SCR_W * 0.6);
	v.addColorStop(0, 'rgba(0,0,0,0)');
	v.addColorStop(1, 'rgba(0,0,0,.6)');
	c.fillStyle = v;
	c.fillRect(0, 0, SCR_W, SCR_H);
	screenTex.needsUpdate = true;
}

/* ------------------------------------------------------------------ */
/* Input                                                               */
/* ------------------------------------------------------------------ */
const raycaster = new THREE.Raycaster();
const pointer = new THREE.Vector2();
const parallax = { x: 0, y: 0, tx: 0, ty: 0 };
let dragStartY = 0, dragStartAngle = 0;

function hitTest(e) {
	pointer.x = (e.clientX / innerWidth) * 2 - 1;
	pointer.y = -(e.clientY / innerHeight) * 2 + 1;
	raycaster.setFromCamera(pointer, camera);
	const hits = raycaster.intersectObjects([leverHit, leverBall, leverArm, buttonCap, buttonRing], false);
	if (hits.length) {
		const o = hits[0].object;
		return (o === buttonCap || o === buttonRing) ? 'button' : 'lever';
	}
	return raycaster.intersectObject(machine, true).length ? 'machine' : null;
}

renderer.domElement.addEventListener('pointerdown', (e) => {
	initAudio();
	const h = hitTest(e);
	if (h === 'lever' && state !== 'spinning' && ! lever.auto) {
		lever.dragging = true;
		dragStartY = e.clientY;
		dragStartAngle = lever.angle;
		lever.lastRatchet = lever.angle;
		renderer.domElement.setPointerCapture(e.pointerId);
		renderer.domElement.style.cursor = 'grabbing';
	} else if (h === 'button') {
		startSpin(false);
	}
});
renderer.domElement.addEventListener('pointermove', (e) => {
	if (e.pointerType === 'mouse') {
		parallax.tx = (e.clientX / innerWidth) * 2 - 1;
		parallax.ty = (e.clientY / innerHeight) * 2 - 1;
	}
	if (lever.dragging) {
		const pxPerRad = innerHeight * 0.22;
		lever.angle = clamp(dragStartAngle + (e.clientY - dragStartY) / pxPerRad, LEVER_REST, LEVER_MAX);
		lever.vel = 0;
		if (Math.abs(lever.angle - lever.lastRatchet) > 0.16) {
			lever.lastRatchet = lever.angle;
			sfx.ratchet();
		}
		return;
	}
	if (e.pointerType !== 'mouse') {
		return;
	}
	const h = hitTest(e);
	renderer.domElement.style.cursor = h === 'lever' ? 'grab' : h ? 'pointer' : 'default';
});

function endDrag() {
	if (! lever.dragging) {
		return;
	}
	lever.dragging = false;
	renderer.domElement.style.cursor = 'default';
	if (lever.angle >= LEVER_FIRE) {
		startSpin(true);
	} else if (lever.angle > 0.3 && state !== 'spinning') {
		// a half-hearted pull: click the lever, get the full pull
		lever.auto = { t: 0, from: lever.angle };
		startSpin(true);
	}
}

renderer.domElement.addEventListener('pointerup', endDrag);
renderer.domElement.addEventListener('pointercancel', endDrag);
// quick click on the lever (no drag) = full pull
renderer.domElement.addEventListener('click', (e) => {
	if (state === 'spinning' || lever.auto) {
		return;
	}
	const h = hitTest(e);
	if ((h === 'lever' && lever.angle < 0.3) || h === 'machine') {
		startSpin(false);
	}
});
addEventListener('keydown', (e) => {
	if (e.code === 'Space' || e.code === 'Enter') {
		e.preventDefault();
		startSpin(false);
	}
});
const muteBtn = document.getElementById('mute');
muteBtn.addEventListener('click', () => {
	muted = ! muted;
	muteBtn.textContent = muted ? '🔇 Sound off' : '🔊 Sound on';
	initAudio();
});

/* ------------------------------------------------------------------ */
/* Resize                                                              */
/* ------------------------------------------------------------------ */
let baseDist = 12;

function resize() {
	const a = innerWidth / innerHeight;
	camera.aspect = a;
	const vf = THREE.MathUtils.degToRad(camera.fov);
	const needH = 8.6, needW = a < 1 ? 5.7 : 6.4;
	// Fit to the viewport, but never draw the machine taller than MAX_MACHINE_PX (CSS pixels)
	const MAX_MACHINE_PX = 860;
	const fitDist = Math.max(needH / 2 / Math.tan(vf / 2), needW / 2 / Math.tan(vf / 2) / a);
	const capDist = (needH * innerHeight) / (MAX_MACHINE_PX * 2 * Math.tan(vf / 2));
	baseDist = Math.max(fitDist, capDist);
	scene.fog.near = baseDist + 4;
	scene.fog.far = baseDist + 22;
	camera.updateProjectionMatrix();
	renderer.setSize(innerWidth, innerHeight);
	composer.setSize(innerWidth, innerHeight);
	bloom.resolution.set(innerWidth, innerHeight);
}

addEventListener('resize', resize);
addEventListener('orientationchange', () => setTimeout(resize, 200));
if (window.visualViewport) {
	visualViewport.addEventListener('resize', resize);
}
resize();

/* ------------------------------------------------------------------ */
/* Loop                                                                */
/* ------------------------------------------------------------------ */
let last = performance.now();

function frame(now) {
	const dt = Math.min(0.05, (now - last) / 1000);
	last = now;
	
	// lever
	if (lever.auto) {
		lever.auto.t += dt;
		const from = lever.auto.from ?? LEVER_REST;
		const p = Math.min(1, lever.auto.t / 0.16);
		lever.angle = from + (LEVER_MAX - from) * p * p;
		if (p >= 1) {
			lever.auto = null;
			lever.vel = 0;
		}
	} else if (! lever.dragging) {
		lever.vel += (-55 * (lever.angle - LEVER_REST) - 7 * lever.vel) * dt;
		lever.angle += lever.vel * dt;
	}
	leverPivot.rotation.x = lever.angle;
	
	// button
	buttonPress = Math.max(0, buttonPress - dt * 5);
	buttonCap.position.y = 0.1 - 0.06 * Math.min(1, buttonPress * 2);
	buttonMat.emissiveIntensity = state === 'spinning' ? 0.5 : 1 + 0.8 * (0.5 + 0.5 * Math.sin(now / 300));
	
	// reels
	for (const r of reels) {
		r.update(now, dt);
	}
	
	// bulbs
	const flashing = now < winFlashUntil;
	const speed = state === 'spinning' ? 70 : 260;
	const phase = Math.floor(now / speed);
	bulbs.forEach((m, i) => {
		let on;
		if (flashing) {
			on = Math.floor(now / 110) % 2 === (i % 2);
		} else if (state === 'spinning') {
			on = (i + phase) % 3 === 0;
		} else {
			on = (i + phase) % 5 === 0 || (i + phase) % 5 === 1;
		}
		m.emissiveIntensity += ((on ? 4.2 : 0.18) - m.emissiveIntensity) * Math.min(1, dt * 25);
	});
	
	drawScreen(now);
	updateParticles(dt);
	
	// camera
	parallax.x += (parallax.tx - parallax.x) * Math.min(1, dt * 3);
	parallax.y += (parallax.ty - parallax.y) * Math.min(1, dt * 3);
	shake = Math.max(0, shake - dt * 0.6);
	const sx = (Math.random() - 0.5) * shake, sy = (Math.random() - 0.5) * shake;
	camera.position.set(
		lookTarget.x + parallax.x * 1.4 + sx,
		lookTarget.y + 0.4 - parallax.y * 0.6 + sy,
		lookTarget.z + baseDist
	);
	camera.lookAt(lookTarget);
	redFill.intensity = RED_BASE + (flashing ? 80 * (Math.floor(now / 110) % 2) : 0);
	
	composer.render();
	requestAnimationFrame(frame);
}

requestAnimationFrame(frame);
</script>
</body>
</html>
