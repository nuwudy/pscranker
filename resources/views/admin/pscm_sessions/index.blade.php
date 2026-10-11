@extends('layouts.admin')
@section('title', 'MicroLearning Sessions')
@section('content')

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb / Header Area --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold uppercase tracking-wider mb-2">
                        <span>✨ PSC Micro-Learning Hub</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">MicroLearning Sessions</h1>
                    <p class="text-slate-500 text-sm mt-1">Manage, categorize, generate and monitor bite-sized PSC interactive modules.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('learner.pscm.index') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold shadow-sm transition">
                        <span>👁️ Student Catalog</span>
                    </a>
                    <a href="{{ route('admin.pscm.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold shadow-md shadow-indigo-100 hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Create New Session</span>
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">🎉</span>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif

            {{-- Category Filter Pills Bar --}}
            <div class="mt-8 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 scrollbar-none text-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Categories:</span>
                    <a href="{{ route('admin.pscm.index') }}" 
                       class="px-3 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ !$selectedCategory ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        All ({{ \App\Models\PscmSession::count() }})
                    </a>
                    @foreach($categories as $key => $label)
                        @php
                            $catCount = \App\Models\PscmSession::where('category', $key)->count();
                        @endphp
                        <a href="{{ route('admin.pscm.index', ['category' => $key]) }}" 
                           class="px-3 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ $selectedCategory === $key ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $key }} <span class="text-xs opacity-75">({{ $catCount }})</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Sessions Table Card --}}
            <div class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                @if($sessions->isEmpty())
                    <div class="py-16 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center text-3xl">
                            📚
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">No sessions found</h3>
                        <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">There are no micro-learning sessions created for this category yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.pscm.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 shadow-sm">
                                <span>+ Create First Session</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Session Details</th>
                                    <th class="py-3.5 px-4">Category</th>
                                    <th class="py-3.5 px-4">Access Layer</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4">Created</th>
                                    <th class="py-3.5 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($sessions as $session)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900 group-hover:text-blue-600 transition">
                                            {{ $session->title }}
                                        </div>
                                        <div class="text-xs text-slate-400 mt-0.5 font-mono truncate max-w-xs">
                                            /{{ $session->slug }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($session->category)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                                🏷️ {{ $categories[$session->category] ?? $session->category }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-400">
                                                Uncategorized
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if(($session->access_level ?? 'premium') === 'premium')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-800 border border-amber-200">
                                                👑 Paid / PRO
                                            </span>
                                        @elseif($session->access_level === 'registered')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                👤 Registered
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                🎁 Free
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($session->status === 'published')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                            </span>
                                        @elseif($session->status === 'draft')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-xs text-slate-500">
                                        {{ $session->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('learner.pscm.show', $session->slug) }}" 
                                               target="_blank" 
                                               class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                                               title="View as Student">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <a href="{{ route('admin.pscm.edit', $session->id) }}" 
                                               class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                               title="Edit Session">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            
                                            {{-- Delete Button with cute modal/confirmation --}}
                                            <form action="{{ route('admin.pscm.destroy', $session->id) }}" 
                                                  method="POST" 
                                                  class="inline-block"
                                                  onsubmit="return confirm('Are you sure you want to delete this session? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                        title="Delete Session">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($sessions->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-slate-50">
                            {{ $sessions->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
@endsection
