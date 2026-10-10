<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $session->title }} - MicroLearning</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-900 font-sans antialiased"
    x-data="pscmSessionData()" x-init="initData({{ json_encode($session) }})">

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-xl flex flex-col relative">
        
        <!-- Header / Progress -->
        <div class="p-4 bg-white border-b sticky top-0 z-10 flex items-center justify-between">
            <button @click="prevStep" x-show="currentStep > 0 && currentStep < 9" class="text-gray-500 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <div class="flex-1 px-4">
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" :style="`width: ${(currentStep / 9) * 100}%`"></div>
                </div>
            </div>
            <div x-show="currentStep === 9" class="font-mono text-sm font-bold text-red-600" x-text="formatTime(omrTimeLeft)"></div>
            <button @click="nextStep" x-show="currentStep < 9" class="text-blue-500 hover:text-blue-700 text-sm font-semibold">Skip</button>
        </div>

        <!-- Main Content Area (One unit per screen) -->
        <div class="flex-1 overflow-y-auto p-6 flex flex-col">
            
            <!-- Screen 0: Hook Question -->
            <div x-show="currentStep === 0" class="flex-1 flex flex-col justify-center">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Previous PSC Question</span>
                <h2 class="text-2xl font-bold mb-6" x-text="session.hook_question?.question"></h2>
                
                <div class="space-y-3">
                    <template x-for="(opt, idx) in session.hook_question?.options" :key="idx">
                        <button class="w-full text-left p-4 rounded-xl border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-colors font-medium" x-text="`${['A','B','C','D'][idx]}. ${opt}`"></button>
                    </template>
                </div>

                <button @click="nextStep" class="mt-8 w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition">Analyze This Question &rarr;</button>
            </div>

            <!-- Screen 1: Explainer Video (Optional) -->
            <div x-show="currentStep === 1" class="flex-1 flex flex-col">
                <h2 class="text-xl font-bold mb-4">Explainer Video</h2>
                <div class="bg-black w-full aspect-video rounded-xl flex items-center justify-center text-white" x-text="session.media?.video_url ? 'Video Player Placeholder' : 'No Video'"></div>
                <button @click="nextStep" class="mt-auto w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg">Continue to Concept &rarr;</button>
            </div>

            <!-- Screen 2: Visual Concept (Optional) -->
            <div x-show="currentStep === 2" class="flex-1 flex flex-col">
                <h2 class="text-xl font-bold mb-4">Concept Anchor</h2>
                <div class="bg-gray-200 w-full aspect-square rounded-xl flex items-center justify-center text-gray-500" x-text="session.media?.image_url ? 'Image Placeholder' : 'No Image'"></div>
                <p class="mt-4 text-gray-600 font-medium text-center" x-text="session.media?.image_caption"></p>
                <button @click="nextStep" class="mt-auto w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg">Read Lesson &rarr;</button>
            </div>

            <!-- Screen 3: Core Lesson -->
            <div x-show="currentStep === 3" class="flex-1 flex flex-col">
                <h2 class="text-2xl font-bold mb-4 text-blue-800">Core Lesson</h2>
                <div class="prose prose-blue" x-html="renderMarkdown(session.lesson_content)"></div>
                <button @click="nextStep" class="mt-8 w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg">Start Practice &rarr;</button>
            </div>

            <!-- Screens 4-7: Practice MCQs -->
            <template x-for="(mcq, idx) in session.practice_mcqs" :key="idx">
                <div x-show="currentStep === (4 + idx)" class="flex-1 flex flex-col justify-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2" x-text="`Practice ${idx + 1} of 4`"></span>
                    <h2 class="text-xl font-bold mb-6" x-text="mcq.question"></h2>
                    <div class="space-y-3">
                        <template x-for="(opt, optIdx) in mcq.options" :key="optIdx">
                            <button @click="selectPracticeAnswer(idx, optIdx)" 
                                :class="getPracticeBtnClass(idx, optIdx)"
                                class="w-full text-left p-4 rounded-xl border-2 transition-colors font-medium">
                                <span x-text="`${['A','B','C','D'][optIdx]}. ${opt}`"></span>
                            </button>
                        </template>
                    </div>
                    
                    <!-- Feedback -->
                    <div x-show="practiceAnswers[idx] !== null" class="mt-6 p-4 rounded-xl" :class="practiceAnswers[idx] === mcq.correct_index ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        <p class="font-bold mb-1" x-text="practiceAnswers[idx] === mcq.correct_index ? 'Correct!' : 'Incorrect'"></p>
                        <p class="text-sm" x-text="mcq.explanation"></p>
                    </div>

                    <button x-show="practiceAnswers[idx] !== null" @click="nextStep" class="mt-auto w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg">Next &rarr;</button>
                </div>
            </template>

            <!-- Screen 8: OMR Instructions -->
            <div x-show="currentStep === 8" class="flex-1 flex flex-col justify-center items-center text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h2 class="text-2xl font-bold mb-2">OMR Integrated Test</h2>
                <p class="text-gray-600 mb-8">5 Questions • 5 Minutes • Negative Marking (0.33)</p>
                <button @click="startOMR" class="w-full bg-red-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-red-700 transition">Start Test Now</button>
            </div>

            <!-- Screen 9: OMR Interface -->
            <div x-show="currentStep === 9" class="flex-1 flex flex-col">
                <div class="mb-4">
                    <h3 class="font-bold text-lg mb-2" x-text="`Q${currentOmrQ + 1}: ${omrQuestions[currentOmrQ]?.question}`"></h3>
                    <div class="space-y-2">
                        <template x-for="(opt, oIdx) in omrQuestions[currentOmrQ]?.options" :key="oIdx">
                            <div class="flex items-center space-x-3 p-3 border rounded-lg bg-gray-50">
                                <!-- OMR Bubble -->
                                <button @click="omrAnswers[currentOmrQ] = oIdx" 
                                    :class="omrAnswers[currentOmrQ] === oIdx ? 'bg-black border-black' : 'bg-white border-gray-400'"
                                    class="w-8 h-8 rounded-full border-2 flex items-center justify-center transition-colors">
                                    <span :class="omrAnswers[currentOmrQ] === oIdx ? 'text-white' : 'text-gray-500'" class="font-bold text-sm" x-text="['A','B','C','D'][oIdx]"></span>
                                </button>
                                <span class="text-gray-700 font-medium" x-text="opt"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Navigation grid -->
                <div class="mt-auto pt-4 border-t">
                    <div class="flex justify-between mb-4">
                        <button @click="currentOmrQ = Math.max(0, currentOmrQ - 1)" class="px-4 py-2 border rounded-lg font-medium" :disabled="currentOmrQ === 0">Previous</button>
                        <button @click="currentOmrQ = Math.min(4, currentOmrQ + 1)" class="px-4 py-2 border rounded-lg font-medium" :disabled="currentOmrQ === 4">Next</button>
                    </div>
                    <div class="flex justify-center space-x-2 mb-4">
                        <template x-for="(q, idx) in omrQuestions" :key="idx">
                            <button @click="currentOmrQ = idx"
                                :class="{
                                    'ring-2 ring-blue-500': currentOmrQ === idx,
                                    'bg-gray-800 text-white': omrAnswers[idx] !== null,
                                    'bg-gray-200': omrAnswers[idx] === null
                                }"
                                class="w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center" x-text="idx + 1"></button>
                        </template>
                    </div>
                    <button @click="submitOMR" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg">Submit Test</button>
                </div>
            </div>

            <!-- Screen 10: Gamified Evaluation -->
            <div x-show="currentStep === 10" class="flex-1 flex flex-col justify-center items-center text-center">
                <div class="w-32 h-32 bg-yellow-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <span class="text-5xl">🏆</span>
                </div>
                <h2 class="text-3xl font-black mb-2 text-gray-800" x-text="`Score: ${score.toFixed(2)} / 5.00`"></h2>
                <div class="flex space-x-4 mb-8 text-sm font-bold">
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full"><span x-text="correctCount"></span> Correct</div>
                    <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full"><span x-text="wrongCount"></span> Wrong</div>
                    <div class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full"><span x-text="unattemptedCount"></span> Skipped</div>
                </div>
                
                <div class="w-full bg-blue-50 rounded-xl p-4 mb-6 text-left">
                    <h4 class="font-bold text-blue-800 mb-2">Earned Rewards</h4>
                    <div class="flex justify-between items-center text-blue-900 font-medium">
                        <span>XP Gained:</span>
                        <span class="text-lg font-black">+150 XP</span>
                    </div>
                    <div class="flex justify-between items-center text-blue-900 font-medium">
                        <span>Streak:</span>
                        <span class="text-lg font-black">3 Days 🔥</span>
                    </div>
                </div>

                <a href="/dashboard" class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-black transition">Back to Dashboard</a>
            </div>

        </div>
    </div>

    <script>
        function pscmSessionData() {
            return {
                session: {},
                currentStep: 0,
                practiceAnswers: [null, null, null, null],
                
                // OMR state
                omrQuestions: [],
                omrAnswers: [null, null, null, null, null],
                currentOmrQ: 0,
                omrTimeLeft: 300,
                omrTimer: null,
                
                // Results state
                score: 0,
                correctCount: 0,
                wrongCount: 0,
                unattemptedCount: 0,

                initData(sessionData) {
                    this.session = sessionData;
                    // Prepare OMR questions (Hook + 4 Practice)
                    if(this.session.hook_question && this.session.practice_mcqs) {
                        this.omrQuestions = [
                            this.session.hook_question,
                            ...this.session.practice_mcqs
                        ];
                    }
                },

                nextStep() {
                    let next = this.currentStep + 1;
                    
                    // Skip media screens if no data
                    if (next === 1 && !this.session.media?.video_url) next++;
                    if (next === 2 && !this.session.media?.image_url) next++;
                    
                    this.currentStep = next;
                },

                prevStep() {
                    let prev = this.currentStep - 1;
                    if (prev === 2 && !this.session.media?.image_url) prev--;
                    if (prev === 1 && !this.session.media?.video_url) prev--;
                    this.currentStep = Math.max(0, prev);
                },

                renderMarkdown(text) {
                    if(!text) return '';
                    // Basic mock markdown render for prototype
                    return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                               .replace(/\n/g, '<br>');
                },

                selectPracticeAnswer(mcqIdx, optIdx) {
                    if (this.practiceAnswers[mcqIdx] !== null) return; // Prevent re-answering
                    this.practiceAnswers[mcqIdx] = optIdx;
                },

                getPracticeBtnClass(mcqIdx, optIdx) {
                    const ans = this.practiceAnswers[mcqIdx];
                    const correctIdx = this.session.practice_mcqs[mcqIdx].correct_index;
                    
                    if (ans === null) return 'border-gray-200 hover:border-blue-500 hover:bg-blue-50';
                    
                    if (optIdx === correctIdx) return 'bg-green-100 border-green-500 text-green-900';
                    if (optIdx === ans && optIdx !== correctIdx) return 'bg-red-100 border-red-500 text-red-900';
                    
                    return 'border-gray-200 opacity-50';
                },

                startOMR() {
                    this.currentStep = 9;
                    this.omrTimeLeft = this.session.omr_config?.time_limit_seconds || 300;
                    this.omrTimer = setInterval(() => {
                        this.omrTimeLeft--;
                        if (this.omrTimeLeft <= 0) {
                            this.submitOMR();
                        }
                    }, 1000);
                },

                formatTime(seconds) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                },

                submitOMR() {
                    clearInterval(this.omrTimer);
                    
                    // Calculate score
                    this.correctCount = 0;
                    this.wrongCount = 0;
                    this.unattemptedCount = 0;

                    this.omrAnswers.forEach((ans, idx) => {
                        if (ans === null) {
                            this.unattemptedCount++;
                        } else if (ans === this.omrQuestions[idx].correct_index) {
                            this.correctCount++;
                        } else {
                            this.wrongCount++;
                        }
                    });

                    this.score = this.correctCount - (this.wrongCount * 0.33);
                    if(this.score < 0) this.score = 0;

                    this.currentStep = 10;
                    
                    if(this.score >= 3) {
                        confetti({
                            particleCount: 150,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    }

                    // In a real app, send data to backend via fetch/axios
                }
            }
        }
    </script>
</body>
</html>
