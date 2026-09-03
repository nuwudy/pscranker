@extends('layouts.app')

@section('title', 'Media Library — PSCRanker Admin')

@section('content')
<div 
    x-data="mediaLibraryManager()"
    class="py-8 bg-slate-50 min-h-[90vh]"
>
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <!-- Top Header & Action Row -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                    <a href="{{ route('admin.sessions.index') }}" class="hover:text-[#0052FF]">← Sessions</a>
                    <span>/</span>
                    <span class="text-slate-800">Media Library</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">
                    Media Library (Photos, Audios, Videos)
                </h1>
                <p class="text-xs text-slate-500 font-medium">
                    Upload images, audio capsules, and explainer videos to use inside the Session Content Builder.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button 
                    @click="showUploadModal = true"
                    class="px-5 py-2.5 bg-[#0052FF] hover:bg-blue-700 active:scale-95 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-2"
                >
                    <span>⬆️ Upload New Media</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-xs font-bold text-emerald-900 flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-xl text-xs font-bold text-red-900">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs mb-6 flex flex-wrap items-center justify-between gap-4">
            
            <!-- Type Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-bold">
                <a 
                    href="{{ route('admin.media.index') }}" 
                    class="px-3 py-1.5 rounded-lg transition {{ !request('type') ? 'bg-slate-900 text-white font-black' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    All ({{ $stats['total'] }})
                </a>
                <a 
                    href="{{ route('admin.media.index', ['type' => 'image']) }}" 
                    class="px-3 py-1.5 rounded-lg transition {{ request('type') === 'image' ? 'bg-purple-600 text-white font-black' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}"
                >
                    🖼️ Photos ({{ $stats['images'] }})
                </a>
                <a 
                    href="{{ route('admin.media.index', ['type' => 'audio']) }}" 
                    class="px-3 py-1.5 rounded-lg transition {{ request('type') === 'audio' ? 'bg-blue-600 text-white font-black' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}"
                >
                    🎙️ Audio ({{ $stats['audios'] }})
                </a>
                <a 
                    href="{{ route('admin.media.index', ['type' => 'video']) }}" 
                    class="px-3 py-1.5 rounded-lg transition {{ request('type') === 'video' ? 'bg-red-600 text-white font-black' : 'bg-red-50 text-red-700 hover:bg-red-100' }}"
                >
                    🎬 Videos ({{ $stats['videos'] }})
                </a>
            </div>

            <!-- Search Field -->
            <form action="{{ route('admin.media.index') }}" method="GET" class="flex items-center gap-2">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Search by file name..." 
                    class="px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none w-48 sm:w-64"
                >
                <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition">
                    Search
                </button>
            </form>

        </div>

        <!-- Media Grid -->
        @if($mediaFiles->isEmpty())
            <div class="bg-white rounded-2xl border-2 border-dashed border-slate-300 p-12 text-center">
                <div class="text-4xl mb-3">📁</div>
                <h3 class="text-base font-black text-slate-800">No media files found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Upload your first photo mnemonic, 30s audio summary, or explainer video to start building rich multimedia capsules.
                </p>
                <button 
                    @click="showUploadModal = true"
                    class="mt-4 px-5 py-2 bg-[#0052FF] text-white text-xs font-bold rounded-xl shadow transition hover:bg-blue-700"
                >
                    Upload Media Now
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($mediaFiles as $file)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                        
                        <!-- Media Preview Container -->
                        <div class="h-44 bg-slate-100 flex items-center justify-center overflow-hidden relative">
                            
                            @if($file->file_type === 'image')
                                <img 
                                    src="{{ $file->url }}" 
                                    alt="{{ $file->name }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                    loading="lazy"
                                >
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-purple-600 text-white">
                                    Photo
                                </span>
                            @elseif($file->file_type === 'audio')
                                <div class="p-4 w-full text-center flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl mb-2">
                                        🎙️
                                    </div>
                                    <span class="text-[11px] font-mono font-bold text-slate-600 mb-2">Audio Track</span>
                                    <audio controls class="w-full h-8" preload="none">
                                        <source src="{{ $file->url }}" type="{{ $file->mime_type }}">
                                    </audio>
                                </div>
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-600 text-white">
                                    Audio
                                </span>
                            @elseif($file->file_type === 'video')
                                <div class="w-full h-full bg-slate-900 flex flex-col items-center justify-center p-2">
                                    <video controls class="w-full h-full max-h-36 object-contain" preload="metadata">
                                        <source src="{{ $file->url }}" type="{{ $file->mime_type }}">
                                    </video>
                                </div>
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-red-600 text-white">
                                    Video
                                </span>
                            @else
                                <div class="text-4xl text-slate-400">📄</div>
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-slate-600 text-white">
                                    File
                                </span>
                            @endif

                            <!-- Direct URL badge -->
                            <div class="absolute bottom-2 right-2">
                                <span class="text-[9px] font-mono font-bold bg-slate-900/80 text-white px-2 py-0.5 rounded">
                                    {{ $file->formatted_size }}
                                </span>
                            </div>

                        </div>

                        <!-- Card Body -->
                        <div class="p-3.5 flex flex-col justify-between flex-grow">
                            <div>
                                <h4 class="text-xs font-black text-slate-900 truncate" title="{{ $file->name }}">
                                    {{ $file->name }}
                                </h4>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 flex items-center justify-between">
                                    <span>{{ $file->created_at->format('M d, Y') }}</span>
                                    <span class="uppercase">{{ pathinfo($file->file_path, PATHINFO_EXTENSION) }}</span>
                                </div>
                            </div>

                            <!-- Actions Row -->
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                
                                <!-- Copy Link button -->
                                <button 
                                    @click="copyToClipboard('{{ $file->url }}')"
                                    class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold rounded-lg transition flex items-center gap-1 active:scale-95"
                                    title="Copy public URL"
                                >
                                    <span>📋 Copy Link</span>
                                </button>

                                <div class="flex items-center gap-2">
                                    <a 
                                        href="{{ $file->url }}" 
                                        target="_blank" 
                                        class="text-blue-600 hover:text-blue-800 text-[11px] font-bold p-1"
                                        title="View raw"
                                    >
                                        ↗
                                    </a>

                                    <form 
                                        action="{{ route('admin.media.destroy', $file) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Delete this media file permanently?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="text-red-400 hover:text-red-600 text-[11px] font-bold p-1"
                                            title="Delete file"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $mediaFiles->links() }}
            </div>
        @endif

    </div>

    <!-- Upload Modal -->
    <div 
        x-show="showUploadModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div 
            @click.outside="showUploadModal = false"
            class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 relative overflow-hidden"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⬆️</span>
                    <h3 class="text-base font-black text-slate-900">Upload Media File</h3>
                </div>
                <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold">
                    ✕
                </button>
            </div>

            <form 
                action="{{ route('admin.media.store') }}" 
                method="POST" 
                enctype="multipart/form-data" 
                class="space-y-4"
            >
                @csrf

                <!-- File Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Choose File (Photo / Audio / Video) *
                    </label>
                    <input 
                        type="file" 
                        name="file" 
                        required 
                        accept="image/*,audio/*,video/*,.pdf"
                        class="w-full text-xs font-semibold file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#0052FF] file:text-white hover:file:bg-blue-700 file:cursor-pointer border border-slate-200 rounded-xl p-2 bg-slate-50"
                    >
                    <p class="text-[10px] text-slate-400 mt-1">
                        Supported: JPG, PNG, WebP, SVG, MP3, WAV, OGG, MP4, WebM (Max 100MB).
                    </p>
                </div>

                <!-- Custom Title / Label -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Title / Label (Optional)
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        placeholder="e.g. Aruvipuram Consecration Timeline Card"
                        class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                    >
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="showUploadModal = false"
                        class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs rounded-xl shadow transition"
                    >
                        Upload to Library 🚀
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification for Copied URL -->
    <div 
        x-show="toastVisible" 
        x-transition 
        class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-2xl flex items-center gap-2 border border-slate-700"
        style="display: none;"
    >
        <span>✅</span>
        <span x-text="toastMessage"></span>
    </div>

</div>

@push('scripts')
<script>
function mediaLibraryManager() {
    return {
        showUploadModal: false,
        toastVisible: false,
        toastMessage: '',

        copyToClipboard(url) {
            navigator.clipboard.writeText(url).then(() => {
                this.toastMessage = 'Media URL copied to clipboard!';
                this.toastVisible = true;
                setTimeout(() => {
                    this.toastVisible = false;
                }, 2500);
            }).catch(err => {
                prompt('Copy URL:', url);
            });
        }
    };
}
</script>
@endpush
@endsection
