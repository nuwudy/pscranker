@extends('layouts.app')

@section('title', 'Admin Session Manager — PSCRanker')

@section('content')
<div class="py-8 bg-slate-50 min-h-[85vh]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Learning Sessions Manager</h1>
                <p class="text-xs text-slate-500 font-medium">Create and edit 4-phase micro-learning sessions, content blocks, and question banks.</p>
            </div>
            <a 
                href="{{ route('admin.sessions.create') }}" 
                class="px-5 py-2.5 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5"
            >
                <span>+ Create New Session</span>
            </a>
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
                            <td class="p-4 font-mono font-bold">{{ $session->order }}</td>
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

                                    @if($session->is_premium)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                            👑 PRO (Prepaid Pass)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-700">
                                            FREE
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <a 
                                    href="{{ route('session.show', $session->slug) }}" 
                                    target="_blank"
                                    class="text-blue-600 hover:underline font-bold text-xs"
                                >
                                    Preview ↗
                                </a>
                                <a 
                                    href="{{ route('admin.sessions.edit', $session) }}" 
                                    class="text-[#0052FF] hover:underline font-black text-xs"
                                >
                                    Edit
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
                                        class="text-red-600 hover:text-red-800 font-bold hover:underline text-xs cursor-pointer ml-1"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                No sessions created yet. Click "+ Create New Session" to build your first 4-phase micro-lesson!
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
</div>
@endsection
