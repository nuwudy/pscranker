@extends('layouts.app')

@section('title', 'Meme Mnemonics Vault — PSCRanker')

@section('content')
<div class="py-8 sm:py-14 bg-gradient-to-b from-yellow-50/40 via-white to-slate-50 min-h-[85vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-yellow-400 to-amber-500 text-slate-950 flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg shadow-yellow-500/20">
                😂
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight">
                Meme Mnemonics Vault
            </h1>
            <p class="text-base font-bold text-[#0052FF] mt-1">
                ക്യാപ്സൂളിൽ കുടുങ്ങിയ ചോദ്യങ്ങൾ & നർമ്മോക്തികൾ
            </p>
            <p class="text-xs sm:text-sm text-slate-600 mt-2">
                Remember difficult PSC dates, Renaissance leaders, and SCERT science facts through unforgettable Malayali pop culture memes.
            </p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
            <a 
                href="{{ route('memebank') }}" 
                class="px-4 py-2 rounded-full text-xs font-black transition {{ !$categorySlug ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}"
            >
                All Memes
            </a>
            @foreach($categories as $cat)
                <a 
                    href="{{ route('memebank', ['category' => $cat->slug]) }}" 
                    class="px-4 py-2 rounded-full text-xs font-black transition {{ $categorySlug === $cat->slug ? 'bg-[#0052FF] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}"
                >
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        <!-- Meme Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($memes as $item)
                <div class="bg-white rounded-3xl border-2 border-slate-200/80 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                    
                    <div>
                        <!-- Category Badge & PYQ Tag -->
                        <div class="p-5 pb-3 flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-50 text-[#0052FF]">
                                {{ $item->category->name }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400">
                                {{ $item->psc_exam_reference ?? 'Kerala PSC PYQ' }}
                            </span>
                        </div>

                        <!-- Meme Graphic or Illustration -->
                        <div class="relative overflow-hidden bg-slate-950 mx-5 rounded-2xl border border-slate-200">
                            <img 
                                src="{{ $item->meme_image_url ?? '/images/meme_card.jpg' }}" 
                                alt="Meme Mnemonic" 
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                            <div class="absolute top-2 right-2 bg-yellow-400 text-slate-950 px-2 py-0.5 rounded text-[10px] font-black shadow">
                                Trap Alert
                            </div>
                        </div>

                        <!-- Content & Trap Commentary -->
                        <div class="p-5">
                            <h3 class="text-base font-black text-slate-900 leading-snug">
                                {{ $item->question_text_malayalam ?? $item->question_text }}
                            </h3>

                            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-2xl">
                                <div class="text-[11px] font-black text-red-600 uppercase flex items-center gap-1">
                                    <span>🚨 Trap Buster:</span>
                                </div>
                                <p class="text-xs font-bold text-red-800 mt-0.5 leading-relaxed">
                                    {{ $item->trap_warning ?? 'Don’t confuse similar sounding years or leaders!' }}
                                </p>
                            </div>

                            <div class="mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-2xl">
                                <div class="text-[11px] font-black text-emerald-700 uppercase flex items-center gap-1">
                                    <span>💡 ശരിയുത്തരം & വിശദീകരണം:</span>
                                </div>
                                <p class="text-xs font-medium text-emerald-950 mt-0.5 leading-relaxed">
                                    {{ $item->explanation_malayalam ?? $item->explanation }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Quick Drill CTA -->
                    <div class="p-5 pt-0">
                        <a 
                            href="{{ route('drill.show') }}" 
                            class="w-full py-2.5 bg-slate-100 hover:bg-[#FFD200] text-slate-900 font-extrabold text-xs rounded-xl transition flex items-center justify-center gap-1 group-hover:bg-[#FFD200]"
                        >
                            <span>Test This in Speed Drill</span>
                            <span>⚡</span>
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-3xl border-2 border-slate-100">
                    <p class="text-slate-500 font-bold">No memes found for this category yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $memes->links() }}
        </div>

    </div>
</div>
@endsection
