<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        $tipe = in_array($request->get('tipe'), Informasi::TIPES) ? $request->get('tipe') : 'berita';

        $items = Informasi::tipe($tipe)
            ->orderBy('urutan')
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        $counts = Informasi::selectRaw('tipe, COUNT(*) AS c')
            ->groupBy('tipe')
            ->pluck('c', 'tipe')
            ->toArray();

        return view('admin.informasi.index', compact('items', 'tipe', 'counts'));
    }

    public function create(Request $request)
    {
        $tipe = in_array($request->get('tipe'), Informasi::TIPES) ? $request->get('tipe') : 'berita';

        return view('admin.informasi.create', compact('tipe'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['tipe'] = $request->input('tipe');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->input('media_type', 'gambar') === 'video') {
            $data['gambar'] = null;
        } else {
            $data['gambar'] = $this->uploadImage($request);
            $data['video_url'] = null;
        }

        Informasi::create($data);

        return to_route('admin.informasi.index', ['tipe' => $data['tipe']])
            ->with('success', 'Informasi berhasil ditambahkan.');
    }

    public function edit(Request $request, Informasi $informasi)
    {
        return view('admin.informasi.edit', [
            'item' => $informasi,
            'tipe' => $informasi->tipe,
        ]);
    }

    public function update(Request $request, Informasi $informasi)
    {
        $data = $this->validated($request);
        $data['tipe'] = $informasi->tipe;
        $data['is_active'] = $request->boolean('is_active');

        if ($request->input('media_type', 'gambar') === 'video') {
            $this->deleteImage($informasi);
            $data['gambar'] = null;
        } else {
            if ($request->hasFile('gambar')) {
                $this->deleteImage($informasi);
                $data['gambar'] = $this->uploadImage($request);
            } else {
                $data['gambar'] = $informasi->gambar;
            }
            $data['video_url'] = null;
        }

        $informasi->update($data);

        return to_route('admin.informasi.index', ['tipe' => $informasi->tipe])
            ->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy(Informasi $informasi)
    {
        $this->deleteImage($informasi);
        $informasi->delete();

        return to_route('admin.informasi.index', ['tipe' => $informasi->tipe])
            ->with('success', 'Informasi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'jenis' => 'nullable|string|max:100',
            'penulis' => 'nullable|string|max:255',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'tanggal' => 'nullable|date',
            'urutan' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'video_url' => 'nullable|url|max:255',
        ]);
    }

    private function uploadImage(Request $request): ?string
    {
        if (! $request->hasFile('gambar')) {
            return null;
        }

        $img = Image::read($request->file('gambar')->getRealPath());
        $filename = 'informasi_'.now()->format('Ymd_His').'_'.uniqid().'.webp';

        Storage::disk('public')->makeDirectory('informasi');
        $img->scaleDown(width: 1600);
        $img->toWebp(80)->save(storage_path('app/public/informasi/'.$filename));

        return 'informasi/'.$filename;
    }

    private function deleteImage(?Informasi $item): void
    {
        if ($item && $item->gambar) {
            Storage::disk('public')->delete($item->gambar);
        }
    }
}
