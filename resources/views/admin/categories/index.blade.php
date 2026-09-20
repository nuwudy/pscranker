@extends('layouts.admin')

@section('title', 'Subject Tracks & Categories — PSCRanker Admin')
@section('page_title', 'Subject Tracks & Categories')
@section('page_subtitle', 'Manage Kerala PSC exam subject areas and their order in learning tracks')

@section('content')
<div class="space-y-6" x-data="{
    showCreateModal: false,
    editingCategory: null,
    formData: {
        name: '',
        name_malayalam: '',
        slug: '',
        badge_color: 'blue',
        description: '',
        order: 0
    },
    openCreate() {
        this.editingCategory = null;
        this.formData = {
            name: '',
            name_malayalam: '',
            slug: '',
            badge_color: 'blue',
            description: '',
            order: {{ count($categories) + 1 }}
        };
        this.showCreateModal = true;
    },
    openEdit(cat) {
        this.editingCategory = cat;
        this.formData = {
            id: cat.id,
            name: cat.name,
            name_malayalam: cat.name_malayalam || '',
            slug: cat.slug,
            badge_color: cat.badge_color || 'blue',
            description: cat.description || '',
            order: cat.order
        };
        this.showCreateModal = true;
    }
}">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('admin.sessions.index') }}" class="text-xs font-bold text-slate-500 hover:text-[#0052FF]">
                        ← Back to Sessions
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-800">Subject Categories</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">PSC Subject Tracks & Categories</h1>
                <p class="text-xs text-slate-500 font-medium">Manage competitive exam subjects (General Knowledge, Quantitative Aptitude, English, etc.) and their ordering.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <button 
                    type="button"
                    @click="openCreate()"
                    class="px-5 py-2.5 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer"
                >
                    <span>+ Add New Subject</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-xs font-bold text-emerald-900 flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-xl text-xs font-bold text-red-900 flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Categories Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100/75 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <th class="p-4"># Order</th>
                        <th class="p-4">Subject Track</th>
                        <th class="p-4">Malayalam Title</th>
                        <th class="p-4">Sessions Count</th>
                        <th class="p-4">Questions Count</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="p-4 font-mono font-bold">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-800 text-xs font-black">
                                    #{{ $cat->order }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->badge_color === 'blue' ? '#3B82F6' : ($cat->badge_color === 'emerald' ? '#10B981' : ($cat->badge_color === 'purple' ? '#8B5CF6' : ($cat->badge_color === 'amber' ? '#F59E0B' : '#64748B'))) }}"></span>
                                    <span>{{ $cat->name }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">/{{ $cat->slug }}</div>
                                @if($cat->description)
                                    <div class="text-[11px] text-slate-500 mt-1 line-clamp-1">{{ $cat->description }}</div>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-800 font-['Noto_Sans_Malayalam']">
                                    {{ $cat->name_malayalam ?: '—' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#0052FF] border border-blue-100">
                                    {{ $cat->sessions_count }} Sessions
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                    {{ $cat->questions_count }} MCQs
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        type="button" 
                                        @click="openEdit(@js($cat))" 
                                        class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 transition cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    @if($cat->sessions_count === 0)
                                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete subject category {{ $cat->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 font-bold hover:bg-red-50 transition cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                No subject categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal for Create / Edit Category -->
        <div 
            x-show="showCreateModal" 
            x-cloak 
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.away="showCreateModal = false"
                class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 relative"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="editingCategory ? 'Edit Subject Category' : 'Create New Subject Track'"></h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <form :action="editingCategory ? ('/admin/categories/' + editingCategory.id) : '{{ route('admin.categories.store') }}'" method="POST">
                    @csrf
                    <template x-if="editingCategory">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4 text-xs font-bold text-slate-700">
                        <div>
                            <label class="block uppercase tracking-wide mb-1">Subject Name (English) *</label>
                            <input 
                                type="text" 
                                name="name" 
                                x-model="formData.name" 
                                required 
                                placeholder="e.g. Quantitative Aptitude" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:border-[#0052FF] focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block uppercase tracking-wide mb-1">Subject Name (Malayalam)</label>
                            <input 
                                type="text" 
                                name="name_malayalam" 
                                x-model="formData.name_malayalam" 
                                placeholder="e.g. ഗണിതവും ക്വാണ്ടിറ്റേറ്റീവ് അഭിരുചിയും" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:border-[#0052FF] focus:outline-none font-['Noto_Sans_Malayalam']"
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block uppercase tracking-wide mb-1">Slug (URL)</label>
                                <input 
                                    type="text" 
                                    name="slug" 
                                    x-model="formData.slug" 
                                    placeholder="quantitative-aptitude" 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:border-[#0052FF] focus:outline-none font-mono"
                                >
                            </div>
                            <div>
                                <label class="block uppercase tracking-wide mb-1">Display Order</label>
                                <input 
                                    type="number" 
                                    name="order" 
                                    x-model="formData.order" 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:border-[#0052FF] focus:outline-none"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block uppercase tracking-wide mb-1">Badge Color</label>
                            <select 
                                name="badge_color" 
                                x-model="formData.badge_color"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:border-[#0052FF] focus:outline-none"
                            >
                                <option value="blue">Blue (General Knowledge / Default)</option>
                                <option value="emerald">Emerald (Maths & Quantitative)</option>
                                <option value="indigo">Indigo (English Language)</option>
                                <option value="purple">Purple (History & Renaissance)</option>
                                <option value="amber">Amber (General Science)</option>
                                <option value="rose">Rose (Current Affairs)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block uppercase tracking-wide mb-1">Description / Syllabus Scope</label>
                            <textarea 
                                name="description" 
                                x-model="formData.description" 
                                rows="3" 
                                placeholder="Core syllabus concepts and PSC exam points covered in this track..."
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:border-[#0052FF] focus:outline-none"
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="showCreateModal = false" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-xs cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 bg-[#0052FF] hover:bg-blue-700 text-white font-black rounded-lg transition text-xs shadow-md cursor-pointer"
                        >
                            Save Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
