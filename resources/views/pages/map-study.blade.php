@extends('layouts.app')

@section('title', $activeCase['title'] . ' - 3D Globe & Map Study Lab | PSCRanker')

@section('content')
<div class="min-h-screen bg-[#070D1B] text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Banner & Mentor Methodology Intro -->
        <div class="mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-800/80">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            🌐 Spatial Memory Engine
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            ⚡ Rank Holder Secret
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight">
                        PSC 3D Globe &amp; Map Study Lab
                    </h1>
                    <p class="text-sm sm:text-base text-slate-400 mt-1 max-w-3xl font-medium">
                        ഭൂമിശാസ്ത്രവും ലോകചരിത്രവും വെറുതെ മനഃപാഠമാക്കാതെ 3D ഗ്ലോബിലൂടെയും ഭൂപടത്തിലൂടെയും കണ്ട് മനസ്സിലാക്കൂ. ഫ്ലാറ്റ് മാപ്പുകളുടെ തെറ്റായ ധാരണകൾ തിരുത്തി റാങ്ക് ഉറപ്പാക്കാം.
                    </p>
                </div>

                <!-- Back to Sessions CTA -->
                <div class="flex items-center gap-3">
                    <a 
                        href="{{ route('sessions.index') }}" 
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs sm:text-sm border border-slate-700 transition flex items-center gap-2"
                    >
                        <span>← PSC Special Lessons</span>
                    </a>
                </div>
            </div>

            <!-- 5 Signature PSC Study Case Selector Tabs -->
            <div class="mt-6 flex items-center gap-2 overflow-x-auto no-scrollbar pb-2">
                @foreach($cases as $key => $caseItem)
                    <a 
                        href="{{ route('map.study', ['case' => $key]) }}"
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-black whitespace-nowrap transition flex items-center gap-2 border {{ $key === $activeCaseKey ? 'bg-[#0052FF] text-white border-blue-400 shadow-lg shadow-blue-500/30 ring-2 ring-blue-400/40' : 'bg-slate-900/80 hover:bg-slate-800 text-slate-300 border-slate-800' }}"
                    >
                        @if($key === 'pacific_reality') 🌏
                        @elseif($key === 'german_invasion') 🇩🇪
                        @elseif($key === 'red_sea') 🌊
                        @elseif($key === 'mandela') 🇿🇦
                        @elseif($key === 'kerala_rivers') 🌴
                        @endif
                        <span>{{ $caseItem['title'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Main Interactive Workspace -->
        <div 
            x-data="mapStudyApp(@js($activeCase))" 
            x-init="initGlobe()"
            class="grid grid-cols-1 lg:grid-cols-12 gap-8"
        >
            <!-- Left / Center Column: Interactive 3D Globe / Map Viewport (8 Cols) -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                
                <!-- Globe Viewport Card -->
                <div class="relative bg-slate-950 rounded-3xl border-2 border-slate-800 shadow-2xl overflow-hidden group">
                    
                    <!-- Top Canvas Control HUD Overlay -->
                    <div class="absolute top-4 left-4 right-4 z-20 flex flex-wrap items-center justify-between gap-3 pointer-events-none">
                        
                        <!-- Case Title Pill -->
                        <div class="pointer-events-auto bg-slate-900/90 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-slate-700 text-xs font-bold text-white flex items-center gap-2 shadow-md">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span x-text="activeCase.title"></span>
                        </div>

                        <!-- Controls Toolbar -->
                        <div class="pointer-events-auto flex items-center gap-1.5 bg-slate-900/90 backdrop-blur-md p-1.5 rounded-2xl border border-slate-700 shadow-md">
                            <!-- 3D Globe vs 2D Map Toggle -->
                            <button 
                                type="button" 
                                @click="toggleProjection()" 
                                class="px-2.5 py-1 rounded-lg text-xs font-black transition flex items-center gap-1.5"
                                :class="mode === '3d_globe' ? 'bg-[#0052FF] text-white' : 'text-slate-300 hover:text-white'"
                                title="Toggle 3D Globe / 2D Map"
                            >
                                <span x-show="mode === '3d_globe'">🌐 3D Globe</span>
                                <span x-show="mode !== '3d_globe'">🗺️ 2D Map</span>
                            </button>

                            <!-- Auto-Spin Button -->
                            <button 
                                type="button" 
                                @click="toggleAutoSpin()" 
                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                :class="isAutoSpinning ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300 hover:text-white'"
                                title="Auto-Rotate Globe"
                            >
                                <span x-text="isAutoSpinning ? '⏸ Pause' : '▶ Spin'"></span>
                            </button>

                            <!-- Reset Button -->
                            <button 
                                type="button" 
                                @click="resetCamera()" 
                                class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition text-xs"
                                title="Reset View"
                            >
                                🎯
                            </button>

                            <!-- Zoom In / Out -->
                            <button 
                                type="button" 
                                @click="zoomIn()" 
                                class="w-7 h-7 flex items-center justify-center text-slate-300 hover:text-white rounded-lg hover:bg-slate-800 transition font-bold"
                                title="Zoom In"
                            >
                                +
                            </button>
                            <button 
                                type="button" 
                                @click="zoomOut()" 
                                class="w-7 h-7 flex items-center justify-center text-slate-300 hover:text-white rounded-lg hover:bg-slate-800 transition font-bold"
                                title="Zoom Out"
                            >
                                −
                            </button>
                        </div>
                    </div>

                    <!-- The Canvas Element -->
                    <div class="w-full aspect-[4/3] sm:aspect-[16/10] relative cursor-grab active:cursor-grabbing select-none">
                        <canvas id="psc-main-globe-canvas" class="w-full h-full block"></canvas>
                        
                        <!-- Drag Gesture Hint -->
                        <div class="absolute bottom-4 left-4 pointer-events-none text-[11px] text-slate-400 bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-full border border-slate-800 flex items-center gap-1.5">
                            <span>👆</span>
                            <span>Click &amp; drag to rotate • Scroll to zoom • Tap pins for notes</span>
                        </div>

                        <!-- Mode indicator badge -->
                        <div class="absolute bottom-4 right-4 pointer-events-none text-[11px] font-mono text-slate-400 bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-full border border-slate-800">
                            <span x-text="mode === '3d_globe' ? 'Orthographic 3D' : 'Equirectangular 2D'"></span>
                        </div>
                    </div>

                </div>

                <!-- Pinpoint Fast Navigator Bar -->
                <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <span>📍 Interactive Focus Pinpoints</span>
                            <span class="text-[10px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full font-mono" x-text="(activeCase.markers || []).length + ' Pins'"></span>
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium">Tap any pin to rotate globe &amp; zoom</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <template x-for="(marker, idx) in activeCase.markers" :key="idx">
                            <button 
                                type="button" 
                                @click="selectMarker(marker)"
                                class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 border"
                                :class="selectedMarker && selectedMarker.label === marker.label ? 'bg-amber-400 text-slate-950 border-amber-300 shadow-md font-black' : 'bg-slate-800 hover:bg-slate-700 text-slate-200 border-slate-700'"
                            >
                                <span class="w-2 h-2 rounded-full" :style="'background-color: ' + (marker.color || '#38BDF8')"></span>
                                <span x-text="marker.label"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Selected Marker Detail Modal / Card -->
                    <template x-if="selectedMarker">
                        <div class="mt-4 p-4 rounded-xl bg-slate-950/90 border border-amber-400/40 animate-fadeIn">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">🎯</span>
                                    <div>
                                        <h3 class="text-sm font-black text-amber-300" x-text="selectedMarker.label"></h3>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                            Coordinates: <span x-text="selectedMarker.lat.toFixed(2) + '° N, ' + selectedMarker.lng.toFixed(2) + '° E'"></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="selectedMarker = null" class="text-xs text-slate-400 hover:text-white">✕</button>
                            </div>

                            <p class="text-xs text-slate-200 mt-2.5 font-medium leading-relaxed" x-text="selectedMarker.note"></p>

                            <template x-if="selectedMarker.note_malayalam">
                                <p class="text-xs text-amber-200 mt-2 font-semibold font-['Noto_Sans_Malayalam'] leading-relaxed" x-text="selectedMarker.note_malayalam"></p>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Case Deep Dive Explanation -->
                <div class="bg-slate-900/60 rounded-3xl border border-slate-800 p-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">📖</span>
                        <h2 class="text-lg font-black text-white font-['Noto_Sans_Malayalam']">
                            {{ $activeCase['title_malayalam'] }}
                        </h2>
                    </div>

                    <p class="text-sm text-slate-300 font-['Noto_Sans_Malayalam'] leading-relaxed">
                        {{ $activeCase['summary_malayalam'] }}
                    </p>

                    <p class="text-xs text-slate-400 leading-relaxed">
                        {{ $activeCase['summary'] }}
                    </p>

                    <!-- Client's Spatial Mentor Tip Callout -->
                    <div class="p-4 bg-gradient-to-r from-blue-950/60 via-indigo-950/40 to-slate-900 rounded-2xl border border-blue-500/30 flex items-start gap-3">
                        <span class="text-2xl">💡</span>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-blue-400 block mb-1">
                                PSC Mentor Spatial Tip / പഠന തന്ത്രം:
                            </span>
                            <p class="text-xs font-bold text-white font-['Noto_Sans_Malayalam'] leading-relaxed">
                                {{ $activeCase['mentor_tip_malayalam'] }}
                            </p>
                            <p class="text-[11px] text-slate-300 mt-1 italic">
                                "{{ $activeCase['mentor_tip'] }}"
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: PSC Exam Repeated Q&A & Related Lessons (4 Cols) -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                
                <!-- PSC High-Yield Repeated Questions Box -->
                <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 shadow-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🔥</span>
                            <h3 class="text-sm font-black text-white uppercase tracking-wide">PSC Repeated Questions</h3>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            High Yield
                        </span>
                    </div>

                    <div class="space-y-4">
                        @foreach($activeCase['psc_questions'] as $qIdx => $qItem)
                            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 transition">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-blue-600/30 text-blue-400 text-xs font-black flex items-center justify-center shrink-0 mt-0.5">
                                        {{ $qIdx + 1 }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-100 font-['Noto_Sans_Malayalam'] leading-snug">
                                            {{ $qItem['q'] }}
                                        </p>
                                        <div class="mt-2 text-xs font-black text-emerald-400 bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-800/40 inline-block font-['Noto_Sans_Malayalam']">
                                            ഉത്തരം: {{ $qItem['a'] }}
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1.5 font-['Noto_Sans_Malayalam']">
                                            💡 {{ $qItem['fact'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Why Globe Study Beats Flat Maps (Client Methodology Callout) -->
                <div class="bg-gradient-to-b from-indigo-950/40 to-slate-900/80 rounded-3xl border border-indigo-500/20 p-5">
                    <h3 class="text-xs font-black uppercase tracking-wider text-indigo-300 flex items-center gap-2 mb-2">
                        <span>🗺️ Flat Map Illusion vs 3D Reality</span>
                    </h3>
                    <p class="text-xs text-slate-300 font-['Noto_Sans_Malayalam'] leading-relaxed">
                        പരന്ന ഭൂപടങ്ങളിൽ അരികുകളിൽ കാണപ്പെടുന്ന രാജ്യങ്ങൾ യഥാർത്ഥത്തിൽ തൊട്ടടുത്ത അയൽക്കാരാണ്. ഗ്ലോബ് പഠനത്തിലൂടെ നിങ്ങളുടെ തലച്ചോറിൽ യഥാർത്ഥ ഭൂപ്രകൃതിയുടെ 3D ചിത്രങ്ങൾ രൂപപ്പെടുന്നു. ഇത് പരീക്ഷാ ഹാളിൽ കൺഫ്യൂഷനില്ലാതെ ശരിയുത്തരം കണ്ടെത്താൻ സഹായിക്കുന്നു.
                    </p>
                </div>

                <!-- Related Interactive Sessions -->
                @if($relatedSessions->isNotEmpty())
                    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                            <span>🎓 Practice with Full OMR Tests</span>
                        </h3>
                        
                        <div class="space-y-2.5">
                            @foreach($relatedSessions as $relSession)
                                <a 
                                    href="{{ route('session.show', ['slug' => $relSession->slug]) }}"
                                    class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-blue-500 transition flex items-center justify-between group"
                                >
                                    <div>
                                        <span class="text-xs font-bold text-slate-200 group-hover:text-blue-400 transition block">
                                            {{ $relSession->title }}
                                        </span>
                                        @if($relSession->title_malayalam)
                                            <span class="text-[11px] text-slate-500 font-['Noto_Sans_Malayalam']">
                                                {{ $relSession->title_malayalam }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-blue-400 font-black group-hover:translate-x-1 transition-transform">→</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function mapStudyApp(activeCase) {
    return {
        activeCase: activeCase,
        globe: null,
        mode: activeCase.mode || '3d_globe',
        isAutoSpinning: false,
        selectedMarker: null,

        initGlobe() {
            this.$nextTick(() => {
                const canvas = document.getElementById('psc-main-globe-canvas');
                if (!canvas || !window.PscGlobe) return;

                this.globe = new window.PscGlobe(canvas, {
                    mode: this.mode,
                    centerLat: this.activeCase.center_lat || 20.0,
                    centerLng: this.activeCase.center_lng || 78.0,
                    zoom: this.activeCase.zoom || 1.4,
                    autoSpin: false,
                    markers: this.activeCase.markers || [],
                    routes: this.activeCase.routes || [],
                    onMarkerClick: (m) => {
                        this.selectedMarker = m;
                    }
                });
            });
        },

        toggleProjection() {
            this.mode = (this.mode === '3d_globe') ? '2d_map' : '3d_globe';
            if (this.globe) {
                this.globe.setMode(this.mode);
            }
        },

        toggleAutoSpin() {
            if (this.globe) {
                this.isAutoSpinning = this.globe.toggleAutoSpin();
            }
        },

        resetCamera() {
            this.selectedMarker = null;
            if (this.globe) {
                this.globe.flyTo(
                    this.activeCase.center_lat || 20.0,
                    this.activeCase.center_lng || 78.0,
                    this.activeCase.zoom || 1.4
                );
            }
        },

        zoomIn() {
            if (this.globe) {
                this.globe.targetZoom = Math.min(6.0, this.globe.targetZoom * 1.25);
            }
        },

        zoomOut() {
            if (this.globe) {
                this.globe.targetZoom = Math.max(0.7, this.globe.targetZoom * 0.8);
            }
        },

        selectMarker(marker) {
            this.selectedMarker = marker;
            if (this.globe) {
                this.globe.activeMarker = marker;
                this.globe.flyTo(marker.lat, marker.lng, Math.max(1.8, (this.activeCase.zoom || 1.4) * 1.15));
            }
        }
    };
}
</script>
@endpush
@endsection
