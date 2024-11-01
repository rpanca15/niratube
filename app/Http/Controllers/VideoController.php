<?php

namespace App\Http\Controllers;

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

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::user()->id;

        if ($search) {
            $videos = Videos::where('title', 'LIKE', '%' . $search . '%')
                ->where('uploader_id', $userId) // Filter berdasarkan ID pengguna
                ->paginate(10);
        } else {
            $videos = Videos::where('uploader_id', $userId)->paginate(10);
        }

        return view('videos.index', compact('videos'));
    }

    public function create(): View
    {
        return view('videos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi form
        $request->validate([
            'video'         => 'required|mimes:mp4,avi,mov,mkv|max:20480', // 20MB untuk video
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'category'      => 'required|string',
            'privacy'       => 'required|in:private,public', // Status bisa private atau public
        ]);

        // Upload video
        $video = $request->file('video');
        $video->storeAs('public/videos', $video->hashName());

        // Create video record
        Videos::create([
            'video'         => $video->hashName(),
            'title'         => $request->title,
            'description'   => $request->description,
            'category'      => $request->category,
            'status'        => $request->privacy,
            'uploader_id'   => Auth::user()->id,
            'views'         => 0, // Awal views adalah 0
            'likes'         => 0, // Awal likes adalah 0
        ]);

        // Redirect ke index
        return redirect()->route('videos.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id): View
    {
        $video = Videos::findOrFail($id);

        $relatedVideos = Videos::where('category', $video->category)
            ->where('id', '!=', $id)
            ->limit(20)
            ->get();

        return view('videos.show', compact('video', 'relatedVideos'));
    }

    public function edit(string $id): View
    {
        $video = Videos::findOrFail($id);
        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        // Validasi form
        $request->validate([
            'video' => 'nullable|mimes:mp4,avi,mov|max:204800', // 200MB
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'category' => 'required|string',
            'privacy' => 'required|in:private,public',
        ]);

        $video = Videos::findOrFail($id);

        $video->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'status' => $request->privacy,
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
            return response()->json(['success' => false, 'message' => 'Pemilik video tidak dapat menambah jumlah tayangan.'], 403);
        }
        $video->increment('views');

        return response()->json(['success' => true]);
    }
}
