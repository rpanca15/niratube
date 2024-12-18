@extends('layouts.sidebar')

@section('title')
    Niratube - Video Saya
@endsection

@section('content')
    <div class="container mx-auto px-4 h-screen flex flex-col">
        <!-- Tab Content -->
        <div id="my-videos" class="tab-content active flex flex-col flex-1">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-4">
                <span class="text-3xl font-bold">Video Saya</span>
                <div class="flex justify-between gap-4 items-center">
                    <!-- Search Form -->
                    <div class="relative">
                        <input type="text" title="Search" id="search-my-videos" placeholder="Cari video saya..."
                            class="px-4 py-2 border rounded-lg focus:outline-none pr-10">
                        <button id="clear-my-videos" title="Clear"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hidden">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <a href="{{ route('videos.create') }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                        <i class="fas fa-upload h-5 w-5 mr-2"></i>
                        Upload
                    </a>
                </div>
            </div>

            <!-- Scrollable Video List -->
            <div id="my-videos-list" class="overflow-auto flex-1 max-h-[calc(100vh-6rem)]">
                @include('videos.partials.my-videos', ['videos' => $myVideos])
            </div>
        </div>

        <div id="liked-videos" class="tab-content hidden flex flex-col flex-1">
            <div class="flex justify-between items-center mb-4">
                <span class="text-3xl font-bold">Video yang Disukai</span>

                <!-- Search Form -->
                <div class="relative">
                    <input type="text" title="Search" id="search-liked-videos" placeholder="Cari video yang disukai..."
                        class="px-4 py-2 border rounded-lg focus:outline-none pr-10">
                    <button id="clear-liked-videos" title="Clear"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hidden">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div id="liked-videos-list" class="overflow-auto flex-1 max-h-[calc(100vh-8rem)]">
                @include('videos.partials.liked-videos', ['videos' => $likedVideos])
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabLinks = document.querySelectorAll('.sidebar-link');
            const tabContents = document.querySelectorAll('.tab-content');

            const currentTab = new URLSearchParams(window.location.search).get('tab') || 'my-videos';

            // Aktifkan tab berdasarkan parameter
            tabLinks.forEach(link => {
                if (link.dataset.tab === currentTab) {
                    link.classList.add('bg-blue-100');
                } else {
                    link.classList.remove('bg-blue-100');
                }
            });

            tabContents.forEach(content => {
                if (content.id === currentTab) {
                    content.classList.remove('hidden');
                    content.classList.add('active');
                } else {
                    content.classList.add('hidden');
                }
            });

            // Switch tab
            tabLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();

                    const selectedTab = this.dataset.tab;
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', selectedTab);
                    history.pushState({}, '', url);

                    tabLinks.forEach(l => l.classList.remove('bg-blue-100'));
                    tabContents.forEach(content => content.classList.add('hidden'));

                    this.classList.add('bg-blue-100');
                    document.querySelector(`#${selectedTab}`).classList.remove('hidden');
                });
            });

            // Function to toggle clear button
            function toggleClearButton(input, button) {
                if (input.value) {
                    button.classList.remove('hidden');
                } else {
                    button.classList.add('hidden');
                }
            }

            // AJAX Search Function
            function ajaxSearch(inputId, clearId, listId, tabName) {
                const input = document.querySelector(`#${inputId}`);
                const clear = document.querySelector(`#${clearId}`);
                const list = document.querySelector(`#${listId}`);

                input.addEventListener('input', function () {
                    toggleClearButton(input, clear);
                    fetchVideos(input.value, tabName, list);
                });

                clear.addEventListener('click', function () {
                    input.value = '';
                    toggleClearButton(input, clear);
                    fetchVideos('', tabName, list);
                });
            }

            function fetchVideos(query, tab, list) {
                fetch(`{{ route('videos.index') }}?search=${query}&tab=${tab}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        list.innerHTML = html;
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Activate AJAX for "My Videos" and "Liked Videos"
            ajaxSearch('search-my-videos', 'clear-my-videos', 'my-videos-list', 'my-videos');
            ajaxSearch('search-liked-videos', 'clear-liked-videos', 'liked-videos-list', 'liked-videos');
        });
    </script>
@endsection
