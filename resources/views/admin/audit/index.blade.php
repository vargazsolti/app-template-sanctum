<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Log') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <section class="bg-white shadow-sm rounded-2xl border">
                <header class="px-6 py-4 border-b">
                    <form method="GET" class="grid md:grid-cols-4 gap-3">
                        <input type="text" name="event" value="{{ request('event') }}" class="border rounded-lg px-3 py-2"
                               placeholder="event (e.g. user_password_changed)">
                        <input type="text" name="causer" value="{{ request('causer') }}" class="border rounded-lg px-3 py-2"
                               placeholder="causer (name/email)">
                        <select name="subject_type" class="border rounded-lg px-3 py-2">
                            <option value="">subject type</option>
                            @foreach($subjectTypes as $t)
                                <option value="{{ $t }}" @selected(request('subject_type') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2">
                            <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">Filter</button>
                            <a href="{{ route('admin.audit.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Reset</a>
                        </div>
                    </form>
                </header>

                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left p-3 font-medium text-gray-600 w-48">When</th>
                                <th class="text-left p-3 font-medium text-gray-600">Event</th>
                                <th class="text-left p-3 font-medium text-gray-600">Subject</th>
                                <th class="text-left p-3 font-medium text-gray-600">Causer</th>
                                <th class="text-left p-3 font-medium text-gray-600">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                        @forelse ($logs as $a)
                            <tr class="align-top">
                                <td class="p-3 text-gray-700">
                                    {{ $a->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="p-3">
                                    <div class="font-medium">{{ $a->description }}</div>
                                    <div class="text-gray-500">{{ $a->event ?? '' }}</div>
                                </td>
                                <td class="p-3">
                                    <div>{{ $a->subject_type ?? '—' }}</div>
                                    <div class="text-gray-500">#{{ $a->subject_id ?? '—' }}</div>
                                </td>
                                <td class="p-3">
                                    @if($a->causer)
                                        <div class="font-medium">{{ $a->causer->name }}</div>
                                        <div class="text-gray-500">{{ $a->causer->email }}</div>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @php $props = $a->properties?->toArray() ?? []; @endphp
                                    @if($props)
                                        <pre class="text-xs bg-gray-50 border rounded p-2 max-w-[560px] overflow-auto">{{ json_encode($props, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td class="p-3 text-gray-500" colspan="5">No entries.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t">
                    {{ $logs->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
