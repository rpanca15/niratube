@extends('layouts.app')

@section('title')
    Niratube - {{ $video->title }}
@endsection

@section('content')
    <div class="container mx-auto mt-[80px] px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Video Player Section -->
            <div class="md:col-span-2 mb-4">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <video id="video-player" class="w-full h-auto" controls autoplay>
                        <source src="{{ asset('storage/videos/' . $video->video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- Video Details Section -->
                <div class="mt-4 bg-white rounded-lg p-4 shadow">
                    <h2 class="text-3xl font-bold">{{ $video->title }}</h2>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-gray-500">{{ $video->views }} views</span>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                            <i class="fas fa-thumbs-up"></i> Like
                        </button>
                    </div>
                    <p class="mt-2 text-gray-700">{{ $video->description }}</p>
                </div>
            </div>

            <!-- Related video Section -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <h3 class="text-xl font-semibold mb-4">Video Lainnya</h3>
                    @forelse($relatedVideos as $related)
                        <a href="{{ route('videos.show', $related->id) }}" class="flex items-center">
                            <img src="" alt="{{ $related->title }}" class="mr-2">
                            <div class="flex flex-col flex-start mb-4">
                                <h4 class="">{{ $related->title }}</h4>
                                <span class="text-gray-500">{{ $related->views }} views</span>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-2 py-4 px-4 text-center text-gray-500">Tidak ada video terkait.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoPlayer = document.getElementById('video-player');
            let hasViewed = false; // Track if views have been added

            videoPlayer.addEventListener('timeupdate', function() {
                // Check if the video has been played more than half of its duration
                if (!hasViewed && videoPlayer.currentTime >= videoPlayer.duration / 2) {
                    hasViewed = true; // Prevent multiple increments

                    // Send a request to the backend to increment views
                    fetch('{{ route('videos.incrementViews', ['id' => $video->id]) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token for security
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({}) // Body can be empty if not needed
                        })
                        .then(response => {
                            if (response.ok) {
                                console.log('View incremented');
                            } else {
                                console.error('Failed to increment view:', response.statusText);
                            }
                        })
                        .catch(error => console.error('Error incrementing view:', error));
                }
            });
        });
    </script>
@endsection
