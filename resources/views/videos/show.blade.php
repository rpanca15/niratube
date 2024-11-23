@extends('layouts.app')

@section('title')
    Niratube - {{ $video->title }}
@endsection

@section('style')
    <style>
        .rotate-270 {
            transform: rotate(270deg) translateX(-50%);
            transform-origin: left top;
        }

        .volume-slider {
            -webkit-appearance: none;
            height: 4px;
            background: #ffffff;
            border-radius: 2px;
        }

        .volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 12px;
            height: 12px;
            background: #ffffff;
            border-radius: 50%;
            cursor: pointer;
        }

        .preview-tooltip {
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .buffering::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto mt-[80px] px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Video Player Section -->
            <div class="md:col-span-2 mb-4">
                <!-- Video Container -->
                <div class="video-container relative bg-white rounded-lg shadow-lg overflow-hidden">
                    <video id="video-player" class="w-full h-auto" loading="lazy" preload="metadata" controlsList="nodownload">
                        <source src="{{ asset('storage/videos/' . $video->video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="video-controls absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 p-2">
                        <div class="progress-bar-container relative">
                            <div class="progress-bar h-1 bg-gray-600 cursor-pointer mb-2">
                                <div class="progress bg-blue-500 h-full" style="width: 0%"></div>
                            </div>
                            <!-- Preview tooltip -->
                            <div
                                class="preview-tooltip hidden absolute bottom-full left-0 transform -translate-x-1/2 mb-2 px-2 py-1 bg-black bg-opacity-75 text-white text-xs rounded">
                                0:00
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <button class="play-pause text-white px-2">
                                    <i class="fas fa-play"></i>
                                </button>

                                <!-- Volume Control -->
                                <div class="volume-container relative flex items-center">
                                    <button class="volume-btn text-white px-2">
                                        <i class="fas fa-volume-up"></i>
                                    </button>
                                    <div
                                        class="volume-slider-container hidden absolute bottom-10 left-0 bg-black bg-opacity-50 p-2 rounded rotate-270">
                                        <input type="range" class="volume-slider w-24" min="0" max="100"
                                            value="100">
                                    </div>
                                </div>
                            </div>

                            <div class="time text-white text-sm">
                                <span class="current-time">0:00</span>
                                /
                                <span class="duration">0:00</span>
                            </div>

                            <div class="flex items-center gap-4">
                                <!-- Quality Selector -->
                                <div class="quality-container relative">
                                    <button class="quality-btn text-white px-2">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <div
                                        class="quality-options hidden absolute bottom-10 right-0 bg-black bg-opacity-50 p-2 rounded">
                                        <button
                                            class="quality-option text-white block w-full text-left px-2 py-1 hover:bg-gray-700"
                                            data-quality="auto">Auto</button>
                                        <button
                                            class="quality-option text-white block w-full text-left px-2 py-1 hover:bg-gray-700"
                                            data-quality="1080p">1080p</button>
                                        <button
                                            class="quality-option text-white block w-full text-left px-2 py-1 hover:bg-gray-700"
                                            data-quality="720p">720p</button>
                                        <button
                                            class="quality-option text-white block w-full text-left px-2 py-1 hover:bg-gray-700"
                                            data-quality="480p">480p</button>
                                    </div>
                                </div>

                                <button class="fullscreen text-white px-2">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Details Section -->
                <div class="mt-4 bg-white rounded-lg p-4 shadow">
                    <h2 class="text-3xl font-bold">{{ $video->title }}</h2>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-gray-500">
                            <span id="views">{{ $video->views }}</span> views
                        </p>
                        <button id="like-button"
                            class="{{ $video->likes()->where('user_id', Auth::id())->where('status', 'active')->exists() ? 'text-blue-500' : 'text-gray-500' }}"
                            data-liked="{{ $video->likes()->where('user_id', Auth::id())->where('status', 'active')->exists() ? 'true' : 'false' }}">
                            <i class="fas fa-thumbs-up"></i>
                            <span id="like-count">{{ $video->likes_count }}</span></button>
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
            const playPauseBtn = document.querySelector('.play-pause');
            const progressBar = document.querySelector('.progress-bar');
            const progress = document.querySelector('.progress');
            const currentTimeElement = document.querySelector('.current-time');
            const durationElement = document.querySelector('.duration');
            const fullscreenBtn = document.querySelector('.fullscreen');
            const volumeBtn = document.querySelector('.volume-btn');
            const volumeSlider = document.querySelector('.volume-slider');
            const volumeContainer = document.querySelector('.volume-container');
            const qualityBtn = document.querySelector('.quality-btn');
            const qualityOptions = document.querySelector('.quality-options');

            let hasViewed = false;
            let lastClickTime = 0;
            let isDragging = false;

            // Format time from seconds to MM:SS
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                seconds = Math.floor(seconds % 60);
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            // Keyboard controls
            document.addEventListener('keydown', function(e) {
                if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !==
                    'TEXTAREA') {
                    switch (e.key) {
                        case 'ArrowRight':
                            e.preventDefault();
                            newTime = videoPlayer.currentTime + 10;
                            if (newTime <= videoPlayer.duration) {
                                videoPlayer.currentTime = newTime;
                            }
                            break;
                        case 'ArrowLeft':
                            e.preventDefault();
                            newTime = videoPlayer.currentTime - 10;
                            if (newTime >= 0) {
                                videoPlayer.currentTime = Math.max(0, newTime);
                            }
                            break;
                        case ' ':
                            e.preventDefault();
                            togglePlayPause();
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            videoPlayer.volume = Math.min(1, videoPlayer.volume + 0.1);
                            updateVolumeUI();
                            break;
                        case 'ArrowDown':
                            e.preventDefault();
                            videoPlayer.volume = Math.max(0, videoPlayer.volume - 0.1);
                            updateVolumeUI();
                            break;
                        case 'm':
                            toggleMute();
                            break;
                    }
                }
            });

            // Progress bar controls
            progressBar.addEventListener('click', function(e) {
                updateVideoProgress(e);
            });

            progressBar.addEventListener('mousedown', function(e) {
                isDragging = true;
                updateVideoProgress(e);
            });

            document.addEventListener('mousemove', function(e) {
                if (isDragging) {
                    updateVideoProgress(e);
                }
            });

            document.addEventListener('mouseup', function() {
                isDragging = false;
            });

            function updateVideoProgress(e) {
                e.preventDefault(); // Prevent any default behavior

                const rect = progressBar.getBoundingClientRect();
                const clickPosition = Math.max(0, Math.min(e.clientX - rect.left, rect.width));
                const percentage = (clickPosition / rect.width);
                const seekTime = percentage * videoPlayer.duration;

                // Update progress bar visual
                progress.style.width = `${percentage * 100}%`;

                // Directly set the currentTime
                if (!isNaN(seekTime) && isFinite(seekTime)) {
                    videoPlayer.currentTime = seekTime;
                    currentTimeElement.textContent = formatTime(seekTime);
                }
            }

            progressBar.addEventListener('selectstart', function(e) {
                e.preventDefault();
            });

            // Volume controls
            volumeBtn.addEventListener('click', toggleMute);

            volumeContainer.addEventListener('mouseenter', function() {
                document.querySelector('.volume-slider-container').classList.remove('hidden');
            });

            volumeContainer.addEventListener('mouseleave', function() {
                document.querySelector('.volume-slider-container').classList.add('hidden');
            });

            volumeSlider.addEventListener('input', function() {
                const volume = this.value / 100;
                videoPlayer.volume = volume;
                updateVolumeUI();
            });

            function toggleMute() {
                videoPlayer.muted = !videoPlayer.muted;
                updateVolumeUI();
            }

            function updateVolumeUI() {
                const volumeIcon = volumeBtn.querySelector('i');
                const volume = videoPlayer.volume;
                volumeSlider.value = volume * 100;

                volumeIcon.className = 'fas';
                if (videoPlayer.muted || volume === 0) {
                    volumeIcon.classList.add('fa-volume-mute');
                } else if (volume < 0.5) {
                    volumeIcon.classList.add('fa-volume-down');
                } else {
                    volumeIcon.classList.add('fa-volume-up');
                }
            }

            // Quality selector
            qualityBtn.addEventListener('click', function() {
                qualityOptions.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!qualityOptions.contains(e.target) && !qualityBtn.contains(e.target)) {
                    qualityOptions.classList.add('hidden');
                }
            });

            // Quality change handler
            document.querySelectorAll('.quality-option').forEach(option => {
                option.addEventListener('click', function() {
                    const quality = this.dataset.quality;
                    const currentTime = videoPlayer.currentTime;
                    const isPlaying = !videoPlayer.paused;

                    // Here you would typically change the video source based on quality
                    // This is a simplified example - you'll need to modify it based on your video sources
                    const videoPath = '{{ asset('storage/videos/' . $video->video) }}';
                    const qualityPath = quality === 'auto' ? videoPath : videoPath.replace('.mp4',
                        `-${quality}.mp4`);

                    videoPlayer.querySelector('source').src = qualityPath;
                    videoPlayer.load();

                    videoPlayer.addEventListener('loadedmetadata', function onLoaded() {
                        videoPlayer.currentTime = currentTime;
                        if (isPlaying) videoPlayer.play();
                        videoPlayer.removeEventListener('loadedmetadata', onLoaded);
                    });

                    qualityOptions.classList.add('hidden');
                    qualityBtn.querySelector('i').className = 'fas fa-cog';
                });
            });

            // Play/Pause toggle function
            function togglePlayPause() {
                if (videoPlayer.paused) {
                    videoPlayer.play();
                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    videoPlayer.pause();
                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            }

            // Click handlers
            playPauseBtn.addEventListener('click', togglePlayPause);

            videoPlayer.addEventListener('click', function(e) {
                const currentTime = new Date().getTime();
                const clickTimeDiff = currentTime - lastClickTime;

                if (clickTimeDiff < 300) { // Double click for fullscreen
                    if (videoPlayer.requestFullscreen) {
                        videoPlayer.requestFullscreen();
                    } else if (videoPlayer.webkitRequestFullscreen) {
                        videoPlayer.webkitRequestFullscreen();
                    } else if (videoPlayer.msRequestFullscreen) {
                        videoPlayer.msRequestFullscreen();
                    }
                } else {
                    togglePlayPause();
                }

                lastClickTime = currentTime;
            });

            // Progress bar update
            videoPlayer.addEventListener('timeupdate', function() {
                if (!isDragging) {
                    const percentage = (videoPlayer.currentTime / videoPlayer.duration) * 100;
                    progress.style.width = `${percentage}%`;
                    currentTimeElement.textContent = formatTime(videoPlayer.currentTime);
                }

                // View counter
                if (!hasViewed && videoPlayer.currentTime >= videoPlayer.duration / 2) {
                    hasViewed = true;
                    incrementView();
                }
            });

            // Set duration when metadata is loaded
            videoPlayer.addEventListener('loadedmetadata', function() {
                durationElement.textContent = formatTime(videoPlayer.duration);
                updateVolumeUI();
            });

            // Fullscreen button
            fullscreenBtn.addEventListener('click', function() {
                if (videoPlayer.requestFullscreen) {
                    videoPlayer.requestFullscreen();
                } else if (videoPlayer.webkitRequestFullscreen) {
                    videoPlayer.webkitRequestFullscreen();
                } else if (videoPlayer.msRequestFullscreen) {
                    videoPlayer.msRequestFullscreen();
                }
            });

            // View increment function
            function incrementView() {
                fetch('{{ route('videos.incrementViews', ['id' => $video->id]) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const viewsCount = document.getElementById('views');
                            if (viewsCount) {
                                viewsCount.textContent = data.views;
                            }
                            console.log('View incremented');
                        } else {
                            console.error('Failed to increment view:', data.message);
                        }
                    })
                    .catch(error => console.error('Error incrementing view:', error));
            }

            // Add loading indicator
            videoPlayer.addEventListener('waiting', function() {
                videoPlayer.classList.add('loading');
            });

            videoPlayer.addEventListener('playing', function() {
                videoPlayer.classList.remove('loading');
            });

            progressBar.addEventListener('mousemove', function(e) {
                if (!previewThumbVisible && !isProgressBarDragging) {
                    const rect = progressBar.getBoundingClientRect();
                    const percentage = (e.clientX - rect.left) / rect.width;
                    const previewTime = percentage * videoPlayer.duration;

                    // Update preview time tooltip
                    // You can add a tooltip element to show the time
                    showPreviewTooltip(e.clientX, formatTime(previewTime));
                }
            });

            progressBar.addEventListener('mouseleave', function() {
                hidePreviewTooltip();
            });

            // Initialize volume
            videoPlayer.volume = 1;
            updateVolumeUI();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const likeButton = document.getElementById(
                'like-button'); // Assuming you have a like button with this ID
            const videoId = {{ $video->id }}; // Ensure this is the correct video ID

            likeButton.addEventListener('click', function() {
                fetch('{{ route('videos.like', ['id' => $video->id]) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const likeCount = document.getElementById('like-count');
                            likeCount.textContent = data.likes_count;
                            if (likeButton.getAttribute('data-liked') === 'true') {
                                likeButton.classList.remove('text-blue-500');
                                likeButton.classList.add('text-gray-500');
                                likeButton.setAttribute('data-liked', 'false');
                            } else {
                                likeButton.classList.remove('text-gray-500');
                                likeButton.classList.add('text-blue-500');
                                likeButton.setAttribute('data-liked', 'true');
                            }
                            console.log('Like berhasil');
                        } else {
                            console.error(data.message);
                            window.location.href = '/login';
                        }
                    })
                    .catch(error => console.error('Error liking video:', error));
            });
        });
    </script>
@endsection
