<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $video->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Video Player Section -->
            <div class="md:col-span-2 mb-4">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <video id="video-player" class="w-full h-auto" controls>
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
    </div>
</body>

</html>
