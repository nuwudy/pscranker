/**
 * PSCRanker Interactive 3D Globe & 2D Map Engine
 * Tailored for Kerala PSC spatial learning:
 * - World history (WWII German invasion, Mandela's journey)
 * - Strategic geography (Red Sea choke points, Pacific reality)
 * - Kerala geography (44 rivers, Palakkad gap, Western Ghats)
 */
(function(window) {
    'use strict';

    // Simplified continental polygon coordinates [lng, lat]
    const CONTINENTS = {
        northAmerica: [
            [-168, 65], [-160, 70], [-140, 70], [-120, 75], [-90, 70], [-80, 72], [-60, 60],
            [-65, 45], [-75, 35], [-80, 25], [-97, 26], [-90, 20], [-85, 12], [-80, 9],
            [-77, 8], [-83, 10], [-95, 16], [-105, 20], [-115, 30], [-122, 38], [-124, 48],
            [-130, 54], [-140, 60], [-150, 60], [-165, 60], [-168, 65]
        ],
        southAmerica: [
            [-77, 8], [-70, 12], [-60, 10], [-50, 0], [-35, -5], [-35, -12], [-40, -22],
            [-50, -30], [-55, -40], [-65, -55], [-75, -50], [-73, -40], [-71, -30], [-75, -15],
            [-81, -5], [-80, 2], [-77, 8]
        ],
        eurasia: [
            [-9, 36], [-9, 43], [0, 48], [8, 55], [12, 57], [20, 55], [30, 60], [40, 65],
            [60, 70], [80, 73], [100, 75], [120, 74], [140, 70], [170, 66], [180, 65],
            [170, 60], [160, 55], [140, 50], [130, 42], [122, 38], [120, 30], [115, 22],
            [105, 20], [100, 10], [98, 5], [92, 16], [88, 22], [80, 13], [77, 8], [73, 18],
            [68, 24], [60, 25], [55, 26], [50, 29], [48, 30], [44, 13], [40, 20], [35, 30],
            [34, 32], [28, 41], [24, 38], [15, 40], [5, 43], [-3, 37], [-9, 36]
        ],
        africa: [
            [-5, 36], [10, 37], [25, 32], [32, 31], [35, 28], [43, 12], [51, 12], [45, 0],
            [40, -10], [35, -20], [32, -28], [28, -32], [20, -34], [18, -34], [15, -28],
            [12, -15], [9, 4], [0, 6], [-10, 5], [-17, 15], [-12, 28], [-5, 36]
        ],
        australia: [
            [114, -22], [120, -34], [130, -32], [138, -35], [146, -39], [150, -36], [153, -28],
            [146, -18], [142, -11], [136, -12], [130, -14], [123, -16], [114, -22]
        ],
        britishIsles: [
            [-5, 50], [1, 51], [0, 58], [-5, 58], [-6, 54], [-5, 50]
        ],
        japan: [
            [130, 32], [132, 34], [136, 35], [141, 41], [145, 44], [141, 45], [139, 37], [130, 32]
        ],
        greenland: [
            [-45, 60], [-35, 65], [-20, 75], [-30, 83], [-55, 80], [-55, 70], [-45, 60]
        ],
        madagascar: [
            [44, -13], [50, -14], [48, -25], [44, -25], [44, -13]
        ],
        indiaDetailed: [
            [68, 24], [72, 21], [73, 16], [74, 14], [75, 12], [76, 10], [77, 8.1],
            [78, 8.8], [79, 10.5], [80, 13], [82, 16], [86, 20], [89, 22], [88, 24],
            [85, 27], [80, 30], [76, 32], [74, 34], [72, 30], [70, 27], [68, 24]
        ]
    };

    class PscGlobe {
        constructor(canvas, options = {}) {
            this.canvas = canvas;
            this.ctx = canvas.getContext('2d');
            
            // Config options
            this.mode = options.mode || '3d_globe'; // '3d_globe' or '2d_map'
            this.lat = options.centerLat !== undefined ? options.centerLat : 20.0;
            this.lng = options.centerLng !== undefined ? options.centerLng : 78.0;
            this.zoom = options.zoom || 1.4;
            this.autoSpin = options.autoSpin !== undefined ? options.autoSpin : false;
            this.spinSpeed = options.spinSpeed || 0.2;
            this.markers = options.markers || [];
            this.routes = options.routes || [];
            this.activeMarker = options.activeMarker || null;
            this.onMarkerClick = options.onMarkerClick || null;

            // Target coordinates for smooth animation
            this.targetLat = this.lat;
            this.targetLng = this.lng;
            this.targetZoom = this.zoom;

            // Interaction state
            this.isDragging = false;
            this.lastMouseX = 0;
            this.lastMouseY = 0;
            this.animFrameId = null;
            this.pulseTimer = 0;

            // Auto-resize
            this.resize();
            this.bindEvents();
            this.startLoop();
        }

        resize() {
            const rect = this.canvas.getBoundingClientRect();
            const parent = this.canvas.parentElement;
            const dpr = window.devicePixelRatio || 1;
            
            let w = Math.round(rect.width || (parent ? parent.clientWidth : 0) || 600);
            let h = Math.round(rect.height || (parent ? parent.clientHeight : 0) || 420);
            if (h < 280) h = 420;

            this.width = w;
            this.height = h;
            this.canvas.width = Math.round(w * dpr);
            this.canvas.height = Math.round(h * dpr);
            this.ctx.resetTransform();
            this.ctx.scale(dpr, dpr);
        }

        bindEvents() {
            const getPos = (e) => {
                const rect = this.canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return { x: clientX - rect.left, y: clientY - rect.top };
            };

            const onStart = (e) => {
                const pos = getPos(e);
                this.isDragging = true;
                this.lastMouseX = pos.x;
                this.lastMouseY = pos.y;
                this.autoSpin = false; // pause auto-spin on interaction
                if (e.touches && e.touches.length > 1) return; // ignore multi-touch
            };

            const onMove = (e) => {
                if (!this.isDragging) return;
                e.preventDefault();
                const pos = getPos(e);
                const dx = pos.x - this.lastMouseX;
                const dy = pos.y - this.lastMouseY;
                this.lastMouseX = pos.x;
                this.lastMouseY = pos.y;

                const sensitivity = 0.35 / (this.zoom * 0.8);
                this.targetLng -= dx * sensitivity;
                this.targetLat += dy * sensitivity;

                // Clamp latitude to avoid pole flipping
                this.targetLat = Math.max(-85, Math.min(85, this.targetLat));
            };

            const onEnd = (e) => {
                if (!this.isDragging) return;
                this.isDragging = false;
                
                // Check if this was a quick click to pick a marker
                if (e.changedTouches || e.type === 'mouseup') {
                    const clientX = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
                    const clientY = e.changedTouches ? e.changedTouches[0].clientY : e.clientY;
                    const rect = this.canvas.getBoundingClientRect();
                    const clickX = clientX - rect.left;
                    const clickY = clientY - rect.top;
                    this.checkMarkerClick(clickX, clickY);
                }
            };

            this.canvas.addEventListener('mousedown', onStart);
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onEnd);

            this.canvas.addEventListener('touchstart', onStart, { passive: false });
            window.addEventListener('touchmove', onMove, { passive: false });
            window.addEventListener('touchend', onEnd);

            // Wheel zoom
            this.canvas.addEventListener('wheel', (e) => {
                e.preventDefault();
                const zoomFactor = e.deltaY < 0 ? 1.12 : 0.89;
                this.targetZoom = Math.max(0.7, Math.min(6.0, this.targetZoom * zoomFactor));
            }, { passive: false });

            // Window resize observer
            this.resizeHandler = () => this.resize();
            window.addEventListener('resize', this.resizeHandler);
        }

        checkMarkerClick(clickX, clickY) {
            if (!this.markers || !this.markers.length) return;
            const threshold = 18;

            for (let i = 0; i < this.markers.length; i++) {
                const m = this.markers[i];
                const pt = this.project(m.lat, m.lng);
                if (pt && pt.visible) {
                    const dist = Math.hypot(pt.x - clickX, pt.y - clickY);
                    if (dist <= threshold) {
                        this.activeMarker = m;
                        this.flyTo(m.lat, m.lng);
                        if (typeof this.onMarkerClick === 'function') {
                            this.onMarkerClick(m, i);
                        }
                        return;
                    }
                }
            }
        }

        setMode(mode) {
            this.mode = mode === '2d_map' ? '2d_map' : '3d_globe';
        }

        toggleAutoSpin() {
            this.autoSpin = !this.autoSpin;
            return this.autoSpin;
        }

        flyTo(lat, lng, zoom = null) {
            this.targetLat = Math.max(-85, Math.min(85, lat));
            this.targetLng = lng;
            if (zoom !== null) {
                this.targetZoom = zoom;
            }
        }

        resetView() {
            this.targetLat = 20.0;
            this.targetLng = 78.0;
            this.targetZoom = 1.4;
            this.activeMarker = null;
        }

        setMarkers(markers) {
            this.markers = markers || [];
        }

        setRoutes(routes) {
            this.routes = routes || [];
        }

        // Projection math: Lat/Lng -> Canvas (x, y, visible)
        project(lat, lng) {
            const rad = Math.PI / 180;
            const phi = lat * rad;
            const lambda = lng * rad;
            const phi0 = this.lat * rad;
            const lambda0 = this.lng * rad;

            const cx = this.width / 2;
            const cy = this.height / 2;
            const radius = Math.min(this.width, this.height) * 0.42 * this.zoom;

            if (this.mode === '3d_globe') {
                const deltaLambda = lambda - lambda0;
                // Cosine of angular distance from center
                const cosC = Math.sin(phi0) * Math.sin(phi) + Math.cos(phi0) * Math.cos(phi) * Math.cos(deltaLambda);
                
                // Point is behind the globe
                const visible = cosC >= 0.05;

                const x = cx + radius * Math.cos(phi) * Math.sin(deltaLambda);
                const y = cy - radius * (Math.cos(phi0) * Math.sin(phi) - Math.sin(phi0) * Math.cos(phi) * Math.cos(deltaLambda));

                return { x, y, visible, radius };
            } else {
                // 2D Equirectangular projection centered on (this.lng, this.lat)
                let dLng = (lng - this.lng);
                while (dLng > 180) dLng -= 360;
                while (dLng < -180) dLng += 360;

                const scaleX = (this.width * 0.5 * this.zoom) / 180;
                const scaleY = (this.height * 0.5 * this.zoom) / 90;

                const x = cx + dLng * scaleX;
                const y = cy - (lat - this.lat) * scaleY;
                const visible = x >= -50 && x <= this.width + 50 && y >= -50 && y <= this.height + 50;

                return { x, y, visible, radius };
            }
        }

        draw() {
            const ctx = this.ctx;
            const w = this.width;
            const h = this.height;
            const cx = w / 2;
            const cy = h / 2;
            const radius = Math.min(w, h) * 0.42 * this.zoom;

            // Clear background (Deep Space / Maritime Navy)
            ctx.fillStyle = '#060B18';
            ctx.fillRect(0, 0, w, h);

            // Subtle background starlight stars
            this.drawStarfield(ctx, w, h);

            if (this.mode === '3d_globe') {
                // Globe Outer Atmosphere Glow
                const atmoGlow = ctx.createRadialGradient(cx, cy, radius * 0.9, cx, cy, radius * 1.15);
                atmoGlow.addColorStop(0, 'rgba(0, 150, 255, 0.25)');
                atmoGlow.addColorStop(0.6, 'rgba(0, 100, 220, 0.08)');
                atmoGlow.addColorStop(1, 'rgba(0, 0, 0, 0)');
                ctx.fillStyle = atmoGlow;
                ctx.beginPath();
                ctx.arc(cx, cy, radius * 1.15, 0, Math.PI * 2);
                ctx.fill();

                // Globe Ocean Sphere Fill with spherical depth gradient
                const oceanGrad = ctx.createRadialGradient(cx - radius * 0.3, cy - radius * 0.35, radius * 0.1, cx, cy, radius);
                oceanGrad.addColorStop(0, '#0D3866');
                oceanGrad.addColorStop(0.65, '#072445');
                oceanGrad.addColorStop(1, '#021024');

                ctx.save();
                ctx.beginPath();
                ctx.arc(cx, cy, radius, 0, Math.PI * 2);
                ctx.fillStyle = oceanGrad;
                ctx.fill();
                ctx.clip(); // Clip everything to the sphere!

                // Draw Graticules (Lat/Long grid lines)
                this.drawGraticules(ctx, cx, cy, radius);

                // Draw Continents
                this.drawContinents(ctx);

                // Draw Routes / Arcs
                this.drawRoutes(ctx);

                // Restore clip
                ctx.restore();

                // Globe Specular Rim & Shading overlay
                const rimGrad = ctx.createRadialGradient(cx - radius * 0.4, cy - radius * 0.4, radius * 0.2, cx, cy, radius);
                rimGrad.addColorStop(0, 'rgba(255, 255, 255, 0.1)');
                rimGrad.addColorStop(0.8, 'rgba(0, 0, 0, 0)');
                rimGrad.addColorStop(1, 'rgba(0, 0, 0, 0.6)');
                ctx.beginPath();
                ctx.arc(cx, cy, radius, 0, Math.PI * 2);
                ctx.fillStyle = rimGrad;
                ctx.fill();

                // Globe Border Ring
                ctx.beginPath();
                ctx.arc(cx, cy, radius, 0, Math.PI * 2);
                ctx.strokeStyle = 'rgba(100, 180, 255, 0.4)';
                ctx.lineWidth = 1.5;
                ctx.stroke();

            } else {
                // 2D Map Canvas View
                ctx.fillStyle = '#061D36';
                ctx.fillRect(0, 0, w, h);

                // Draw 2D Graticule Grid
                this.drawGraticules2D(ctx, w, h);

                // Draw Continents in 2D
                this.drawContinents(ctx);

                // Draw Routes in 2D
                this.drawRoutes(ctx);
            }

            // Draw Markers (Pulsing Pinpoints)
            this.drawMarkers(ctx);
        }

        drawStarfield(ctx, w, h) {
            ctx.fillStyle = 'rgba(255, 255, 255, 0.35)';
            const count = 35;
            for (let i = 0; i < count; i++) {
                const sx = (Math.sin(i * 997.3) * 0.5 + 0.5) * w;
                const sy = (Math.cos(i * 353.7) * 0.5 + 0.5) * h;
                const size = (i % 3 === 0) ? 1.5 : 1;
                ctx.fillRect(sx, sy, size, size);
            }
        }

        drawGraticules(ctx, cx, cy, radius) {
            ctx.lineWidth = 0.75;

            // Parallels (Latitudes)
            for (let lat = -60; lat <= 60; lat += 20) {
                ctx.beginPath();
                ctx.strokeStyle = (lat === 0) ? 'rgba(255, 215, 0, 0.5)' : 'rgba(100, 170, 255, 0.15)'; // Equator in gold
                ctx.lineWidth = (lat === 0) ? 1.5 : 0.75;

                let first = true;
                for (let lng = -180; lng <= 180; lng += 4) {
                    const pt = this.project(lat, lng);
                    if (pt.visible) {
                        if (first) { ctx.moveTo(pt.x, pt.y); first = false; }
                        else { ctx.lineTo(pt.x, pt.y); }
                    } else {
                        first = true;
                    }
                }
                ctx.stroke();
            }

            // Meridians (Longitudes)
            for (let lng = -180; lng < 180; lng += 30) {
                ctx.beginPath();
                ctx.strokeStyle = (lng === 0) ? 'rgba(0, 255, 200, 0.45)' : 'rgba(100, 170, 255, 0.15)';
                ctx.lineWidth = (lng === 0) ? 1.2 : 0.75;

                let first = true;
                for (let lat = -80; lat <= 80; lat += 4) {
                    const pt = this.project(lat, lng);
                    if (pt.visible) {
                        if (first) { ctx.moveTo(pt.x, pt.y); first = false; }
                        else { ctx.lineTo(pt.x, pt.y); }
                    } else {
                        first = true;
                    }
                }
                ctx.stroke();
            }
        }

        drawGraticules2D(ctx, w, h) {
            ctx.lineWidth = 0.75;
            // Draw lat/long grid lines in 2D mode
            for (let lat = -80; lat <= 80; lat += 20) {
                const pt1 = this.project(lat, -180);
                const pt2 = this.project(lat, 180);
                ctx.beginPath();
                ctx.strokeStyle = (lat === 0) ? 'rgba(255, 215, 0, 0.5)' : 'rgba(100, 170, 255, 0.15)';
                ctx.lineWidth = (lat === 0) ? 1.5 : 0.75;
                ctx.moveTo(pt1.x, pt1.y);
                ctx.lineTo(pt2.x, pt2.y);
                ctx.stroke();
            }
            for (let lng = -180; lng <= 180; lng += 30) {
                const pt1 = this.project(-80, lng);
                const pt2 = this.project(80, lng);
                ctx.beginPath();
                ctx.strokeStyle = (lng === 0) ? 'rgba(0, 255, 200, 0.4)' : 'rgba(100, 170, 255, 0.15)';
                ctx.lineWidth = (lng === 0) ? 1.2 : 0.75;
                ctx.moveTo(pt1.x, pt1.y);
                ctx.lineTo(pt2.x, pt2.y);
                ctx.stroke();
            }
        }

        drawContinents(ctx) {
            ctx.fillStyle = '#1B4D3E'; // Forest green / dark emerald landmasses
            ctx.strokeStyle = '#34D399'; // Emerald coastline border
            ctx.lineWidth = 1.2;

            for (const key in CONTINENTS) {
                const poly = CONTINENTS[key];
                if (!poly || poly.length < 3) continue;

                // Interpolate along polygon edges to curve around globe sphere
                ctx.beginPath();
                let started = false;

                for (let i = 0; i < poly.length; i++) {
                    const nextIdx = (i + 1) % poly.length;
                    const p1 = poly[i];
                    const p2 = poly[nextIdx];

                    // Subdivide segment into steps for curvature fidelity
                    const steps = 6;
                    for (let s = 0; s < steps; s++) {
                        const t = s / steps;
                        const lng = p1[0] + (p2[0] - p1[0]) * t;
                        const lat = p1[1] + (p2[1] - p1[1]) * t;

                        const pt = this.project(lat, lng);
                        if (pt.visible) {
                            if (!started) {
                                ctx.moveTo(pt.x, pt.y);
                                started = true;
                            } else {
                                ctx.lineTo(pt.x, pt.y);
                            }
                        } else {
                            started = false;
                        }
                    }
                }

                ctx.fillStyle = 'rgba(24, 75, 55, 0.85)';
                ctx.fill();
                ctx.strokeStyle = 'rgba(52, 211, 153, 0.7)';
                ctx.stroke();
            }
        }

        drawRoutes(ctx) {
            if (!this.routes || !this.routes.length) return;

            this.routes.forEach(route => {
                const from = route.from; // [lat, lng]
                const to = route.to;     // [lat, lng]
                const color = route.color || '#FACC15'; // Default vibrant gold

                ctx.beginPath();
                ctx.strokeStyle = color;
                ctx.lineWidth = 2.5;
                ctx.setLineDash([5, 3]);

                const steps = 24;
                let started = false;

                for (let i = 0; i <= steps; i++) {
                    const t = i / steps;
                    const lat = from[0] + (to[0] - from[0]) * t;
                    const lng = from[1] + (to[1] - from[1]) * t;
                    const pt = this.project(lat, lng);

                    if (pt.visible) {
                        if (!started) {
                            ctx.moveTo(pt.x, pt.y);
                            started = true;
                        } else {
                            ctx.lineTo(pt.x, pt.y);
                        }
                    } else {
                        started = false;
                    }
                }
                ctx.stroke();
                ctx.setLineDash([]); // Reset dash
            });
        }

        drawMarkers(ctx) {
            if (!this.markers || !this.markers.length) return;
            this.pulseTimer += 0.05;
            const pulseScale = (Math.sin(this.pulseTimer) * 0.5 + 0.5);

            this.markers.forEach((m, idx) => {
                const pt = this.project(m.lat, m.lng);
                if (!pt.visible) return;

                const isActive = (this.activeMarker && this.activeMarker.label === m.label);
                const baseColor = m.color || (isActive ? '#FFD200' : '#38BDF8');

                // Pulsing outer ring
                ctx.beginPath();
                ctx.arc(pt.x, pt.y, 7 + pulseScale * 8, 0, Math.PI * 2);
                ctx.strokeStyle = isActive ? 'rgba(255, 210, 0, 0.4)' : 'rgba(56, 189, 248, 0.35)';
                ctx.lineWidth = 1.5;
                ctx.stroke();

                // Core pin circle
                ctx.beginPath();
                ctx.arc(pt.x, pt.y, isActive ? 5 : 4, 0, Math.PI * 2);
                ctx.fillStyle = baseColor;
                ctx.fill();
                ctx.strokeStyle = '#FFFFFF';
                ctx.lineWidth = 1.5;
                ctx.stroke();

                // Marker text badge
                const label = m.label || `Point ${idx + 1}`;
                ctx.font = isActive ? 'bold 11px Inter, sans-serif' : '9px Inter, sans-serif';
                const textWidth = ctx.measureText(label).width;

                // Pill background
                const pillX = pt.x + 8;
                const pillY = pt.y - 14;
                ctx.fillStyle = isActive ? 'rgba(0, 0, 0, 0.9)' : 'rgba(15, 23, 42, 0.85)';
                ctx.strokeStyle = isActive ? '#FFD200' : 'rgba(255, 255, 255, 0.3)';
                ctx.lineWidth = 1;

                this.roundRect(ctx, pillX - 4, pillY - 10, textWidth + 8, 16, 4);
                ctx.fill();
                ctx.stroke();

                // Text
                ctx.fillStyle = isActive ? '#FFD200' : '#F1F5F9';
                ctx.fillText(label, pillX, pillY + 2);
            });
        }

        roundRect(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        }

        startLoop() {
            const step = () => {
                // Auto-spin if enabled and not dragging
                if (this.autoSpin && !this.isDragging) {
                    this.targetLng += this.spinSpeed;
                }

                // Smooth exponential easing for rotation and zoom
                this.lng += (this.targetLng - this.lng) * 0.1;
                this.lat += (this.targetLat - this.lat) * 0.1;
                this.zoom += (this.targetZoom - this.zoom) * 0.1;

                this.draw();
                this.animFrameId = requestAnimationFrame(step);
            };
            this.animFrameId = requestAnimationFrame(step);
        }

        destroy() {
            if (this.animFrameId) {
                cancelAnimationFrame(this.animFrameId);
            }
            if (this.resizeHandler) {
                window.removeEventListener('resize', this.resizeHandler);
            }
        }
    }

    // Export to global window
    window.PscGlobe = PscGlobe;
})(window);
