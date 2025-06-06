// --- PANTALLA DE CARGA ANIMADA ---
const colors = ["#007bff", "#ffc107"];
const balls = [];
const ballCount = 32;
const logoTarget = [];
const circleRadius = 65;
let formingCircle = false;
let explode = false;
let logoShown = false;

function setCircleTargets() {
    logoTarget.length = 0;
    const angleStep = (2 * Math.PI) / ballCount;
    for (let i = 0; i < ballCount; i++) {
        const angle = i * angleStep;
        logoTarget.push({
            x: Math.cos(angle) * circleRadius,
            y: Math.sin(angle) * circleRadius,
            color: i % 2
        });
    }
}

function randomBetween(a, b) {
    return a + Math.random() * (b - a);
}

function resizeCanvas() {
    const canvas = document.getElementById('bg-canvas');
    if (!canvas) return;
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}

function initBalls() {
    balls.length = 0;
    const w = window.innerWidth, h = window.innerHeight;
    for (let i = 0; i < ballCount; i++) {
        balls.push({
            x: randomBetween(0, w),
            y: randomBetween(0, h),
            r: randomBetween(18, 22),
            color: colors[i % colors.length],
            vx: 0,
            vy: 0,
            tx: null, ty: null,
            angle: 0,
            speed: 0,
            exploded: false
        });
    }
}

function animateBalls() {
    const canvas = document.getElementById('bg-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    let allArrived = true;
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2 - 30;

    for (let i = 0; i < balls.length; i++) {
        let b = balls[i];

        if (formingCircle && !explode) {
            const t = logoTarget[i];
            b.angle = (b.angle || Math.atan2(b.y - centerY, b.x - centerX));
            b.angle += 0.09;
            b.tx = centerX + t.x * Math.cos(b.angle) - t.y * Math.sin(b.angle);
            b.ty = centerY + t.x * Math.sin(b.angle) + t.y * Math.cos(b.angle);
            b.x += (b.tx - b.x) * 0.35;
            b.y += (b.ty - b.y) * 0.35;
        } else if (explode) {
            if (!b.exploded) {
                const dx = b.x - centerX;
                const dy = b.y - centerY;
                const dist = Math.sqrt(dx*dx + dy*dy) || 1;
                b.vx = (dx / dist) * (7 + Math.random() * 2);
                b.vy = (dy / dist) * (7 + Math.random() * 2);
                b.exploded = true;
            }
            b.x += b.vx;
            b.y += b.vy;
        } else {
            b.x += randomBetween(-1.5, 1.5);
            b.y += randomBetween(-1.5, 1.5);
            allArrived = false;
        }

        if (!logoShown) {
            ctx.beginPath();
            ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
            ctx.fillStyle = b.color;
            ctx.globalAlpha = 0.98;
            ctx.shadowColor = b.color;
            ctx.shadowBlur = 6;
            ctx.fill();
            ctx.globalAlpha = 1;
            ctx.shadowBlur = 0;
        }
    }

    if (!explode && !logoShown) {
        for (let i = 0; i < balls.length; i++) {
            for (let j = i + 1; j < balls.length; j++) {
                const a = balls[i], b = balls[j];
                const dx = a.x - b.x, dy = a.y - b.y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 44) {
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.strokeStyle = a.color;
                    ctx.globalAlpha = 0.07;
                    ctx.lineWidth = 3 - dist/22;
                    ctx.stroke();
                    ctx.globalAlpha = 1;
                }
            }
        }
    }

    if (explode && !logoShown) {
        let outCount = 0;
        for (let b of balls) {
            if (
                b.x < -100 || b.x > canvas.width + 100 ||
                b.y < -100 || b.y > canvas.height + 100
            ) {
                outCount++;
            }
        }
        if (outCount === balls.length) {
            logoShown = true;
            document.getElementById('logo-mextium').classList.add('visible');
        }
    }

    requestAnimationFrame(animateBalls);
}

// Mostrar loader
function mostrarPantallaCarga() {
    const loader = document.getElementById('bg-canvas');
    const logo = document.getElementById('logo-mextium');
    if (loader) loader.style.display = 'block';
    if (logo) {
        logo.classList.remove('visible');
        logoShown = false;
    }
    formingCircle = false;
    explode = false;
    setCircleTargets();
    resizeCanvas();
    initBalls();
    animateBalls();

    setTimeout(() => { formingCircle = true; }, 600);
    setTimeout(() => { explode = true; }, 600 + 1200);
}

// Ocultar loader
function ocultarPantallaCarga() {
    const loader = document.getElementById('bg-canvas');
    const logo = document.getElementById('logo-mextium');
    if (loader) loader.style.display = 'none';
    if (logo) logo.classList.remove('visible');
}

// Opcional: mostrar loader al cargar la página
window.addEventListener('DOMContentLoaded', mostrarPantallaCarga);

// Opcional: ocultar loader cuando la página termine de cargar
window.addEventListener('load', () => {
    setTimeout(ocultarPantallaCarga, 2200); // Ajusta el tiempo según tu animación
});

// Si usas AJAX o navegación SPA, llama a mostrarPantallaCarga() antes de cargar la nueva página
// y a ocultarPantallaCarga() cuando termine.