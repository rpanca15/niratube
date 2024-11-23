<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Videos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categories = Category::all();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::user()->id;

        if ($search) {
            $videos = Videos::withCount(['likes' => function ($query) {
                $query->where('status', 'active'); // Menghitung hanya yang aktif
            }])
                ->where('title', 'LIKE', '%' . $search . '%')
                ->where('uploader_id', $userId)
                ->get();
        } else {
            $videos = Videos::withCount(['likes' => function ($query) {
                $query->where('status', 'active'); // Menghitung hanya yang aktif
            }])
                ->where('uploader_id', $userId)
                ->get();
        }

        return view('videos.index', compact('videos', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('videos.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi form
        $request->validate([
            'video'         => 'required|mimes:mp4,avi,mov,mkv|max:200480', // 20MB untuk video
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'category_id'   => 'required|string',
            'privacy'       => 'required|in:private,public', // Status bisa private atau public
        ]);

        // Upload video
        $video = $request->file('video');
        $video->storeAs('public/videos', $video->hashName());

        // Create video record
        $newVideo = Videos::create([
            'video'         => $video->hashName(),
            'title'         => $request->title,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'privacy'       => $request->privacy,
            'uploader_id'   => Auth::user()->id,
            'views'         => 0, // Awal views adalah 0
        ]);

        // Redirect ke index
        return redirect()->route('videos.index')->with(['success' => 'Data Berhasil Disimpan dan Video Disukai!']);
    }

    public function show(string $id): View
    {
        $video = Videos::with('likes')->findOrFail($id);

        $relatedVideos = Videos::where('category_id', $video->category_id)
            ->where('id', '!=', $id)
            ->limit(20)
            ->get();

        return view('videos.show', compact('video', 'relatedVideos'));
    }

    public function edit(string $id): View
    {
        $video = Videos::findOrFail($id);
        $categories = Category::all();

        return view('videos.edit', compact('video', 'categories'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        // Validasi form
        $request->validate([
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'category_id' => 'required|string',
            'privacy' => 'required|in:private,public',
        ]);

        $video = Videos::findOrFail($id);

        if ($video->uploader_id !== Auth::id()) {
            return redirect()->route('videos.index')->withErrors(['error' => 'You are not authorized to edit this video']);
        }

        // Lanjutkan dengan update jika validasi berhasil
        $video->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'privacy' => $request->privacy,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video berhasil diperbarui!');
    }

    public function destroy($id): RedirectResponse
    {
        $video = Videos::find($id);

        if (!$video) {
            return redirect()->route('videos.index')->withErrors(['error' => 'Video not found.']);
        }

        if (Auth::id() !== $video->uploader_id) {
            return redirect()->route('videos.index')->withErrors(['error' => 'Tidak bisa menghapus video milik orang lain']);
        }

        if ($video->video && Storage::exists('public/videos/' . $video->video)) {
            Storage::delete('public/videos/' . $video->video);
        }

        $video->delete();

        return redirect()->route('videos.index')->with(['success' => 'Video Berhasil Dihapus!']);
    }

    public function incrementViews($id)
    {
        $video = Videos::findOrFail($id);

        if ($video->uploader_id == Auth::id()) {
            return response()->json([
                'success' => false, 
                'message' => 'Pemilik video tidak dapat menambah jumlah tayangan.'
            ], 403);
        }
        $video->increment('views');
        $views = $video->views;

        return response()->json([
            'success' => true,
            'views' => $views
        ]);
    }

    public function likeVideo($id)
    {
        $video = Videos::findOrFail($id);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login untuk memberi like'
            ], 401); // Unauthorized
        }

        $user = Auth::user();

        if ($video->uploader_id == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pemilik video tidak dapat menambah like pada videonya sendiri'
            ], 403); // Forbidden
        }

        $video->like($user);

        $likesCount = $video->likes()->where('status', 'active')->count();
        $isLiked = $video->likes()->where('user_id', $user->id)->where('status', 'active')->exists();

        return response()->json([
            'success' => true,
            'likes_count' => $likesCount,
            'is_liked' => $isLiked
        ]);
    }
}
