<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('admin.informasi.index', ['tipe' => $tipe]) }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Edit Informasi</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.informasi.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.informasi._form')
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>