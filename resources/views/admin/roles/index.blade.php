<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Role</h1>
        <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Tambah Role</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($roles as $role)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $role->name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach ($role->permissions as $perm)
                                <span class="inline-flex px-2 py-0.5 text-xs bg-gray-100 text-gray-700 rounded">{{ $perm->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if ($role->name !== 'Administrator')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Hapus role ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                        @else
                        <span class="text-gray-400 text-sm">Default</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $roles->links() }}
        </div>
    </div>
</x-layouts.admin>
