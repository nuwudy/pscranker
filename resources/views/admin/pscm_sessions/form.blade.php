@extends('layouts.admin')
@section('title', isset($session) ? 'Edit MicroLearning Session' : 'Create MicroLearning Session')
@section('content')

    <div class="py-10 bg-slate-50 min-h-screen" x-data="pscmAdminForm()" x-init="initData('{{ isset($session) ? $session->status : 'draft' }}', '{{ route('admin.pscm.generate') }}', '{{ old('category', $session->category ?? 'GK') }}', '{{ addslashes(old('title', $session->title ?? '')) }}')">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Top Header / Navigation --}}
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.pscm.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-900 shadow-sm transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <span class="text-xs font-bold text-indigo-600 tracking-wider uppercase">PSC MicroLearning Studio</span>
                        <h1 class="text-2xl font-black text-slate-900">{{ isset($session) ? 'Edit Micro-Session' : 'Create Micro-Session' }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="bg-white p-1 rounded-xl border border-slate-200 shadow-sm flex items-center gap-1">
                        <button type="button" @click="mode = 'auto'" :class="mode === 'auto' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                            <span>✨ Auto-Generate (AI)</span>
                        </button>
                        <button type="button" @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                            <span>⚙️ Review / Manual Media</span>
                        </button>
                    </div>

                    <button type="button" onclick="document.getElementById('sessionForm').submit();" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-200 transition">
                        <span>💾 Save Session</span>
                    </button>
                </div>
            </div>

            {{-- Main Form Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-16">
                
                <form id="sessionForm" action="{{ isset($session) ? route('admin.pscm.update', $session->id) : route('admin.pscm.store') }}" method="POST">
                    @csrf
                    @if(isset($session)) @method('PUT') @endif

                    @if ($errors->any())
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl mb-6 text-sm">
                            <p class="font-bold mb-1">Please fix the following errors:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Category Selection, Access Tier & Status Bar --}}
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                        <div class="sm:col-span-2">
                            <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">Subject Category <span class="text-rose-500">*</span></label>
                            <select name="category" x-model="category" class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-medium text-sm transition">
                                <option value="">Select Category...</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }} ({{ $key }})</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Learners filter units by this subject category.</p>
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">Access Layer <span class="text-amber-500 font-black">★</span></label>
                            <select name="access_level" x-model="accessLevel" class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-bold text-sm transition" :class="accessLevel === 'premium' ? 'border-amber-300 bg-amber-50/50 text-amber-900' : (accessLevel === 'registered' ? 'border-blue-300 bg-blue-50/50 text-blue-900' : '')">
                                <option value="premium">👑 Paid / PRO (Default)</option>
                                <option value="registered">👤 Registered (Free with Login)</option>
                                <option value="free">🎁 Free (Open to Everyone)</option>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1" x-text="accessLevel === 'premium' ? 'Requires active PRO subscription.' : (accessLevel === 'registered' ? 'Free for signed-in members.' : 'Publicly accessible without login.')"></p>
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">Publication Status</label>
                            <select name="status" x-model="status" class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-medium text-sm transition">
                                <option value="draft">Draft (Admin Only)</option>
                                <option value="published">Published (Visible to Students)</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="mb-6">
                        <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">Session Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="title" class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-semibold text-sm transition" placeholder="e.g. Writs under Indian Constitution & Supreme Court powers" required>
                    </div>

                    {{-- AI Auto-Generate Screen --}}
                    <template x-if="mode === 'auto'">
                        <div class="mb-6 bg-gradient-to-br from-indigo-50/70 via-purple-50/50 to-pink-50/30 p-5 rounded-2xl border border-indigo-100">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-indigo-950 font-bold text-sm">Paste Previous PSC Question / Screen OCR Text</label>
                                <span class="text-xs bg-indigo-100 text-indigo-700 font-semibold px-2.5 py-0.5 rounded-full">Gemini 1.5 Flash AI</span>
                            </div>
                            
                            <textarea x-model="rawHookQuestion" rows="5" class="w-full border border-indigo-200 rounded-xl p-3.5 text-slate-800 bg-white placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition" placeholder="Paste the raw text of the question here (including options A, B, C, D and Malayalam/English text). The AI will extract the question, parse options, auto-detect the subject category, compose the core lesson, and build 4 practice MCQs!"></textarea>
                            
                            <div class="mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <p class="text-xs text-indigo-600 font-medium">✨ Generates formatted lesson + 4 practice questions + OMR exam set in ~5 seconds.</p>
                                <button type="button" @click="generateAI" :disabled="loading" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-100 disabled:opacity-60 transition">
                                    <svg x-show="loading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-show="!loading">✨ Generate with AI (Gemini)</span>
                                    <span x-show="loading">Generating Lesson & MCQs...</span>
                                </button>
                            </div>
                            <p x-show="errorMsg" x-text="errorMsg" class="text-rose-600 font-medium mt-2 text-xs bg-rose-50 p-2 rounded-lg border border-rose-200"></p>
                        </div>
                    </template>

                    {{-- Manual / Detailed Fields Screen --}}
                    <div x-show="mode === 'manual'" style="display: none;">
                        <div class="space-y-6">
                            
                            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 flex items-center justify-between">
                                <div class="text-xs text-amber-800">
                                    <span class="font-bold">💡 Manual Review & Media Customization:</span> Review the generated text or add custom video/image hooks below.
                                </div>
                                <button type="button" @click="mode = 'auto'" class="text-xs text-amber-900 underline font-bold">Back to AI input</button>
                            </div>

                            {{-- 1. Hook Question --}}
                            <div>
                                <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">Hook Question (Structured JSON)</label>
                                <textarea name="hook_question" x-model="hookQuestion" rows="4" class="w-full font-mono text-xs border border-slate-200 rounded-xl p-3 text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition">{{ old('hook_question', is_array($session->hook_question ?? null) ? json_encode($session->hook_question, JSON_PRETTY_PRINT) : ($session->hook_question ?? '')) }}</textarea>
                            </div>

                            {{-- 2. Manually attach Video or Image between Hook Question & Core Lesson --}}
                            <div class="bg-gradient-to-br from-slate-50 to-indigo-50/40 p-5 rounded-2xl border-2 border-indigo-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🎬</span>
                                        <label class="text-slate-900 font-bold text-sm">Media Anchor (Video or Image of your choice)</label>
                                    </div>
                                    <span class="text-[11px] font-bold text-indigo-700 bg-indigo-100/80 px-2.5 py-0.5 rounded-full">Optional • Displays before Lesson</span>
                                </div>
                                <p class="text-xs text-slate-500 mb-4">Add a YouTube/MP4 video or diagram/meme image right after the hook question to enrich student retention. You can either paste a URL or directly upload a file!</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    
                                    {{-- Video Card (URL or Direct File Upload) --}}
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                                        <div>
                                            <label class="block text-slate-800 text-xs font-bold uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                                <span>🎥 Video Anchor</span>
                                                <span class="text-[10px] text-slate-400 font-normal">YouTube or MP4</span>
                                            </label>
                                            
                                            <input type="text" name="video_url" x-model="videoUrl" @input="if(videoUrl.trim()) imageUrl = ''" class="w-full border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 transition" placeholder="https://www.youtube.com/watch?v=... or direct / uploaded MP4 link">
                                            
                                            <!-- Direct Video File Upload Button -->
                                            <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition" :class="uploadingVideo ? 'opacity-50 pointer-events-none' : ''">
                                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    <span x-text="uploadingVideo ? 'Uploading Video...' : '📁 Upload Local Video'"></span>
                                                    <input type="file" accept="video/mp4,video/webm,video/quicktime" class="hidden" @change="uploadMediaFile($event, 'video')">
                                                </label>
                                                <button type="button" x-show="videoUrl" @click="videoUrl = ''" class="text-xs text-rose-500 hover:text-rose-700 font-medium">Remove</button>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-2">Plays right after the Hook Question.</p>
                                    </div>

                                    {{-- Image Card (URL or Direct File Upload) --}}
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                                        <div>
                                            <label class="block text-slate-800 text-xs font-bold uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                                <span>🖼️ Image / Infographic / Meme</span>
                                                <span class="text-[10px] text-slate-400 font-normal">JPG, PNG, WebP</span>
                                            </label>
                                            
                                            <input type="text" name="image_url" x-model="imageUrl" @input="if(imageUrl.trim()) videoUrl = ''" class="w-full border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 transition mb-2" placeholder="https://... or uploaded image link">
                                            <input type="text" name="image_caption" x-model="imageCaption" class="w-full border border-slate-200 rounded-lg p-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 transition" placeholder="Optional caption (e.g. 'Articles 32 & 226 Summary Table')">
                                            
                                            <!-- Direct Image File Upload Button -->
                                            <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition" :class="uploadingImage ? 'opacity-50 pointer-events-none' : ''">
                                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    <span x-text="uploadingImage ? 'Uploading Image...' : '📁 Upload Local Image'"></span>
                                                    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" @change="uploadMediaFile($event, 'image')">
                                                </label>
                                                <button type="button" x-show="imageUrl" @click="imageUrl = ''" class="text-xs text-rose-500 hover:text-rose-700 font-medium">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Live Upload / Preview Status Alert --}}
                                <div x-show="uploadMsg" class="mt-3 p-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs font-semibold flex items-center justify-between" x-text="uploadMsg"></div>

                                {{-- Live Preview Card if URL is provided --}}
                                <div x-show="imageUrl || videoUrl" class="mt-4 pt-3 border-t border-indigo-100 flex flex-wrap items-center gap-3 text-xs text-indigo-900 font-semibold">
                                    <span>Attached Media:</span>
                                    <span x-show="videoUrl" class="px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 flex items-center gap-1">
                                        <span>🎥 Video Active</span>
                                    </span>
                                    <span x-show="imageUrl" class="px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 flex items-center gap-1">
                                        <span>🖼️ Image Active</span>
                                    </span>
                                </div>
                            </div>

                            {{-- 3. Core Lesson Content --}}
                            <div>
                                <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">Core Lesson Content (Markdown)</label>
                                <textarea name="lesson_content" x-model="lessonContent" rows="10" class="w-full font-sans text-sm border border-slate-200 rounded-xl p-3.5 text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition" placeholder="Write high-yield SCERT/NCERT facts in markdown..."></textarea>
                            </div>
                            
                            {{-- 4. Practice MCQs --}}
                            <div>
                                <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">Practice MCQs (JSON Array)</label>
                                <textarea name="practice_mcqs" x-model="practiceMcqs" rows="8" class="w-full font-mono text-xs border border-slate-200 rounded-xl p-3 text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition">{{ old('practice_mcqs', json_encode($session->practice_mcqs ?? [])) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Form Footer CTA --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('admin.pscm.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-md shadow-emerald-100 transition">
                            <span>💾 Save MicroLearning Session</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function pscmAdminForm() {
            return {
                mode: 'auto',
                status: 'draft',
                accessLevel: `{{ old('access_level', $session->access_level ?? 'premium') }}`,
                category: 'GK',
                title: '',
                rawHookQuestion: '',
                videoUrl: `{{ old('video_url', $session->media['video_url'] ?? '') }}`,
                imageUrl: `{{ old('image_url', $session->media['image_url'] ?? '') }}`,
                imageCaption: `{{ old('image_caption', $session->media['image_caption'] ?? '') }}`,
                hookQuestion: `{!! addslashes(old('hook_question', is_array($session->hook_question ?? null) ? json_encode($session->hook_question, JSON_PRETTY_PRINT) : ($session->hook_question ?? ''))) !!}`,
                lessonContent: `{!! addslashes(old('lesson_content', $session->lesson_content ?? '')) !!}`,
                practiceMcqs: `{!! addslashes(old('practice_mcqs', json_encode($session->practice_mcqs ?? []))) !!}`,
                generateUrl: '',
                loading: false,
                uploadingVideo: false,
                uploadingImage: false,
                uploadMsg: '',
                errorMsg: '',

                initData(status, generateUrl, category, title) {
                    this.status = status || 'draft';
                    this.generateUrl = generateUrl;
                    if (category) this.category = category;
                    if (title) this.title = title;
                    
                    // If editing an existing session with structured question or media, switch to manual mode by default
                    const hookVal = this.hookQuestion.trim();
                    if((hookVal && hookVal.startsWith('{')) || this.videoUrl || this.imageUrl) {
                        this.mode = 'manual';
                    }
                },

                async uploadMediaFile(event, type) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (type === 'video') this.uploadingVideo = true;
                    if (type === 'image') this.uploadingImage = true;
                    this.uploadMsg = `Uploading ${file.name}...`;

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', document.querySelector('input[name="_token"]').value);

                    try {
                        const response = await fetch('{{ route('admin.media.store') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success && data.media && data.media.url) {
                            if (type === 'video') {
                                this.videoUrl = data.media.url;
                                this.imageUrl = '';
                                this.imageCaption = '';
                            } else {
                                this.imageUrl = data.media.url;
                                this.videoUrl = '';
                                if (!this.imageCaption) {
                                    this.imageCaption = data.media.name;
                                }
                            }
                            this.uploadMsg = `✅ Successfully uploaded: ${data.media.name}`;
                            setTimeout(() => { this.uploadMsg = ''; }, 5000);
                        } else {
                            alert('Upload failed: ' + (data.message || 'Unknown error occurred.'));
                            this.uploadMsg = '';
                        }
                    } catch (err) {
                        alert('Upload failed: ' + err.message);
                        this.uploadMsg = '';
                    } finally {
                        if (type === 'video') this.uploadingVideo = false;
                        if (type === 'image') this.uploadingImage = false;
                        event.target.value = '';
                    }
                },

                async generateAI() {
                    if (!this.rawHookQuestion.trim()) {
                        this.errorMsg = 'Please enter or paste the raw hook question text first.';
                        return;
                    }
                    
                    this.loading = true;
                    this.errorMsg = '';
                    
                    try {
                        const response = await fetch(this.generateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({ hook_question: this.rawHookQuestion })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Update models directly
                            this.lessonContent = data.lesson_content;
                            this.practiceMcqs = JSON.stringify(data.practice_mcqs, null, 2);
                            
                            if (data.hook_question_json) {
                                this.hookQuestion = JSON.stringify(data.hook_question_json, null, 2);
                            }
                            
                            if (data.category && (!this.category || this.category === 'GK')) {
                                this.category = data.category;
                            }

                            if (data.suggested_title && !this.title) {
                                this.title = data.suggested_title;
                            }
                            
                            // Switch to manual mode so user can review beautiful content and attach media
                            this.mode = 'manual';
                        } else {
                            this.errorMsg = data.message || 'Generation failed.';
                        }
                    } catch (e) {
                        this.errorMsg = e.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endsection
