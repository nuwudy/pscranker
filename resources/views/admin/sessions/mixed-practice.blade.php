@extends('layouts.app')

@section('title', 'Mixed Practice Train Concocter — PSCRanker Admin')

@section('content')
<div 
    x-data="mixedTrainConcocter({
        train: @js($mixedTrain),
        sessions: @js($availableSessions),
        categories: @js($categories),
        toggleUrl: @js(route('admin.mixed-practice.toggle')),
        reorderUrl: @js(route('admin.mixed-practice.reorder')),
        csrfToken: '{{ csrf_token() }}'
    })"
    class="py-8 bg-slate-50 min-h-[90vh]"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Breadcrumbs & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                    <a href="{{ route('admin.sessions.index') }}" class="hover:text-[#0052FF]">
                        ← Sessions List
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-black">Mixed Practice Train</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-950 flex items-center gap-2.5">
                    <span>🚂 Mixed Practice Train Concocter</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                    Curate the sequential train launched when students click <strong>[START COURSE UNITS ➔]</strong> on the homepage. Subject tracks remain completely independent.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a 
                    href="{{ route('sessions.index') }}" 
                    target="_blank"
                    class="px-4 py-2 bg-white text-slate-700 hover:text-[#0052FF] text-xs font-black rounded-xl border border-slate-300 hover:border-[#0052FF] transition flex items-center gap-1.5 shadow-2xs"
                >
                    <span>👁️ View Student Catalog ↗</span>
                </a>
                <a 
                    href="{{ route('admin.sessions.create') }}" 
                    class="px-4 py-2 bg-[#0052FF] hover:bg-blue-700 text-white text-xs font-black rounded-xl transition flex items-center gap-1.5 shadow-sm"
                >
                    <span>+ Create New Unit</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-xs font-bold text-emerald-900 flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Main 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- ======================================================== -->
            <!-- LEFT COLUMN: ACTIVE MIXED PRACTICE TRAIN (Cols 5)        -->
            <!-- ======================================================== -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-3xl border-2 border-blue-200/80 p-5 sm:p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base font-black shadow-xs">
                                🚂
                            </span>
                            <div>
                                <h3 class="text-base font-black text-slate-900">Active Mixed Train</h3>
                                <p class="text-[11px] text-slate-500 font-medium">
                                    <span x-text="trainList.length"></span> Units in Sequential Order
                                </p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-blue-100 text-[#0052FF] border border-blue-200">
                            Live on Homepage
                        </span>
                    </div>

                    <!-- Train List Container -->
                    <div class="space-y-3 max-h-[700px] overflow-y-auto pr-1">
                        <template x-if="trainList.length === 0">
                            <div class="py-12 text-center text-slate-400">
                                <span class="text-4xl block mb-2">🚂</span>
                                <h4 class="font-black text-sm text-slate-700">The Mixed Train is currently empty</h4>
                                <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                                    Pick units from the Subject Pool on the right by clicking <strong>"+ Add to Train"</strong> to build the sequence.
                                </p>
                            </div>
                        </template>

                        <template x-for="(item, idx) in trainList" :key="item.id">
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 bg-slate-50/70 hover:border-[#0052FF] hover:bg-white transition-all flex items-center justify-between gap-3 group">
                                <div class="flex items-start gap-3 min-w-0">
                                    <!-- Step Number Badge with Direct Input -->
                                    <div class="flex flex-col items-center gap-1 shrink-0 mt-0.5">
                                        <div class="relative group/step">
                                            <input 
                                                type="number" 
                                                :value="idx + 1"
                                                @change="changeStepDirect(idx, parseInt($event.target.value))"
                                                min="1"
                                                :max="trainList.length"
                                                class="w-11 h-8 rounded-xl bg-[#0052FF] text-white text-center font-black text-xs shadow-xs focus:ring-2 focus:ring-yellow-400 focus:bg-blue-700 outline-none cursor-pointer"
                                                title="Step number in mixed practice train. Type any number (e.g. 1) to jump directly."
                                            >
                                        </div>
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Step</span>
                                    </div>

                                    <div class="min-w-0">
                                        <!-- Subject & Unit Tag -->
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span 
                                                class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider"
                                                :class="getSubjectTagClass(item.category ? item.category.slug : '')"
                                                x-text="item.category ? item.category.name : 'Subject'"
                                            ></span>
                                            <span class="text-[10px] font-bold text-slate-400" x-text="'Unit #' + item.order"></span>
                                            <template x-if="item.is_premium">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-200 text-amber-900">👑 PRO</span>
                                            </template>
                                        </div>

                                        <h4 class="text-xs sm:text-sm font-black text-slate-900 truncate" x-text="item.title"></h4>
                                        <template x-if="item.title_malayalam">
                                            <p class="text-[11px] font-bold text-[#0052FF] font-['Noto_Sans_Malayalam'] truncate" x-text="item.title_malayalam"></p>
                                        </template>
                                    </div>
                                </div>

                                <!-- Action Buttons: Move to Top, Move Up, Down, Edit, Remove -->
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        type="button" 
                                        @click="changeStepDirect(idx, 1)" 
                                        x-show="idx > 0"
                                        class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#0052FF] border border-blue-200 text-[10px] font-black transition cursor-pointer"
                                        title="Move to Step #1 (Front of Mixed Practice Train)"
                                    >
                                        Top #1
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="moveStep(idx, -1)" 
                                        :disabled="idx === 0"
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 disabled:opacity-30 disabled:pointer-events-none flex items-center justify-center font-black text-xs transition cursor-pointer"
                                        title="Move Up 1 Step"
                                    >
                                        ▲
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="moveStep(idx, 1)" 
                                        :disabled="idx === trainList.length - 1"
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 disabled:opacity-30 disabled:pointer-events-none flex items-center justify-center font-black text-xs transition cursor-pointer"
                                        title="Move Down 1 Step"
                                    >
                                        ▼
                                    </button>
                                    <a 
                                        :href="'/admin/sessions/' + item.id + '/edit'" 
                                        target="_blank"
                                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-black text-xs transition ml-0.5"
                                        title="Edit Session in New Tab"
                                    >
                                        ✏️
                                    </a>
                                    <button 
                                        type="button" 
                                        @click="toggleSession(item.id)" 
                                        class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 flex items-center justify-center font-black text-xs transition ml-0.5 cursor-pointer"
                                        title="Remove from Train"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Quick Guidance Note -->
                    <div class="mt-4 p-3 bg-blue-50/70 border border-blue-200 rounded-xl text-[11px] text-blue-900 flex items-start gap-2">
                        <span>💡</span>
                        <span>
                            When students click <strong>START COURSE UNITS ➔</strong>, Step #1 opens first. Finishing Step #1 automatically points them to Step #2 in the runner.
                        </span>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- RIGHT COLUMN: SUBJECT SESSIONS POOL (Cols 7)             -->
            <!-- ======================================================== -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 mb-4">
                        <div>
                            <h3 class="text-base font-black text-slate-900">Subject Sessions Pool</h3>
                            <p class="text-xs text-slate-500 font-medium">
                                Showing <strong>recently added sessions first</strong>. Search or filter by subject to add to Mixed Practice.
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 self-start sm:self-auto">
                            <span x-text="filteredSessions.length"></span> Available Sessions
                        </span>
                    </div>

                    <!-- Live Instant Search Bar -->
                    <div class="relative mb-3">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            placeholder="Search by title (English or Malayalam), unit number, or keyword..." 
                            class="w-full pl-9 pr-8 py-2.5 bg-slate-50 text-xs font-bold rounded-xl border border-slate-200 focus:border-[#0052FF] focus:bg-white focus:outline-none transition"
                        >
                        <button 
                            type="button" 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-black"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Subject Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-1.5 mb-5 text-[11px] font-bold">
                        <button 
                            type="button" 
                            @click="selectedCategory = 'all'" 
                            :class="selectedCategory === 'all' ? 'bg-slate-950 text-white font-black shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-2.5 py-1 rounded-lg transition cursor-pointer"
                        >
                            All Subjects
                        </button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button 
                                type="button" 
                                @click="selectedCategory = cat.slug" 
                                :class="selectedCategory === cat.slug ? 'bg-[#0052FF] text-white font-black shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1"
                            >
                                <span x-text="getSubjectIcon(cat.slug)"></span>
                                <span x-text="cat.name"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Available Sessions Cards List -->
                    <div class="space-y-3 max-h-[640px] overflow-y-auto pr-1">
                        <template x-if="filteredSessions.length === 0">
                            <div class="py-12 text-center text-slate-400">
                                <span class="text-3xl block mb-2">🔍</span>
                                <p class="text-xs font-bold text-slate-600">No sessions match your search criteria</p>
                                <button type="button" @click="searchQuery = ''; selectedCategory = 'all'" class="text-xs font-bold text-[#0052FF] hover:underline mt-1">Clear Filters</button>
                            </div>
                        </template>

                        <template x-for="item in filteredSessions" :key="item.id">
                            <div 
                                class="p-3.5 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                :class="isInTrain(item.id) ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-xs'"
                            >
                                <div class="min-w-0 flex-grow">
                                    <div class="flex flex-wrap items-center gap-1.5 mb-1 text-[10px]">
                                        <!-- Subject Badge -->
                                        <span 
                                            class="px-2 py-0.5 rounded font-black uppercase tracking-wider"
                                            :class="getSubjectTagClass(item.category ? item.category.slug : '')"
                                            x-text="item.category ? item.category.name : 'Subject'"
                                        ></span>
                                        <span class="font-bold text-slate-500" x-text="'Unit #' + item.order"></span>
                                        <span class="text-slate-300">•</span>
                                        <span class="font-mono text-slate-400" x-text="formatDate(item.created_at)"></span>
                                        <template x-if="item.is_premium">
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-200 text-amber-900">👑 PRO</span>
                                        </template>
                                    </div>

                                    <h4 class="text-xs sm:text-sm font-black text-slate-900" x-text="item.title"></h4>
                                    <template x-if="item.title_malayalam">
                                        <p class="text-[11px] font-bold text-[#0052FF] font-['Noto_Sans_Malayalam'] mt-0.5" x-text="item.title_malayalam"></p>
                                    </template>
                                </div>

                                <!-- 1-Click Action Toggle -->
                                <div class="shrink-0">
                                    <button 
                                        type="button" 
                                        @click="toggleSession(item.id)" 
                                        class="w-full sm:w-auto px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 cursor-pointer"
                                        :class="isInTrain(item.id) 
                                            ? 'bg-emerald-100 hover:bg-red-50 text-emerald-800 hover:text-red-700 border border-emerald-300 hover:border-red-300' 
                                            : 'bg-[#0052FF] hover:bg-blue-700 text-white shadow-xs'"
                                    >
                                        <template x-if="isInTrain(item.id)">
                                            <span>✓ In Train (Step #<span x-text="getStepNumber(item.id)"></span>)</span>
                                        </template>
                                        <template x-if="!isInTrain(item.id)">
                                            <span>+ Add to Mixed Practice</span>
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script>
function mixedTrainConcocter(config) {
    return {
        trainList: config.train || [],
        sessionPool: config.sessions || [],
        categories: config.categories || [],
        toggleUrl: config.toggleUrl,
        reorderUrl: config.reorderUrl,
        csrfToken: config.csrfToken,
        searchQuery: '',
        selectedCategory: 'all',
        isSubmitting: false,

        get filteredSessions() {
            let list = this.sessionPool;

            // 1. Filter by category
            if (this.selectedCategory !== 'all') {
                list = list.filter(s => s.category && s.category.slug === this.selectedCategory);
            }

            // 2. Filter by search query
            if (this.searchQuery && this.searchQuery.trim().length > 0) {
                const q = this.searchQuery.trim().toLowerCase();
                list = list.filter(s => {
                    const title = (s.title || '').toLowerCase();
                    const titleMl = (s.title_malayalam || '').toLowerCase();
                    const slug = (s.slug || '').toLowerCase();
                    const cat = s.category ? (s.category.name || '').toLowerCase() : '';
                    return title.includes(q) || titleMl.includes(q) || slug.includes(q) || cat.includes(q);
                });
            }

            return list;
        },

        isInTrain(sessionId) {
            return this.trainList.some(s => s.id === sessionId);
        },

        getStepNumber(sessionId) {
            const index = this.trainList.findIndex(s => s.id === sessionId);
            return index >= 0 ? (index + 1) : null;
        },

        async toggleSession(sessionId) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;

            try {
                const response = await fetch(this.toggleUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ session_id: sessionId })
                });

                const data = await response.json();
                if (data.success) {
                    this.trainList = data.mixedTrain || [];
                    // Update session in pool
                    const poolIndex = this.sessionPool.findIndex(s => s.id === sessionId);
                    if (poolIndex >= 0) {
                        this.sessionPool[poolIndex].in_general_stream = data.in_general_stream;
                        this.sessionPool[poolIndex].general_stream_order = data.general_stream_order;
                    }
                } else {
                    alert('Could not update train: ' + (data.message || 'Please try again.'));
                }
            } catch (err) {
                console.error('Toggle error:', err);
                alert('Network error updating train.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async moveStep(currentIndex, direction) {
            const targetIndex = currentIndex + direction;
            if (targetIndex < 0 || targetIndex >= this.trainList.length) return;

            const [item] = this.trainList.splice(currentIndex, 1);
            this.trainList.splice(targetIndex, 0, item);

            await this.saveOrderToServer();
        },

        async changeStepDirect(currentIndex, newStepNumber) {
            if (isNaN(newStepNumber) || newStepNumber < 1) newStepNumber = 1;
            if (newStepNumber > this.trainList.length) newStepNumber = this.trainList.length;
            const targetIndex = newStepNumber - 1;
            if (targetIndex === currentIndex) return;

            const [item] = this.trainList.splice(currentIndex, 1);
            this.trainList.splice(targetIndex, 0, item);

            await this.saveOrderToServer();
        },

        async saveOrderToServer() {
            const orderedIds = this.trainList.map(s => s.id);
            try {
                const response = await fetch(this.reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ordered_ids: orderedIds })
                });
                const data = await response.json();
                if (data.success && data.mixedTrain) {
                    this.trainList = data.mixedTrain;
                }
            } catch (err) {
                console.error('Reorder error:', err);
            }
        },

        getSubjectIcon(slug) {
            const icons = {
                'english': '📖',
                'maths': '🔢',
                'science': '🔬',
                'history': '🏛️',
                'geography': '🌍',
                'current-affairs': '📰',
                'map-study': '🌐'
            };
            return icons[slug] || '⚡';
        },

        getSubjectTagClass(slug) {
            const classes = {
                'english': 'bg-blue-100 text-blue-800',
                'maths': 'bg-amber-100 text-amber-800',
                'science': 'bg-emerald-100 text-emerald-800',
                'history': 'bg-purple-100 text-purple-800',
                'geography': 'bg-teal-100 text-teal-800',
                'current-affairs': 'bg-red-100 text-red-800',
                'map-study': 'bg-indigo-100 text-indigo-800',
            };
            return classes[slug] || 'bg-slate-100 text-slate-700';
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-IN', { month: 'short', day: 'numeric' });
        }
    };
}
</script>
@endpush
@endsection
