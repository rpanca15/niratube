@extends('layouts.sidebar')

@section('title')
    Niratube - Video Saya
@endsection

@section('content')
    <div class="container mx-auto px-4">
        <!-- Tab Content -->
        <div id="my-videos" class="tab-content active">
            <div class="flex justify-between items-center mb-4">
                <span class="text-3xl font-bold">Video Saya</span>
                <div class="flex justify-between gap-4 items-center">
                    <!-- Search Form -->
                    <form action="{{ route('videos.index') }}" method="GET">
                        <input type="hidden" name="tab" value="my-videos">
                        <input type="text" name="search" value="{{ request('tab') === 'my-videos' ? $search : '' }}"
                            placeholder="Cari video saya..." class="px-4 py-2 border rounded-lg focus:outline-none">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg ml-2">Cari</button>
                    </form>
                    <a href="{{ route('videos.create') }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-upload h-5 w-5 mr-2"></i>
                        Upload
                    </a>
                </div>
            </div>

            @include('videos.partials.my-videos', ['videos' => $myVideos])
        </div>

        <div id="liked-videos" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <span class="text-3xl font-bold mb-4">Video yang Disukai</span>

                <!-- Search Form -->
                <form action="{{ route('videos.index') }}" method="GET" class="mb-4">
                    <input type="hidden" name="tab" value="liked-videos">
                    <input type="text" name="search" value="{{ request('tab') === 'liked-videos' ? $search : '' }}"
                        placeholder="Cari video yang disukai..." class="px-4 py-2 border rounded-lg focus:outline-none">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg ml-2">Cari</button>
                </form>
            </div>

            @include('videos.partials.liked-videos', ['videos' => $likedVideos])
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.sidebar-link');
            const tabContents = document.querySelectorAll('.tab-content');

            const currentTab = new URLSearchParams(window.location.search).get('tab') || 'my-videos';

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

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
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
        });
    </script>
@endsection
