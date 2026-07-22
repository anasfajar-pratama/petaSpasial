<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Tambah User</a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach ($user->roles as $role)
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $role->name === 'Administrator' ? 'bg-purple-100 text-purple-700' : ($role->name === 'Operator' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                        @if ($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.admin>
