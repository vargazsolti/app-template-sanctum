<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Roles & Permissions to Users') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 p-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left p-2">User</th>
                                <th class="text-left p-2">Roles</th>
                                <th class="text-left p-2">Add Role</th>
                                <th class="text-left p-2">Permissions</th>
                                <th class="text-left p-2">Add Permission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $u)
                                <tr class="border-b align-top">
                                    <td class="p-2">
                                        <div class="font-medium">{{ $u->name }}</div>
                                        <div class="text-gray-600">{{ $u->email }}</div>
                                    </td>

                                    <td class="p-2">
                                        @forelse ($u->roles as $role)
                                            <form method="POST" action="{{ route('admin.access.users.roles.detach', [$u, $role]) }}"
                                                  class="inline-block mb-1">
                                                @csrf @method('DELETE')
                                                <span class="inline-flex items-center gap-2 border rounded px-2 py-0.5">
                                                    {{ $role->name }}
                                                    <button title="Remove" class="text-xs text-red-600">×</button>
                                                </span>
                                            </form>
                                        @empty
                                            <span class="text-gray-500">—</span>
                                        @endforelse
                                    </td>

                                    <td class="p-2">
                                        <form method="POST" action="{{ route('admin.access.users.roles.attach', $u) }}" class="flex gap-2">
                                            @csrf
                                            <select name="role" class="border rounded px-2 py-1">
                                                @foreach ($allRoles as $r)
                                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                            <button class="px-2 py-1 border rounded text-xs">Add</button>
                                        </form>
                                    </td>

                                    <td class="p-2">
                                        @forelse ($u->permissions as $perm)
                                            <form method="POST" action="{{ route('admin.access.users.permissions.revoke', [$u, $perm]) }}"
                                                  class="inline-block mb-1">
                                                @csrf @method('DELETE')
                                                <span class="inline-flex items-center gap-2 border rounded px-2 py-0.5">
                                                    {{ $perm->name }}
                                                    <button title="Revoke" class="text-xs text-red-600">×</button>
                                                </span>
                                            </form>
                                        @empty
                                            <span class="text-gray-500">—</span>
                                        @endforelse
                                    </td>

                                    <td class="p-2">
                                        <form method="POST" action="{{ route('admin.access.users.permissions.give', $u) }}" class="flex gap-2">
                                            @csrf
                                            <select name="permission" class="border rounded px-2 py-1">
                                                @foreach ($allPermissions as $p)
                                                    <option value="{{ $p->name }}">{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                            <button class="px-2 py-1 border rounded text-xs">Add</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>

            <div>
                <a class="underline text-sm text-gray-700" href="{{ route('admin.access.roles-permissions') }}">← Manage roles & permissions</a>
            </div>
        </div>
    </div>
</x-app-layout>
