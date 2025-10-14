<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles & Permissions') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 p-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            {{-- ROLES (card) --}}
            <section class="bg-white shadow-sm rounded-2xl border">
                <header class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ __('Roles') }}</h3>
                </header>

                <div class="p-6 space-y-6">
                    {{-- Create Role --}}
                    <form method="POST" action="{{ route('admin.access.roles.store') }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input name="name" class="border rounded-lg px-3 py-2 w-full sm:max-w-md" placeholder="new role name" required>
                        <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm w-full sm:w-auto">Add</button>
                    </form>
                    @error('name')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror>

                    {{-- Roles list --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left p-3 font-medium text-gray-600 w-40">Role</th>
                                    <th class="text-left p-3 font-medium text-gray-600">Permissions</th>
                                    <th class="text-left p-3 font-medium text-gray-600 w-64">Add Permission</th>
                                    <th class="text-left p-3 font-medium text-gray-600 w-36">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse ($roles as $role)
                                    <tr class="align-top">
                                        <td class="p-3 font-medium">{{ $role->name }}</td>

                                        <td class="p-3">
                                            <div class="flex flex-wrap gap-2">
                                                @forelse ($role->permissions as $perm)
                                                    <form method="POST"
                                                          action="{{ route('admin.access.roles.permissions.detach', [$role, $perm]) }}"
                                                          class="inline-block">
                                                        @csrf @method('DELETE')
                                                        <span class="inline-flex items-center gap-2 border rounded-lg px-2.5 py-1">
                                                            <span class="truncate max-w-[240px]">{{ $perm->name }}</span>
                                                            <button title="Revoke" class="text-xs text-red-600 leading-none">×</button>
                                                        </span>
                                                    </form>
                                                @empty
                                                    <span class="text-gray-500">—</span>
                                                @endforelse
                                            </div>
                                        </td>

                                        <td class="p-3">
                                            <form method="POST" action="{{ route('admin.access.roles.permissions.attach', $role) }}"
                                                  class="flex flex-col sm:flex-row gap-2">
                                                @csrf
                                                <select name="permission" class="border rounded-lg px-3 py-2 w-full">
                                                    @foreach ($allPermissions as $p)
                                                        <option value="{{ $p->name }}">{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-xs sm:text-sm w-full sm:w-auto">Add</button>
                                            </form>
                                        </td>

                                        <td class="p-3">
                                            <form method="POST" action="{{ route('admin.access.roles.destroy', $role) }}"
                                                  onsubmit="return confirm('Delete role {{ $role->name }}?')">
                                                @csrf @method('DELETE')
                                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-xs sm:text-sm w-full sm:w-auto">Delete Role</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="p-3 text-gray-500" colspan="4">No roles yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- PERMISSIONS (card) --}}
            <section class="bg-white shadow-sm rounded-2xl border">
                <header class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ __('Permissions') }}</h3>
                </header>

                <div class="p-6 space-y-6">
                    {{-- Create Permission --}}
                    <form method="POST" action="{{ route('admin.access.permissions.store') }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input name="name" class="border rounded-lg px-3 py-2 w-full sm:max-w-xl" placeholder="new permission (e.g. users.read)" required>
                        <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm w-full sm:w-auto">Add</button>
                    </form>
                    @error('name')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror

                    {{-- Permissions list --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left p-3 font-medium text-gray-600">Permission</th>
                                    <th class="text-left p-3 font-medium text-gray-600 w-36">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse ($permissions as $permission)
                                    <tr>
                                        <td class="p-3">
                                            <span class="inline-block truncate max-w-[720px]">{{ $permission->name }}</span>
                                        </td>
                                        <td class="p-3">
                                            <form method="POST" action="{{ route('admin.access.permissions.destroy', $permission) }}"
                                                  onsubmit="return confirm('Delete permission {{ $permission->name }}?')">
                                                @csrf @method('DELETE')
                                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-xs sm:text-sm w-full sm:w-auto">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="p-3 text-gray-500" colspan="2">No permissions yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <div>
                <a class="underline text-sm text-gray-700" href="{{ route('admin.access.users') }}">
                    → Assign roles/permissions to users
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
