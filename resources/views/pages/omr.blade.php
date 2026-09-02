@extends('layouts.app')

@section('title', 'Interactive OMR Bubble Simulator — PSCRanker')

@section('content')
<div class="py-8 sm:py-14 bg-gradient-to-b from-blue-50/60 via-slate-50 to-white min-h-[85vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-xl mx-auto mb-10">
            <div class="w-16 h-16 rounded-3xl bg-blue-100 text-[#0052FF] flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                📝
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight">
                OMR Bubble Simulator
            </h1>
            <p class="text-base font-bold text-[#0052FF] mt-1">
                Master Speed Bubbling & Negative Marking Reflexes
            </p>
            <p class="text-xs sm:text-sm text-slate-600 mt-2">
                In Kerala PSC, slow bubbling wastes 8-12 minutes! Practice clicking/darkening bubbles at high speed without making pen-bleed or double-bubbling errors.
            </p>
        </div>

        <!-- Interactive Alpine OMR Simulator -->
        <div 
            x-data="omrSimulatorEngine()" 
            class="bg-white rounded-3xl border-2 border-slate-200/80 shadow-2xl p-6 sm:p-10"
        >
            <!-- Top Controls Bar -->
            <div class="bg-slate-900 text-white rounded-2xl p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
                
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⏱️</span>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Time Elapsed</div>
                        <div class="text-lg font-mono font-black text-yellow-400" x-text="formatTime(secondsElapsed)">00:00</div>
                    </div>
                </div>

                <!-- Tally metrics -->
                <div class="flex items-center gap-6">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Bubbled</div>
                        <div class="text-base font-black text-white"><span x-text="bubbledCount"></span> / 10</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Estimated Net Marks</div>
                        <div class="text-base font-black text-emerald-400" x-text="calculateEstimatedScore()"></div>
                    </div>
                </div>

                <button 
                    @click="resetSheet()" 
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-xl border border-slate-700 transition"
                >
                    Reset Sheet 🔄
                </button>

            </div>

            <!-- Authentic Kerala PSC OMR Sheet Frame -->
            <div class="border-2 border-dashed border-slate-300 rounded-2xl p-4 sm:p-8 bg-[#FAFBFD] font-mono">
                <div class="flex items-center justify-between border-b-2 border-slate-300 pb-3 mb-6">
                    <div>
                        <div class="text-xs font-bold text-slate-800 uppercase tracking-widest">KERALA PUBLIC SERVICE COMMISSION</div>
                        <div class="text-[10px] text-slate-500">OBJECTIVE MULTIPLE CHOICE OMR ANSWER SHEET</div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black bg-blue-100 text-[#0052FF] px-2.5 py-1 rounded">VERSION A</span>
                    </div>
                </div>

                <!-- 10 Questions Bubble Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <template x-for="q in 10" :key="q">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs hover:border-blue-400 transition">
                            <span class="text-xs font-black text-slate-800 w-8" x-text="q < 10 ? '0' + q : q"></span>
                            
                            <!-- Bubble Options A B C D -->
                            <div class="flex items-center gap-2.5 sm:gap-3">
                                <template x-for="opt in ['A', 'B', 'C', 'D']" :key="opt">
                                    <button 
                                        @click="fillBubble(q, opt)"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 flex items-center justify-center text-[11px] font-bold transition-all duration-150 active:scale-90"
                                        :class="sheetAnswers[q] === opt ? 'bg-slate-900 border-slate-950 text-white shadow-inner scale-105' : 'bg-white border-slate-400 text-slate-700 hover:border-slate-800 hover:bg-slate-50'"
                                        :title="'Question ' + q + ' Option ' + opt"
                                    >
                                        <span x-text="opt"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Clear button -->
                            <button 
                                @click="clearBubble(q)" 
                                x-show="sheetAnswers[q]"
                                class="text-slate-300 hover:text-red-500 text-xs transition"
                                title="Erase bubble"
                            >
                                ✕
                            </button>
                            <span x-show="!sheetAnswers[q]" class="w-3"></span>
                        </div>
                    </template>
                </div>

                <!-- OMR Instructions Footer -->
                <div class="mt-6 pt-4 border-t border-slate-200 text-[10px] text-slate-500 space-y-1">
                    <p>⚠️ <strong>Important PSC Instructions:</strong> Use Blue/Black ballpoint pen only. No gel pens or whitener permitted.</p>
                    <p>⚠️ Negative marking of <strong>1/3 mark (0.33)</strong> is deducted for each wrong answer.</p>
                </div>
            </div>

            <!-- Complete Practice & Jump to Rapid Fire -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs font-bold text-slate-600">
                    💡 Want to practice with real questions & 20s timers?
                </div>
                <a 
                    href="{{ route('drill.show') }}" 
                    class="px-8 py-3.5 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-sm rounded-2xl shadow-md transition flex items-center gap-2 active:scale-95"
                >
                    <span>Launch Rapid Speed Drill</span>
                    <span>⚡</span>
                </a>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script>
function omrSimulatorEngine() {
    return {
        sheetAnswers: {},
        secondsElapsed: 0,
        timer: null,

        init() {
            this.timer = setInterval(() => {
                this.secondsElapsed++;
            }, 1000);
        },

        fillBubble(questionNum, option) {
            this.sheetAnswers[questionNum] = option;
            window.PscSound.playTick();
        },

        clearBubble(questionNum) {
            delete this.sheetAnswers[questionNum];
        },

        resetSheet() {
            this.sheetAnswers = {};
            this.secondsElapsed = 0;
        },

        get bubbledCount() {
            return Object.keys(this.sheetAnswers).length;
        },

        calculateEstimatedScore() {
            const count = this.bubbledCount;
            if (count === 0) return '0.00 pts';
            // Simulate 80% accuracy
            const correct = Math.round(count * 0.8);
            const wrong = count - correct;
            const net = (correct * 1.0) - (wrong * 0.33);
            return net.toFixed(2) + ' pts';
        },

        formatTime(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        }
    }
}
</script>
@endpush
@endsection
