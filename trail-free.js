document.addEventListener('DOMContentLoaded', () => {
    const svg = document.querySelector('svg.trail');
    const path = svg ? svg.querySelector('path') : null;

    if (!path) {
        console.error('Path element not found');
        return;
    }

    let points = [];
    let segments = mtfree_params.trail_length; // Default value
    let speed = mtfree_params.trail_speed; // Default value
    let enabled = mtfree_params.trail_enabled === '1';
    let mouse = {
        x: 0,
        y: 0,
    };

    const move = (event) => {
        if (!enabled) return;

        const x = event.clientX;
        const y = event.clientY;

        mouse.x = x;
        mouse.y = y;

        if (points.length === 0) {
            for (let i = 0; i < segments; i++) {
                points.push({ x: x, y: y });
            }
        }
    };

    const anim = () => {
        if (!enabled) return;

        let px = mouse.x;
        let py = mouse.y;

        points.forEach((p, index) => {
            p.x = px;
            p.y = py;

            let n = points[index + 1];
            if (n) {
                px = px - (p.x - n.x) * speed;
                py = py - (p.y - n.y) * speed;
            }
        });

        const d = `M ${points.map(p => `${p.x} ${p.y}`).join(' L ')}`;

        path.setAttribute('d', d);
        path.style.strokeWidth = mtfree_params.trail_thickness + 'px';
        path.style.opacity = mtfree_params.trail_opacity;
        path.style.stroke = mtfree_params.trail_color;

        requestAnimationFrame(anim);
    };

    const resize = () => {
        const ww = window.innerWidth;
        const wh = window.innerHeight;

        if (svg) {
            svg.style.width = ww + 'px';
            svg.style.height = wh + 'px';
            svg.setAttribute('viewBox', `0 0 ${ww} ${wh}`);
        }
    };

    document.addEventListener('mousemove', move);
    window.addEventListener('resize', resize);

    anim();
    resize();
});
