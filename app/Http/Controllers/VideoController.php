<?php

namespace App\Http\Controllers;

use App\Models\Videos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Jika ada pencarian, filter video berdasarkan judul
        if ($search) {
            $videos = Videos::where('title', 'LIKE', '%' . $search . '%')->paginate(10);
        } else {
            $videos = Videos::paginate(10);
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
            'video'         => 'required|mimes:mp4,avi,mov|max:20480', // 20MB untuk video
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
            'uploader_id'   => 0,
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
            ->limit(5)
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
            'video' => 'nullable|mimes:mp4,avi,mov|max:20480', // 20MB
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'category' => 'required|string',
            'privacy' => 'required|in:private,public',
        ]);

        $video = Videos::findOrFail($id);

        if ($request->hasFile('video')) {
            Storage::delete('public/videos/' . $video->video);
            $newVideo = $request->file('video');
            $newVideo->storeAs('public/videos', $newVideo->hashName());
            $video->video = $newVideo->hashName();
        }

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
        $video = Videos::findOrFail($id);

        Storage::delete('public/videos/' . $video->video);

        $video->delete();

        return redirect()->route('videos.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
