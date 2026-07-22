<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Tambah Role</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-3 py-2" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                <div class="space-y-2 max-h-64 overflow-y-auto border rounded-lg p-3">
                    @foreach ($permissions as $group => $groupPerms)
                        <div class="mb-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ $group }}</p>
                            @foreach ($groupPerms as $perm)
                                <label class="inline-flex items-center mr-4 mb-1">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="rounded border-gray-300">
                                    <span class="ml-1 text-sm">{{ $perm->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>
