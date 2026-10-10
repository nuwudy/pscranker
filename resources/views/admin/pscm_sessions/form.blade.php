@extends('layouts.admin')
@section('title', isset($session) ? 'Edit MicroLearning Session' : 'Create MicroLearning Session')
@section('content')

    <div class="py-12" x-data="pscmAdminForm()" x-init="initData('{{ isset($session) ? $session->status : 'draft' }}', '{{ route('admin.pscm.generate') }}')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex justify-between items-center">
                <div class="flex space-x-4">
                    <button type="button" @click="mode = 'auto'" :class="{'bg-blue-600 text-white': mode === 'auto', 'bg-gray-200': mode !== 'auto'}" class="px-4 py-2 rounded">Auto-Generate (AI)</button>
                    <button type="button" @click="mode = 'manual'" :class="{'bg-blue-600 text-white': mode === 'manual', 'bg-gray-200': mode !== 'manual'}" class="px-4 py-2 rounded">Manual Override</button>
                </div>
                <button type="button" onclick="document.getElementById('sessionForm').submit();" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-lg text-lg animate-pulse">
                    💾 Save Session
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-20">
                
                <form id="sessionForm" action="{{ isset($session) ? route('admin.pscm.update', $session->id) : route('admin.pscm.store') }}" method="POST">
                    @csrf
                    @if(isset($session)) @method('PUT') @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                        <input type="text" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="{{ old('title', $session->title ?? '') }}" required>
                    </div>

                    <template x-if="mode === 'auto'">
                        <div class="bg-blue-50 p-4 rounded mb-4 border border-blue-200">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Raw Question Text (Paste here)</label>
                            <textarea x-model="rawHookQuestion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder='Type or paste the raw PSC question here...'></textarea>
                            <p class="text-sm text-blue-700 mt-2 mb-2">Enter the hook question above and AI will generate the lesson and MCQs.</p>
                            <button type="button" @click="generateAI" :disabled="loading" class="bg-purple-600 text-white px-4 py-2 rounded disabled:opacity-50 flex items-center">
                                <span x-show="!loading">Generate with AI (Gemini)</span>
                                <span x-show="loading">Generating... Please wait...</span>
                            </button>
                            <p x-show="errorMsg" x-text="errorMsg" class="text-red-500 mt-2 text-sm"></p>
                        </div>
                    </template>



                    <div x-show="mode === 'manual'" style="display: none;">
                        <div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Hook Question (JSON)</label>
                                <textarea name="hook_question" x-model="hookQuestion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('hook_question', is_array($session->hook_question ?? null) ? json_encode($session->hook_question, JSON_PRETTY_PRINT) : ($session->hook_question ?? '')) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Lesson Content (Markdown)</label>
                                <textarea name="lesson_content" x-model="lessonContent" rows="10" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Practice MCQs (JSON)</label>
                                <textarea name="practice_mcqs" x-model="practiceMcqs" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Media URLs (JSON)</label>
                                <textarea name="media" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('media', json_encode($session->media ?? [])) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                        <select name="status" x-model="status" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between">
                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                            Save Session
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
                rawHookQuestion: '',
                hookQuestion: `{!! addslashes(old('hook_question', is_array($session->hook_question ?? null) ? json_encode($session->hook_question, JSON_PRETTY_PRINT) : ($session->hook_question ?? ''))) !!}`,
                lessonContent: `{!! addslashes(old('lesson_content', $session->lesson_content ?? '')) !!}`,
                practiceMcqs: `{!! addslashes(old('practice_mcqs', json_encode($session->practice_mcqs ?? []))) !!}`,
                generateUrl: '',
                loading: false,
                errorMsg: '',

                initData(status, generateUrl) {
                    this.status = status;
                    this.generateUrl = generateUrl;
                    
                    // If editing an existing session, switch to manual mode by default
                    const hookVal = this.hookQuestion.trim();
                    if(hookVal && hookVal.startsWith('{')) {
                        this.mode = 'manual';
                    }
                },

                async generateAI() {
                    if (!this.rawHookQuestion) {
                        this.errorMsg = 'Please enter a raw hook question first.';
                        return;
                    }
                    
                    this.loading = true;
                    this.errorMsg = '';
                    
                    try {
                        const response = await fetch(this.generateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({ hook_question: this.rawHookQuestion })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Update alpine models directly
                            this.lessonContent = data.lesson_content;
                            this.practiceMcqs = JSON.stringify(data.practice_mcqs, null, 2);
                            if(data.hook_question_json) {
                                this.hookQuestion = JSON.stringify(data.hook_question_json, null, 2);
                            }
                            
                            // Switch to manual mode to review
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
