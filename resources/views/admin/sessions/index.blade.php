@extends('layouts.admin')

@section('title', 'Learning Sessions Manager — PSCRanker Admin')
@section('page_title', 'Learning Sessions & Tracks')
@section('page_subtitle', 'Manage sequential units, stackable blocks, and capstone OMR banks')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">All Learning Sessions</h2>
            <p class="text-xs text-slate-500 font-medium">Create and edit sequential units, content blocks, and question banks.</p>
        </div>
            <div class="flex items-center gap-2.5">
                <a 
                    href="{{ route('admin.categories.index') }}" 
                    class="px-4 py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-700 font-black text-xs rounded-xl border border-purple-200 transition flex items-center gap-1.5 shadow-2xs"
                >
                    <span>📚 Subject Tracks &amp; Categories</span>
                </a>
                <a 
                    href="{{ route('admin.mixed-practice.index') }}" 
                    class="px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-[#0052FF] font-black text-xs rounded-xl border border-blue-200 transition flex items-center gap-1.5 shadow-2xs"
                >
                    <span>🚂 Curate Mixed Practice Train ➔</span>
                </a>
                <a 
                    href="{{ route('admin.sessions.create') }}" 
                    class="px-5 py-2.5 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5"
                >
                    <span>+ Create New Session</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-xs font-bold text-emerald-900 flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100/75 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <th class="p-4"># Order</th>
                        <th class="p-4">Session Title</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Content Blocks</th>
                        <th class="p-4">Questions</th>
                        <th class="p-4">XP Reward</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="p-4 font-mono font-bold">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-800 text-xs font-black">
                                    Session #{{ $session->order }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $session->title }}</div>
                                @if($session->title_malayalam)
                                    <div class="text-[11px] text-[#0052FF] font-['Noto_Sans_Malayalam']">{{ $session->title_malayalam }}</div>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono">{{ $session->slug }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                                        {{ $session->category ? $session->category->name : 'Unassigned' }}
                                    </span>
                                    @if($session->in_general_stream)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-blue-100 text-blue-800 border border-blue-200">
                                            🚂 Train #{{ $session->general_stream_order ?? '1' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 font-mono font-bold">
                                @if($session->isCustomCode())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span>⚡ Custom HTML</span>
                                    </span>
                                @else
                                    {{ $session->contents_count }} blocks
                                @endif
                            </td>
                            <td class="p-4 font-mono font-bold">
                                @if($session->isCustomCode())
                                    <span class="text-[10px] text-slate-400 font-bold">Embedded in code</span>
                                @else
                                    {{ $session->questions_count }} questions
                                @endif
                            </td>
                            <td class="p-4 font-mono text-amber-600 font-bold">
                                +{{ $session->xp_reward }} XP
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col gap-1 items-start">
                                    @if($session->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 text-slate-600">Draft</span>
                                    @endif

                                    @if($session->access_level === 'premium' || $session->is_premium)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                            👑 PRO (Prepaid Pass)
                                        </span>
                                    @elseif($session->access_level === 'registered')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-900 border border-blue-200">
                                            🔵 Member Free
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            🟢 Free (All)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a 
                                        href="{{ route('session.show', $session->slug) }}" 
                                        target="_blank"
                                        class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-black text-xs transition inline-flex items-center gap-1 shadow-2xs"
                                        title="View Live Session Progression Stepper"
                                    >
                                        <span>👁️ Live ↗</span>
                                    </a>
                                    <a 
                                        href="{{ route('session.show', ['slug' => $session->slug, 'preview' => 'finished']) }}" 
                                        target="_blank"
                                        class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition inline-flex items-center gap-1"
                                        title="View Finished Session Scorecard"
                                    >
                                        <span>🏁 Finished</span>
                                    </a>
                                    <a 
                                        href="{{ route('admin.sessions.edit', $session) }}" 
                                        class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#0052FF] border border-blue-200 font-black text-xs transition inline-flex items-center gap-1"
                                    >
                                        <span>✏️ Edit</span>
                                    </a>
                                    <form 
                                        action="{{ route('admin.sessions.destroy', $session) }}" 
                                        method="POST" 
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to permanently delete session #{{ $session->id }} (\'{{ addslashes($session->title) }}\')? This will delete all its contents and questions.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="px-2 py-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg font-bold text-xs cursor-pointer transition"
                                            title="Delete Session"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                No sessions created yet. Click "+ Create New Session" to build your first modular learning track session!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sessions->links() }}
        </div>

    </div>
@endsection
