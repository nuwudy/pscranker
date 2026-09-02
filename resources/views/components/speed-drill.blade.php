@props([
    'quizId' => 1,
    'standalone' => false
])

<div 
    x-data="speedDrillEngine({{ $quizId }})" 
    x-init="init()" 
    class="w-full max-w-4xl mx-auto {{ $standalone ? 'min-h-[85vh] flex flex-col justify-center' : '' }}"
>
    <!-- Loading State -->
    <template x-if="isLoading">
        <div class="bg-white rounded-3xl p-12 border-2 border-blue-100 shadow-xl text-center">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-yellow-400 rounded-full animate-spin mx-auto mb-4"></div>
            <h3 class="text-xl font-black text-slate-800">ലോഡ് ചെയ്യുന്നു... (Loading High-Speed Drill)</h3>
            <p class="text-sm text-slate-500 mt-1">Getting Kerala PSC trap questions ready!</p>
        </div>
    </template>

    <!-- Error State -->
    <template x-if="loadError">
        <div class="bg-red-50 rounded-3xl p-8 border-2 border-red-200 text-center">
            <span class="text-4xl">⚠️</span>
            <h3 class="text-lg font-black text-red-800 mt-2">Error loading drill questions</h3>
            <button @click="loadQuestions()" class="mt-4 px-5 py-2 bg-red-600 text-white font-bold rounded-xl shadow">Retry</button>
        </div>
    </template>

    <!-- 1. Intro Start Screen -->
    <template x-if="!isLoading && !loadError && state === 'intro'">
        <div class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-blue-100 shadow-2xl relative overflow-hidden">
            <!-- Decorative corner lightning -->
            <div class="absolute -top-10 -right-10 w-36 h-36 bg-yellow-100 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-36 h-36 bg-blue-100 rounded-full blur-2xl pointer-events-none"></div>

            <div class="text-center max-w-xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-[#0052FF] text-xs font-black uppercase tracking-wider mb-4">
                    <span>⚡ 3-Minute Kerala PSC Rapid Fire</span>
                </div>

                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight" x-text="quizData ? quizData.title : 'Speed Drill Blitz'"></h2>
                <p class="text-sm sm:text-base font-semibold text-[#0052FF] mt-1" x-text="quizData ? quizData.title_malayalam : ''"></p>
                <p class="text-sm text-slate-600 mt-3" x-text="quizData ? quizData.description : ''"></p>

                <!-- Drill Rules Matrix -->
                <div class="grid grid-cols-3 gap-3 my-6 text-center">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3">
                        <span class="text-xl">⏱️</span>
                        <div class="text-base sm:text-lg font-black text-slate-900 mt-1">20s</div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase">Per Question</div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-3">
                        <span class="text-xl">✅</span>
                        <div class="text-base sm:text-lg font-black text-emerald-700 mt-1">+1.00</div>
                        <div class="text-[11px] font-bold text-emerald-600 uppercase">Correct Mark</div>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-3">
                        <span class="text-xl">⚠️</span>
                        <div class="text-base sm:text-lg font-black text-red-600 mt-1">-0.33</div>
                        <div class="text-[11px] font-bold text-red-500 uppercase">PSC Negative</div>
                    </div>
                </div>

                <!-- Candidate Name Input -->
                <div class="mb-6 text-left">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Your Name / Call Sign (for Leaderboard):</label>
                    <input 
                        type="text" 
                        x-model="candidateName" 
                        placeholder="e.g. Arjun Nair, Sneha..." 
                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-[#0052FF] focus:outline-none font-bold text-slate-800 transition"
                    >
                </div>

                <!-- Launch Button -->
                <button 
                    @click="startDrill()" 
                    class="w-full py-4 px-8 bg-gradient-to-r from-[#0052FF] to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-black text-lg sm:text-xl rounded-2xl shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 transform active:scale-95 transition flex items-center justify-center gap-3 border-2 border-yellow-400"
                >
                    <span>START 3-MIN SPEED DRILL</span>
                    <span class="text-yellow-300 text-2xl animate-pulse">⚡</span>
                </button>
                <div class="mt-3 text-xs text-slate-400 font-medium">
                    Negative marking reflex test • Real PSC standard
                </div>
            </div>
        </div>
    </template>

    <!-- 2. Active Quiz Playing Screen (Mobile App Feel) -->
    <template x-if="state === 'playing' && currentQuestion">
        <div class="bg-white rounded-3xl border-2 border-blue-200 shadow-2xl overflow-hidden flex flex-col">
            
            <!-- Quiz App Header with Timer, Progress & Live Score -->
            <div class="bg-slate-900 text-white p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
                
                <!-- Question Number Indicator -->
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-sm">
                        <span x-text="currentIndex + 1"></span>
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-slate-300">
                        / <span x-text="questions.length"></span> Questions
                    </span>
                </div>

                <!-- 20-Second Countdown Dial -->
                <div class="flex items-center gap-2 bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-700">
                    <span class="text-sm">⏳</span>
                    <div 
                        class="text-lg font-black tracking-tight"
                        :class="{
                            'text-white': questionTimer > 5,
                            'text-amber-400 animate-pulse': questionTimer <= 5 && questionTimer > 2,
                            'text-red-500 font-black animate-ping': questionTimer <= 2
                        }"
                    >
                        <span x-text="questionTimer"></span>s
                    </div>
                </div>

                <!-- Real-time Score Tally (with Negative Marking!) -->
                <div class="flex items-center gap-2">
                    <div class="text-right">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Live Score</div>
                        <div 
                            class="text-sm sm:text-base font-black"
                            :class="currentScore >= 0 ? 'text-emerald-400' : 'text-red-400'"
                            x-text="currentScore.toFixed(2)"
                        ></div>
                    </div>
                </div>

            </div>

            <!-- Question Timer Progress Bar -->
            <div class="w-full bg-slate-200 h-2 relative">
                <div 
                    class="h-full transition-all duration-1000 ease-linear"
                    :class="questionTimer > 5 ? 'bg-[#0052FF]' : 'bg-red-500 animate-pulse'"
                    :style="'width: ' + ((questionTimer / 20) * 100) + '%'"
                ></div>
            </div>

            <!-- Question Body -->
            <div class="p-4 sm:p-8 flex-grow">
                
                <!-- Category Tag & Exam Reference -->
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <span 
                        class="px-2.5 py-1 rounded-md text-[11px] font-black uppercase tracking-wider bg-blue-50 text-[#0052FF] border border-blue-100"
                        x-text="currentQuestion.category"
                    ></span>
                    <span 
                        x-show="currentQuestion.psc_exam_reference" 
                        class="text-xs font-bold text-slate-400 flex items-center gap-1"
                    >
                        <span>📜</span> <span x-text="currentQuestion.psc_exam_reference"></span>
                    </span>
                </div>

                <!-- Question Text (Malayalam First, English Below) -->
                <h3 
                    x-show="currentQuestion.question_text_malayalam"
                    class="text-lg sm:text-2xl font-black text-slate-900 leading-snug tracking-tight mb-2" 
                    x-text="currentQuestion.question_text_malayalam"
                ></h3>
                <p 
                    class="text-sm sm:text-base font-medium text-slate-600 leading-relaxed mb-6" 
                    x-text="currentQuestion.question_text"
                ></p>

                <!-- Options Grid (A, B, C, D) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <template x-for="opt in currentQuestion.options" :key="opt.key">
                        <button
                            @click="selectOption(opt.key)"
                            :disabled="selectedOption !== null"
                            class="w-full text-left p-4 rounded-2xl border-2 transition-all font-bold flex items-start gap-3 relative group"
                            :class="getOptionClass(opt.key)"
                        >
                            <!-- Option Key Bubble (A, B, C, D) -->
                            <span 
                                class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 font-black text-sm transition"
                                :class="getOptionBubbleClass(opt.key)"
                                x-text="opt.key"
                            ></span>

                            <!-- Option Texts -->
                            <div class="flex-grow">
                                <div 
                                    x-show="opt.text_ml" 
                                    class="text-base text-slate-900 leading-snug font-bold"
                                    x-text="opt.text_ml"
                                ></div>
                                <div 
                                    class="text-xs text-slate-500 mt-0.5"
                                    x-text="opt.text"
                                ></div>
                            </div>

                            <!-- Right Indicator Icon -->
                            <div class="shrink-0 text-xl" x-show="selectedOption !== null">
                                <span x-show="opt.key === currentQuestion.correct_option">✅</span>
                                <span x-show="selectedOption === opt.key && opt.key !== currentQuestion.correct_option">❌</span>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- Instant Feedback & Humorous Trap Banner (Visible upon answer) -->
                <div 
                    x-show="selectedOption !== null" 
                    x-transition 
                    class="mt-6 p-4 sm:p-5 rounded-2xl border-2"
                    :class="isCorrect ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-red-50 border-red-200 text-red-900'"
                >
                    <div class="flex items-start gap-3">
                        <span class="text-3xl shrink-0" x-text="isCorrect ? '🎉' : '🚨'"></span>
                        <div class="flex-grow">
                            <div class="flex items-center justify-between">
                                <h4 
                                    class="text-base font-black"
                                    x-text="isCorrect ? 'പൊളിച്ചു സഖാവേ! (+1.00 Mark)' : 'PSC Trap Alert! (-0.33 Mark)'"
                                ></h4>
                                <span 
                                    class="px-2 py-0.5 rounded text-xs font-black"
                                    :class="isCorrect ? 'bg-emerald-200 text-emerald-800' : 'bg-red-200 text-red-800'"
                                    x-text="isCorrect ? '+1.00' : '-0.33'"
                                ></span>
                            </div>

                            <!-- Witty Trap Warning Micro-Copy -->
                            <p 
                                x-show="!isCorrect && currentQuestion.trap_warning" 
                                class="text-sm font-bold text-red-700 mt-1"
                                x-text="currentQuestion.trap_warning"
                            ></p>

                            <!-- Malayalam & English Explanation -->
                            <p 
                                x-show="currentQuestion.explanation_malayalam"
                                class="text-xs sm:text-sm text-slate-700 font-medium mt-2 leading-relaxed bg-white/70 p-2.5 rounded-xl"
                                x-text="currentQuestion.explanation_malayalam"
                            ></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Quiz Action Bottom Footer -->
            <div class="bg-slate-50 border-t border-slate-200 p-4 sm:p-5 flex items-center justify-between">
                <div class="text-xs text-slate-500 font-bold hidden sm:block">
                    ⚡ Fast reflex speed drill — Keep up your momentum!
                </div>

                <!-- Next Question Button -->
                <div class="w-full sm:w-auto flex justify-end">
                    <button 
                        x-show="selectedOption !== null"
                        @click="nextQuestion()"
                        class="w-full sm:w-auto px-8 py-3 bg-[#0052FF] hover:bg-blue-600 text-white font-black rounded-xl shadow-md transition flex items-center justify-center gap-2 active:scale-95"
                    >
                        <span x-text="currentIndex < questions.length - 1 ? 'Next Question →' : 'View Final Rank Card 🏆'"></span>
                    </button>
                    
                    <button 
                        x-show="selectedOption === null"
                        @click="skipQuestion()"
                        class="text-xs font-bold text-slate-500 hover:text-slate-800 py-2 px-4 transition"
                    >
                        Skip Question (0 Marks) ⏭️
                    </button>
                </div>
            </div>

        </div>
    </template>

    <!-- 3. Final Results & Rank Card Screen -->
    <template x-if="state === 'finished'">
        <div class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-blue-200 shadow-2xl text-center relative overflow-hidden">
            
            <!-- Confetti & Celebration Header -->
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-yellow-400 to-amber-500 text-slate-950 flex items-center justify-center text-4xl mx-auto mb-4 shadow-lg shadow-yellow-500/30 animate-bounce">
                🏆
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black uppercase tracking-wider mb-2">
                <span>Speed Drill Completed!</span>
            </div>

            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
                <span x-text="candidateName"></span>'s PSC Rank Card
            </h2>
            <p class="text-sm font-semibold text-slate-500 mt-1">
                Your performance against Kerala PSC standards
            </p>

            <!-- Big Score Badge -->
            <div class="my-6 bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-3xl p-6 max-w-md mx-auto shadow-inner">
                <div class="text-xs font-bold uppercase text-slate-500 tracking-wider">Final Net Marks</div>
                <div class="text-4xl sm:text-6xl font-black text-[#0052FF] my-1" x-text="currentScore.toFixed(2)"></div>
                <div class="text-xs font-bold text-slate-600">
                    Out of <span x-text="questions.length"></span>.00 Maximum Marks
                </div>

                <!-- Percentile Rank Banner -->
                <div class="mt-4 pt-3 border-t border-blue-200/80 flex items-center justify-center gap-2 text-sm font-black text-slate-800">
                    <span class="text-yellow-500">⭐</span>
                    <span>Top <strong class="text-[#0052FF]" x-text="(100 - percentileRank).toFixed(1) + '%'"></strong> in Kerala Daily Duel</span>
                </div>
            </div>

            <!-- Granular Breakdown Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-xl mx-auto mb-8 text-center">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-3">
                    <div class="text-xl sm:text-2xl font-black text-emerald-700" x-text="correctCount"></div>
                    <div class="text-[11px] font-bold text-emerald-600 uppercase">Correct (+<span x-text="correctCount"></span>)</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-2xl p-3">
                    <div class="text-xl sm:text-2xl font-black text-red-600" x-text="wrongCount"></div>
                    <div class="text-[11px] font-bold text-red-500 uppercase">Wrong (-<span x-text="(wrongCount * 0.33).toFixed(2)"></span>)</div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3">
                    <div class="text-xl sm:text-2xl font-black text-slate-700" x-text="unansweredCount"></div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase">Skipped</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-3">
                    <div class="text-xl sm:text-2xl font-black text-[#0052FF]" x-text="accuracyPercentage + '%'"></div>
                    <div class="text-[11px] font-bold text-blue-600 uppercase">Accuracy</div>
                </div>
            </div>

            <!-- Action Buttons: Retake & Share -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
                <button 
                    @click="retakeDrill()" 
                    class="w-full sm:w-1/2 py-3.5 px-6 bg-[#FFD200] hover:bg-[#F5C500] text-slate-950 font-black rounded-2xl shadow-md transition active:scale-95 border border-yellow-400 flex items-center justify-center gap-2"
                >
                    <span>🔄 Retake Drill</span>
                </button>
                <a 
                    :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('I scored ' + currentScore.toFixed(2) + ' marks on PSCRanker 3-min speed drill! Can you beat my score? Try now: ' + window.location.origin)" 
                    target="_blank" 
                    class="w-full sm:w-1/2 py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-md transition active:scale-95 flex items-center justify-center gap-2"
                >
                    <span>💬 Share on WhatsApp</span>
                </a>
            </div>

            <div class="mt-4">
                <a href="{{ route('leaderboard') }}" class="text-sm font-extrabold text-[#0052FF] hover:underline">
                    View Live Daily Leaderboard →
                </a>
            </div>

        </div>
    </template>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('speedDrillEngine', (quizId) => ({
        quizId: quizId,
        quizData: null,
        questions: [],
        isLoading: true,
        loadError: false,
        state: 'intro', // 'intro', 'playing', 'finished'
        candidateName: localStorage.getItem('pscranker_candidate_name') || 'Guest Ranker',

        currentIndex: 0,
        currentQuestion: null,
        selectedOption: null,
        isCorrect: false,
        
        // Kerala PSC Standard Negative Marking
        currentScore: 0.00,
        correctCount: 0,
        wrongCount: 0,
        unansweredCount: 0,
        
        // Timers
        questionTimer: 20,
        timerInterval: null,
        startTime: null,
        totalTimeTaken: 0,
        percentileRank: 92.4,

        init() {
            this.loadQuestions();
        },

        async loadQuestions() {
            this.isLoading = true;
            this.loadError = false;
            try {
                const response = await fetch(`/api/quiz/${this.quizId}/questions`);
                if (!response.ok) throw new Error('Failed to load quiz');
                const data = await response.json();
                this.quizData = data.quiz;
                this.questions = data.questions;
                this.isLoading = false;
            } catch (e) {
                console.error('Quiz Load Error:', e);
                this.loadError = true;
                this.isLoading = false;
            }
        },

        startDrill() {
            if (!this.candidateName.trim()) {
                this.candidateName = 'Guest Ranker #' + Math.floor(100 + Math.random() * 900);
            }
            localStorage.setItem('pscranker_candidate_name', this.candidateName);

            this.state = 'playing';
            this.currentIndex = 0;
            this.currentScore = 0.00;
            this.correctCount = 0;
            this.wrongCount = 0;
            this.unansweredCount = 0;
            this.startTime = Date.now();
            
            this.loadCurrentQuestion();
        },

        loadCurrentQuestion() {
            this.currentQuestion = this.questions[this.currentIndex];
            this.selectedOption = null;
            this.isCorrect = false;
            this.questionTimer = 20;

            clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                if (this.questionTimer > 0) {
                    this.questionTimer--;
                    if (this.questionTimer <= 3) {
                        window.PscSound.playTick();
                    }
                } else {
                    clearInterval(this.timerInterval);
                    this.timeOutQuestion();
                }
            }, 1000);
        },

        selectOption(key) {
            if (this.selectedOption !== null) return;
            clearInterval(this.timerInterval);

            this.selectedOption = key;
            this.isCorrect = (key === this.currentQuestion.correct_option);

            if (this.isCorrect) {
                this.correctCount++;
                this.currentScore += 1.00;
                window.PscSound.playCorrect();

                // Trigger confetti celebration
                if (window.confetti) {
                    window.confetti({
                        particleCount: 40,
                        spread: 60,
                        origin: { y: 0.7 }
                    });
                }
            } else {
                this.wrongCount++;
                this.currentScore -= 0.33; // Standard Kerala PSC Negative
                window.PscSound.playWrong();
            }

            if ('vibrate' in navigator) {
                navigator.vibrate(this.isCorrect ? 50 : [80, 50, 80]);
            }
        },

        timeOutQuestion() {
            if (this.selectedOption !== null) return;
            this.unansweredCount++;
            // Alert user of timeout
            this.nextQuestion();
        },

        skipQuestion() {
            clearInterval(this.timerInterval);
            this.unansweredCount++;
            this.nextQuestion();
        },

        nextQuestion() {
            clearInterval(this.timerInterval);
            if (this.currentIndex < this.questions.length - 1) {
                this.currentIndex++;
                this.loadCurrentQuestion();
            } else {
                this.finishDrill();
            }
        },

        async finishDrill() {
            clearInterval(this.timerInterval);
            this.totalTimeTaken = Math.round((Date.now() - this.startTime) / 1000);
            this.state = 'finished';
            window.PscSound.playFanfare();

            // Submit attempt to backend
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('/api/drill/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quiz_id: this.quizId,
                        candidate_name: this.candidateName,
                        total_questions: this.questions.length,
                        correct_answers: this.correctCount,
                        wrong_answers: this.wrongCount,
                        unanswered: this.unansweredCount,
                        score: parseFloat(this.currentScore.toFixed(2)),
                        accuracy_percentage: parseFloat(this.accuracyPercentage),
                        time_taken_seconds: this.totalTimeTaken
                    })
                });

                if (response.ok) {
                    const resData = await response.json();
                    if (resData.percentile) {
                        this.percentileRank = resData.percentile;
                    }
                }
            } catch (err) {
                console.error('Error submitting score:', err);
            }
        },

        retakeDrill() {
            this.startDrill();
        },

        get accuracyPercentage() {
            const attempted = this.correctCount + this.wrongCount;
            if (attempted === 0) return 0;
            return Math.round((this.correctCount / attempted) * 100);
        },

        getOptionClass(key) {
            if (this.selectedOption === null) {
                return 'bg-white hover:bg-blue-50/60 border-slate-200 hover:border-[#0052FF] text-slate-800 shadow-sm';
            }
            if (key === this.currentQuestion.correct_option) {
                return 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-400';
            }
            if (this.selectedOption === key) {
                return 'bg-red-50 border-red-500 text-red-900 ring-2 ring-red-400';
            }
            return 'bg-slate-50 border-slate-200 text-slate-400 opacity-60';
        },

        getOptionBubbleClass(key) {
            if (this.selectedOption === null) {
                return 'bg-slate-100 text-slate-700 group-hover:bg-blue-600 group-hover:text-white';
            }
            if (key === this.currentQuestion.correct_option) {
                return 'bg-emerald-600 text-white';
            }
            if (this.selectedOption === key) {
                return 'bg-red-600 text-white';
            }
            return 'bg-slate-200 text-slate-400';
        }
    }));
});
</script>
@endpush
