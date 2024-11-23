<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Video</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-4">
        <h1 class="text-3xl font-bold mb-6 text-center">Edit Video</h1>
        <form class="flex flex-wrap gap-4" action="{{ route('videos.update', $video->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Ubah method menjadi PUT -->

            <!-- Video Preview Section -->
            <div id="div-preview" class="flex-1 bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-center mb-4" id="preview-container">
                    <video id="video-preview" class="w-full h-auto rounded border" controls>
                        <source src="{{ asset('storage/videos/' . $video->video) }}" type="video/mp4">
                        Browser Anda tidak mendukung video HTML5.
                    </video>
                </div>
                <div class="text-center text-gray-600 mt-2">
                    <p><strong>Nama File:</strong> {{ basename($video->video) }}</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="flex-1 bg-white shadow-lg rounded-lg p-6">
                <!-- Title Input -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                    <input type="text" id="title" name="title" value="{{ $video->title }}"
                        class="w-full px-3 py-2 border rounded-lg" required>
                </div>

                <!-- Description Input -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border rounded-lg" required>{{ $video->description }}</textarea>
                </div>

                <!-- Category Dropdown -->
                <div class="mb-4">
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                    <select id="category" name="category_id" class="w-full px-3 py-2 border rounded-lg" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $video->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Privacy Radio Button -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Privasi</label>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="private" name="privacy" value="private"
                            {{ $video->status == 'private' ? 'checked' : '' }}>
                        <label for="private" class="ml-2">Private</label>
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="public" name="privacy" value="public"
                            {{ $video->status == 'public' ? 'checked' : '' }}>
                        <label for="public" class="ml-2">Public</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between mt-4">
                    <button type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('videos.index') }}"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
