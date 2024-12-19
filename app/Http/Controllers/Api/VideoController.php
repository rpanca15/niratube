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

        // Query untuk mengambil semua video dengan kondisi privacy public
        $videosQuery = Videos::withCount(['likes' => function ($query) {
            $query->where('status', 'active'); // Menghitung hanya likes yang aktif
        }])
            ->where('privacy', 'public'); // Menambahkan filter privacy public

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

    // Mendapatkan semua kategori
    public function category()
    {
        try {
            // Ambil semua kategori
            $categories = Category::all();

            // Kembalikan response JSON dengan status sukses
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi form menggunakan Validator
        $validator = Validator::make($request->all(), [
            'video'       => 'required|string', // Changed to accept base64 string
            'title'       => 'required|min:5',
            'description' => 'required|min:10',
            'category_id' => 'required|integer|exists:categories,id', // Validasi id kategori
            'privacy'     => 'required|in:private,public', // Status bisa private atau public
        ]);

        // Jika validasi gagal, kembalikan respon dengan error
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        // Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            return $this->sendError('Unauthorized', ['message' => 'You must be logged in to upload a video.'], 401);
        }

        try {
            // Decode base64 string
            $videoData = explode(',', $request->video);
            $videoContent = base64_decode($videoData[1] ?? $request->video);

            if (!$videoContent) {
                return $this->sendError('Invalid video data', ['message' => 'The provided video data is invalid.'], 422);
            }

            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.mp4';

            // Save video file
            Storage::put('public/videos/' . $filename, $videoContent);

            // Membuat record video baru di database
            $newVideo = Videos::create([
                'video'         => $filename,
                'title'         => $request->title,
                'description'   => $request->description,
                'category_id'   => $request->category_id,
                'privacy'       => $request->privacy,
                'uploader_id'   => Auth::id(), // Menggunakan ID user yang sedang login
                'views'         => 0, // Awal views adalah 0
            ]);

            return $this->sendResponse([
                'video' => $newVideo,
                'path'  => Storage::url('public/videos/' . $filename),
            ], 'Video uploaded successfully.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Upload Error', ['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $video = Videos::with('likes')->findOrFail($id);

        // Menambahkan data status apakah video disukai oleh pengguna
        $isLiked = false;
        if (Auth::check()) {
            $isLiked = $video->likes()->where('user_id', Auth::id())->where('status', 'active')->exists();
        }

        $relatedVideos = Videos::where('category_id', $video->category_id)
            ->where('id', '!=', $id)
            ->limit(20)
            ->get();

        return $this->sendResponse([
            'video' => $video,
            'video_url' => asset('storage/videos/' . $video->video),
            'relatedVideos' => $relatedVideos,
            'is_liked' => $isLiked, // Tambahkan status disukai
            'likes_count' => $video->likes()->where('status', 'active')->count(), // Jumlah likes aktif
        ], 'Video and related videos retrieved successfully.');
    }


    public function edit(string $id)
    {
        $video = Videos::find($id);

        if (!$video) {
            return $this->sendError('Video not found', ['message' => 'Video not found.'], 404);
        }

        $categories = Category::all();

        return $this->sendResponse([
            'video' => $video,
            'categories' => $categories,
        ], 'Video data retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'category_id' => 'required|integer|exists:categories,id',
            'privacy' => 'required|in:private,public',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

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

    /**
     * Increment views for a video.
     */
    public function incrementViews($id)
    {
        $video = Videos::findOrFail($id);

        // Periksa apakah pengguna sedang melihat videonya sendiri
        if (Auth::check() && $video->uploader_id === Auth::id()) {
            return $this->sendError('Cannot increment views on your own video.', [], 403);
        }

        // Tambah jumlah views
        $video->increment('views');

        return $this->sendResponse([
            'video_id' => $video->id,
            'views' => $video->views,
        ], 'Video views incremented successfully.');
    }

    /**
     * Add a like to a video.
     */
    public function likeVideo(Request $request, $id)
    {
        $video = Videos::findOrFail($id);

        if (!Auth::check()) {
            return $this->sendError('You must be logged in to like a video.', [], 401);
        }

        $user = Auth::user();

        // Pemilik video tidak boleh like videonya sendiri
        if ($video->uploader_id === $user->id) {
            return $this->sendError('Cannot like your own video.', [], 403);
        }

        // Cek apakah user sudah pernah like video ini sebelumnya
        $like = $video->likes()->where('user_id', $user->id)->first();

        if ($like) {
            // Toggle status like
            $like->status = $like->status === 'active' ? 'inactive' : 'active';
            $like->save();
        } else {
            // Tambahkan like baru
            $video->likes()->create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }

        $likesCount = $video->likes()->where('status', 'active')->count();
        $isLiked = $video->likes()->where('user_id', $user->id)->where('status', 'active')->exists();

        return $this->sendResponse([
            'video_id' => $video->id,
            'likes_count' => $likesCount,
            'is_liked' => $isLiked,
        ], 'Video like status updated successfully.');
    }

    /**
     * Display the list of videos uploaded by the authenticated user.
     */
    public function myVideos(Request $request)
    {
        // Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            return $this->sendError('Unauthorized', ['message' => 'You must be logged in to view your videos.'], 401);
        }

        $search = $request->query('search');
        $userId = Auth::user()->id;

        // Query untuk video yang diunggah oleh pengguna
        $myVideosQuery = Videos::withCount(['likes' => function ($query) {
            $query->where('status', 'active');
        }])->where('uploader_id', $userId);

        // Jika ada pencarian, tambahkan filter berdasarkan judul
        if ($search) {
            $myVideosQuery->where('title', 'LIKE', '%' . $search . '%');
        }

        $myVideos = $myVideosQuery->get();

        // Mengembalikan respons JSON dengan video yang diunggah oleh pengguna
        return $this->sendResponse([
            'myVideos' => $myVideos
        ], 'User\'s videos retrieved successfully.');
    }

    /**
     * Display the list of videos liked by the authenticated user.
     */
    public function likedVideos(Request $request)
    {
        // Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            return $this->sendError('Unauthorized', ['message' => 'You must be logged in to view liked videos.'], 401);
        }

        $search = $request->query('search');
        $userId = Auth::user()->id;

        // Query untuk video yang disukai oleh pengguna
        $likedVideosQuery = Videos::withCount(['likes' => function ($query) {
            $query->where('status', 'active');
        }])->whereHas('likes', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('status', 'active');
        });

        // Jika ada pencarian, tambahkan filter berdasarkan judul
        if ($search) {
            $likedVideosQuery->where('title', 'LIKE', '%' . $search . '%');
        }

        $likedVideos = $likedVideosQuery->get();

        // Mengembalikan respons JSON dengan video yang disukai oleh pengguna
        return $this->sendResponse([
            'likedVideos' => $likedVideos
        ], 'Liked videos retrieved successfully.');
    }
}
