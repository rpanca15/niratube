@extends('layouts.app')

@section('title')
    Niratube
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-[80px]">
        @foreach ($videos as $video)
            <a href="{{ route('videos.show', $video->id) }}" class="bg-white shadow-md rounded-lg p-4">
                <video class="video-preview w-full h-auto rounded-lg mr-4" muted disablePictureInPicture
                    oncontextmenu="return false;">
                    <source src="{{ asset('storage/videos/' . $video->video) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <h3 class="text-lg font-semibold text-gray-800">{{ $video->title }}</h3>
                <p class="text-sm text-gray-600">{{ $video->category }}</p>
            </a>
        @endforeach
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
                video.addEventListener('mouseover', function() {
                    playVideo();
                });
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
    </script>
@endsection
