<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Video</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropArea = document.getElementById('upload-label');
            const fileInput = document.getElementById('file-input');
            const videoPreview = document.getElementById('video-preview');
            const previewContainer = document.getElementById('preview-container');
            const fileName = document.getElementById('file-name');
            const divUpload = document.getElementById('div-upload');
            const dropInstruction = document.getElementById('drop-instruction');
            const iconUpload = document.getElementById('icon-upload');
            const resetButton = document.getElementById('reset-button');

            // Prevent default behaviors for drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Highlight drop area when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.add('bg-gray-100', 'border-blue-500', 'border-2'); // Add highlight
                    dropInstruction.textContent = 'Lepaskan file';
                    dropInstruction.classList.add('text-blue-500');
                    iconUpload.classList.add('bg-gray-100', 'text-blue-500', 'border-blue-500', 'border-2');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.remove('bg-gray-100', 'border-blue-500', 'border-2');
                    dropInstruction.textContent = 'Seret dan lepas video di sini atau klik untuk memilih file';
                    dropInstruction.classList.remove('text-blue-500');
                    iconUpload.classList.remove('bg-gray-100', 'text-blue-500', 'border-blue-500', 'border-2');
                }, false);
            });

            // Handle dropped files
            dropArea.addEventListener('drop', (e) => {
                let dt = e.dataTransfer;
                let files = dt.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    previewVideo(files[0]);
                }
            });

            // Handle file input change
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    previewVideo(file);
                }
            });

            // Function to preview video
            function previewVideo(file) {
                if (file && file.type.startsWith('video/')) {
                    const videoURL = URL.createObjectURL(file);
                    videoPreview.src = videoURL;
                    previewContainer.classList.remove('hidden'); // Show video preview
                    dropArea.classList.add('hidden'); // Hide upload label
                    fileName.textContent = file.name;
                    fileName.classList.remove('hidden'); // Show file name
                    resetButton.classList.remove('hidden'); // Show reset button
                    divUpload.classList.add('flex', 'flex-col', 'justify-center', 'items-center');
                } else {
                    alert('Tolong pilih file video yang valid.');
                }
            }

            // Handle reset button click
            resetButton.addEventListener('click', () => {
                // Reset input and preview
                fileInput.value = ''; // Clear file input
                videoPreview.src = ''; // Clear video source
                previewContainer.classList.add('hidden'); // Hide video preview
                dropArea.classList.remove('hidden'); // Show upload label
                fileName.textContent = ''; // Clear file name
                fileName.classList.add('hidden'); // Hide file name
                dropInstruction.textContent = 'Seret dan lepas video di sini atau klik untuk memilih file';
                resetButton.classList.add('hidden'); // Hide reset button
                divUpload.classList.remove('flex', 'flex-col', 'justify-center', 'items-center');
            });
        });
    </script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-4">
        <h1 class="text-3xl font-bold mb-6 text-center">Edit Video</h1>
        <form class="flex flex-wrap gap-4" action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Ubah method menjadi PUT -->

            <!-- Video Upload Section -->
            <div id="div-upload" class="flex-1 bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-center mb-4 hidden" id="preview-container">
                    <video id="video-preview" class="w-full h-auto rounded border" controls></video>
                </div>
                <label for="file-input" class="flex flex-col justify-center items-center bg-gray-50 border border-dashed border-gray-400 rounded-lg h-full cursor-pointer hover:bg-gray-100 transition" id="upload-label">
                    <i id="icon-upload" class="fas fa-plus text-4xl w-24 h-24 flex items-center justify-center text-gray-400 border border-dashed border-gray-400 rounded"></i>
                    <span id="drop-instruction" class="text-gray-500 text-lg">Seret dan lepas video di sini atau klik untuk memilih file</span>
                </label>
                <input type="file" id="file-input" name="video" accept="video/*" class="hidden @error('video') is-invalid @enderror">
                <div id="file-name" class="mt-2 text-gray-600 text-center hidden"></div>

                <button id="reset-button" class="mt-4 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition hidden">
                    <i class="fas fa-times text-xl text-white"></i>
                </button>
                @error('video')
                <div class="text-red-500 mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Section -->
            <div class="flex-1 bg-white shadow-lg rounded-lg p-6">
                <!-- Title Input -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                    <input type="text" id="title" name="title" value="{{ $video->title }}" class="w-full px-3 py-2 border rounded-lg" required>
                </div>

                <!-- Description Input -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border rounded-lg" required>{{ $video->description }}</textarea>
                </div>

                <!-- Category Dropdown -->
                <div class="mb-4">
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                    <select id="category" name="category" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="Education" {{ $video->category == 'Education' ? 'selected' : '' }}>Edukasi</option>
                        <option value="Entertainment" {{ $video->category == 'Entertainment' ? 'selected' : '' }}>Hiburan</option>
                        <option value="Technology" {{ $video->category == 'Technology' ? 'selected' : '' }}>Teknologi</option>
                        <option value="Other" {{ $video->category == 'Other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Privacy Radio Button -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Privasi</label>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="private" name="privacy" value="private" {{ $video->status == 'private' ? 'checked' : '' }}>
                        <label for="private" class="ml-2">Private</label>
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="public" name="privacy" value="public" {{ $video->status == 'public' ? 'checked' : '' }}>
                        <label for="public" class="ml-2">Public</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between mt-4">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('videos.index') }}" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
