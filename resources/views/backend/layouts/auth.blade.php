<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_title', 'Aurelia Estates') }} &bull; Executive Portal</title>
    
    @if(setting('site_favicon'))
        <link rel="icon" type="image/x-icon" href="{{ asset_url(setting('site_favicon')) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Cinzel:wght@600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons (FontAwesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <style>
        :root {
            --primary: #D4AF37;
            --primary-hover: #e5c358;
            --primary-glow: rgba(212, 175, 55, 0.35);
            --bg-deep: #07090e;
            --surface: rgba(14, 18, 28, 0.82);
            --surface-card: rgba(16, 21, 33, 0.90);
            --surface-border: rgba(212, 175, 55, 0.25);
            --text-main: #f9fafb;
            --text-muted: #94a3b8;
            --text-subtle: #64748b;
        }
        
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-deep);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Interactive Particle Canvas */
        #particleCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            pointer-events: none;
        }

        /* Subtle luxury blueprint grid */
        .bg-grid-overlay {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(212, 175, 55, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(212, 175, 55, 0.035) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: -3;
            pointer-events: none;
        }

        /* Ambient colored glow orbs */
        .ambient-blob {
            position: fixed;
            filter: blur(140px);
            border-radius: 50%;
            z-index: -4;
            pointer-events: none;
            opacity: 0.55;
            animation: ambientPulse 22s infinite ease-in-out alternate;
        }
        
        .blob-1 {
            width: 50vw;
            height: 50vw;
            max-width: 600px;
            max-height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.25) 0%, rgba(212, 175, 55, 0) 70%);
            top: -15%;
            left: -10%;
        }
        
        .blob-2 {
            width: 45vw;
            height: 45vw;
            max-width: 550px;
            max-height: 550px;
            background: radial-gradient(circle, rgba(20, 60, 120, 0.35) 0%, rgba(20, 60, 120, 0) 70%);
            bottom: -15%;
            right: -8%;
            animation-delay: -11s;
        }

        @keyframes ambientPulse {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(5%, 6%) scale(1.08); }
            100% { transform: translate(-4%, -5%) scale(0.96); }
        }

        /* Top Bar Status */
        .auth-top-bar {
            width: 100%;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            z-index: 10;
        }

        .system-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 4px 12px;
            border-radius: 20px;
            color: #cbd5e1;
            font-weight: 500;
        }

        .dot-live {
            width: 7px;
            height: 7px;
            background-color: #10b981;
            border-radius: 50%;
            position: relative;
        }

        .dot-live::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background-color: rgba(16, 185, 129, 0.6);
            animation: dotGlow 2s infinite;
        }

        @keyframes dotGlow {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(2.8); opacity: 0; }
        }

        @yield('styles')
    </style>
</head>
<body>
    <canvas id="particleCanvas"></canvas>
    <div class="bg-grid-overlay"></div>
    <div class="ambient-blob blob-1"></div>
    <div class="ambient-blob blob-2"></div>

    <!-- Top Ambient Bar -->
    <header class="auth-top-bar d-none d-md-flex">
        <div class="system-pill">
            <span class="dot-live"></span>
            <span>SYSTEM ENCLAVE: <strong>OPERATIONAL</strong></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span id="topLiveClock" style="font-family: monospace; letter-spacing: 1px; color: var(--primary);">--:--:--</span>
            <span class="text-secondary">&bull;</span>
            <span>AUTH v2.8 &bull; 256-BIT SSL</span>
        </div>
    </header>

    <main class="w-100 py-3 py-md-4 d-flex align-items-center justify-content-center flex-grow-1">
        @yield('content')
    </main>

    <!-- Bottom Mini Note -->
    <footer class="text-center py-2 text-secondary" style="font-size: 0.7rem; letter-spacing: 0.5px; opacity: 0.7;">
        &copy; {{ date('Y') }} {{ setting('site_title', 'Aurelia Estates') }} &bull; Proprietary Luxury Real Estate Management Engine
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Realtime Clock
        function updateTopClock() {
            const clockEl = document.getElementById('topLiveClock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString();
            }
        }
        setInterval(updateTopClock, 1000);
        updateTopClock();

        // High-end ambient constellation particles
        (function() {
            const canvas = document.getElementById('particleCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            const particleCount = Math.min(width < 768 ? 25 : 45, 50);

            class Particle {
                constructor() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.vx = (Math.random() - 0.5) * 0.45;
                    this.vy = (Math.random() - 0.5) * 0.45;
                    this.radius = Math.random() * 1.8 + 0.8;
                    this.alpha = Math.random() * 0.5 + 0.2;
                }
                update() {
                    this.x += this.vx;
                    this.y += this.vy;
                    if (this.x < 0) this.x = width;
                    if (this.x > width) this.x = 0;
                    if (this.y < 0) this.y = height;
                    if (this.y > height) this.y = 0;
                }
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(212, 175, 55, ${this.alpha})`;
                    ctx.fill();
                }
            }

            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);

                // Draw connecting constellation filaments
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 130) {
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = `rgba(212, 175, 55, ${(1 - dist / 130) * 0.12})`;
                            ctx.lineWidth = 0.8;
                            ctx.stroke();
                        }
                    }
                }

                particles.forEach(p => {
                    p.update();
                    p.draw();
                });

                requestAnimationFrame(animate);
            }
            animate();
        })();
    </script>
    @stack('scripts')
</body>
</html>