<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Videos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlaylistController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $userId = Auth::id();

        $playlistsQuery = Playlist::withCount('videos')->where('user_id', $userId);

        if ($search) {
            $playlistsQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        $playlists = $playlistsQuery->get();

        return view('playlists.index', compact('playlists', 'search'));
    }

    public function create(): View
    {
        return view('playlists.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Playlist::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('playlists.index')->with('success', 'Playlist berhasil dibuat!');
    }

    public function show($id): View
    {
        $playlist = Playlist::with('videos')->findOrFail($id);

        if ($playlist->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke playlist ini.');
        }

        return view('playlists.show', compact('playlist'));
    }

    public function edit($id): View
    {
        $playlist = Playlist::findOrFail($id);

        if ($playlist->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit playlist ini.');
        }

        return view('playlists.edit', compact('playlist'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $playlist = Playlist::findOrFail($id);

        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')->withErrors(['error' => 'Anda tidak berhak mengedit playlist ini.']);
        }

        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $playlist->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('playlists.index')->with('success', 'Playlist berhasil diperbarui!');
    }

    public function destroy($id): RedirectResponse
    {
        $playlist = Playlist::findOrFail($id);

        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')->withErrors(['error' => 'Anda tidak berhak menghapus playlist ini.']);
        }

        $playlist->delete();

        return redirect()->route('playlists.index')->with('success', 'Playlist berhasil dihapus!');
    }

    public function addVideo(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
        ]);

        $playlist = Playlist::findOrFail($id);

        if ($playlist->user_id !== Auth::id()) {
            return redirect()->route('playlists.index')->withErrors(['error' => 'Anda tidak berhak menambahkan video ke playlist ini.']);
        }

        $videoId = $request->video_id;

        // Cek apakah video sudah ada di playlist
        if ($playlist->videos()->where('id', $videoId)->exists()) {
            return redirect()->route('videos.show', $videoId)->withErrors(['error' => 'Video ini sudah ada di playlist.']);
        }

        // Tambahkan video ke playlist
        $playlist->videos()->attach($videoId);

        return redirect()->route('videos.show', $videoId)->with('success', 'Video berhasil ditambahkan ke playlist!');
    }
}
