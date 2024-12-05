<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Videos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mengambil parameter pencarian dari query string
        $search = $request->query('search');

        // Query untuk mengambil semua video
        $videosQuery = Videos::withCount(['likes' => function ($query) {
            $query->where('status', 'active'); // Menghitung hanya likes yang aktif
        }]);

        // Jika parameter pencarian tersedia, tambahkan filter judul
        if ($search) {
            $videosQuery->where('title', 'LIKE', '%' . $search . '%');
        }

        $videos = $videosQuery->get();

        // Mendapatkan semua kategori
        $categories = Category::all();

        // Mengembalikan respons JSON dengan BaseController
        return $this->sendResponse([
            'videos' => $videos,
            'categories' => $categories,
        ], 'Videos and categories retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi form
        $request->validate([
            'video'         => 'required|mimes:mp4,avi,mov,mkv|max:20480', // 20MB untuk video
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'category_id'   => 'required|integer|exists:categories,id', // Validasi id kategori
            'privacy'       => 'required|in:private,public', // Status bisa private atau public
        ]);

        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return $this->sendError('Unauthorized', ['message' => 'You must be logged in to upload a video.'], 401);
        }

        // Upload video
        $video = $request->file('video');
        $videoPath = $video->storeAs('public/videos', $video->hashName());

        // Create video record
        $newVideo = Videos::create([
            'video'         => $video->hashName(),
            'title'         => $request->title,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'privacy'       => $request->privacy,
            'uploader_id'   => Auth::id(), // Gunakan ID user yang sedang login
            'views'         => 0, // Awal views adalah 0
        ]);

        // Mengembalikan respons JSON dengan BaseController
        return $this->sendResponse([
            // 'message' => 'Video berhasil diunggah.',
            'video'   => $newVideo,
            'path'    => $videoPath,
        ], 'Video uploaded successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Mendapatkan video berdasarkan ID, termasuk relasi likes
        $video = Videos::with('likes')->findOrFail($id);

        // Mendapatkan video terkait berdasarkan kategori yang sama (kecuali video yang sedang dilihat)
        $relatedVideos = Videos::where('category_id', $video->category_id)
            ->where('id', '!=', $id)
            ->limit(20)
            ->get();

        // Mengembalikan respons JSON dengan BaseController
        return $this->sendResponse([
            'video' => $video,
            'relatedVideos' => $relatedVideos,
        ], 'Video and related videos retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi data menggunakan Validator
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'category_id' => 'required|integer|exists:categories,id',
            'privacy' => 'required|in:private,public',
        ]);

        // Jika validasi gagal, kembalikan pesan error menggunakan sendError
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        // Temukan video berdasarkan ID
        $video = Videos::findOrFail($id);

        // Periksa otorisasi
        if ($video->uploader_id !== $request->user()->id) {
            return $this->sendError('Unauthorized', ['message' => 'You are not authorized to update this video.'], 403);
        }

        // Update data video
        $video->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'privacy' => $request->privacy,
        ]);

        // Mengembalikan respons JSON dengan BaseController
        return $this->sendResponse($video, 'Video updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Temukan video berdasarkan ID
        $video = Videos::find($id);

        if (!$video) {
            return $this->sendError('Video not found', ['message' => 'Video not found.'], 404);
        }

        // Periksa otorisasi
        if ($video->uploader_id !== request()->user()->id) {
            return $this->sendError('Unauthorized', ['message' => 'You are not authorized to delete this video.'], 403);
        }

        // Hapus file video dari penyimpanan jika ada
        if ($video->video && Storage::exists('public/videos/' . $video->video)) {
            Storage::delete('public/videos/' . $video->video);
        }

        // Hapus data video dari database
        $video->delete();

        // Mengembalikan respons JSON dengan BaseController
        return $this->sendResponse([], 'Video deleted successfully.');
    }
}
