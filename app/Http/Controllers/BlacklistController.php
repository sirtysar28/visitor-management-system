<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use App\Support\Vms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlacklistController extends Controller
{
    public function index()
    {
        $blacklists = Blacklist::with('creator')->latest()->paginate(10);

        return view('blacklist.index', compact('blacklists'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'id_number' => ['nullable', 'string', 'max:60'],
            'company' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'reason' => ['required', 'string', 'max:500'],
            'type' => ['required', 'in:blacklist,watchlist'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('blacklists', 'public');
        }

        $data['created_by'] = auth()->id();
        Blacklist::create($data);

        Vms::audit('create', 'blacklist', "Menambahkan {$data['type']}: {$data['full_name']}");

        return back()->with('success', 'Data berhasil ditambahkan.');
    }

    public function toggle(Blacklist $blacklist)
    {
        $blacklist->update(['is_active' => ! $blacklist->is_active]);

        Vms::audit('update', 'blacklist', 'Mengubah status ' . $blacklist->full_name);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy(Blacklist $blacklist)
    {
        if ($blacklist->photo) {
            Storage::disk('public')->delete($blacklist->photo);
        }
        $name = $blacklist->full_name;
        $blacklist->delete();

        Vms::audit('delete', 'blacklist', "Menghapus data {$name}");

        return back()->with('success', 'Data berhasil dihapus.');
    }
}
