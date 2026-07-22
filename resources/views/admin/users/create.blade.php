<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Tambah User</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-3 py-2" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-3 py-2" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded-lg px-3 py-2" required>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full border rounded-lg px-3 py-2" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>
