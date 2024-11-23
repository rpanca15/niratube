@extends('layouts.app')

@section('title')
    Niratube - Video Saya
@endsection

@section('content')
<div class="container mx-auto px-4 mt-[80px]">
    <div class="grid grid-cols-1 md:grid-cols-2 justify-between items-center mb-8">
        <!-- Grid Kiri (H2) -->
        <div>
            <h1 class="text-3xl font-bold">Video Saya</h1>
        </div>

        <!-- Grid Kanan (Search dan Upload) -->
        <div class="flex justify-end space-x-4">
            <!-- Form Pencarian -->
            <form action="{{ route('videos.index') }}" method="GET" id="searchForm" class="flex items-center">
                <input type="text" name="search" value="{{ request()->query('search') }}"
                    placeholder="Cari video..." class="px-4 py-2 border rounded-lg focus:outline-none"
                    id="searchInput" />
            </form>

            <!-- Tombol Upload -->
            <a href="{{ route('videos.create') }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-upload h-5 w-5 mr-2"></i>
                Upload
            </a>
        </div>
    </div>

    <!-- Daftar Video dalam Kartu -->
    <div class="overflow-x-auto">
        <div class="grid grid-cols-2 gap-4">
            @forelse($videos as $video)
                <div class="bg-white shadow-md rounded-lg p-3 text-sm flex items-center">
                    <a href="{{ route('videos.show', $video->id) }}" class="flex items-start">
                        <video class="video-preview w-60 h-auto rounded-lg mr-4" muted disablePictureInPicture
                            oncontextmenu="return false;">
                            <source src="{{ asset('storage/videos/' . $video->video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </a>
                    <div class="flex-1 h-auto">
                        <h2 class="flex-start text-lg font-semibold text-gray-800 mb-2">{{ $video->title }}</h2>
                        <div class="grid grid-cols-2 gap-1 text-xs text-gray-600">
                            <p class="flex items-center">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                <span class="font-medium">{{ $video->created_at->format('d M Y') }}</span>
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span class="font-medium">{{ $video->status }}</span>
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-tags mr-1"></i>
                                <span class="font-medium">{{ $video->category->name }}</span>
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-eye mr-1"></i>
                                <span class="font-medium">{{ $video->views }}</span>
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-thumbs-up mr-1"></i>
                                <span class="font-medium">{{ $video->likes_count }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                            action="{{ route('videos.destroy', $video->id) }}" method="POST">
                            <a href="{{ route('videos.edit', $video->id) }}"
                                class="bg-orange-600 text-white px-2 py-1 rounded-l-lg hover:bg-orange-700 transition inline-block">
                                <i class="fas fa-pen w-4 h-4"></i>
                            </a>
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-600 text-white px-2 py-1 rounded-r-lg hover:bg-red-700 transition">
                                <i class="fas fa-trash w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-4 px-4 text-center text-gray-500">Tidak ada video yang tersedia.</div>
            @endforelse
        </div>
        {{-- {{ $videos->links() }} --}}
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videos = document.querySelectorAll('.video-preview');
        videos.forEach(video => {
            function playVideo() {
                video.currentTime = 0;
                video.play();
            }
            video.addEventListener('mouseover', function() { playVideo(); });
            video.addEventListener('mouseleave', function() {
                video.pause();
                video.currentTime = 0;
            });
            video.addEventListener('timeupdate', function() {
                if (video.currentTime >= 5) {
                    video.currentTime = 0;
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        searchInput.addEventListener('input', function() { searchForm.submit(); });
    });
</script>
@endsection
