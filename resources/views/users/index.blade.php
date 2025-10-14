<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div id="flash" class="mb-3 text-sm"></div>

                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full text-sm" id="users-table">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-left p-2">ID</th>
                                    <th class="text-left p-2">Name</th>
                                    <th class="text-left p-2">Email</th>
                                    <th class="text-left p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="users-tbody">
                                <tr><td class="p-2" colspan="4">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        const flash = document.getElementById('flash');
        const tbody = document.getElementById('users-tbody');

        const BASE_URL = '{{ url('') }}';
        const API_BASE = BASE_URL + '/api/v1';

        function setFlash(msg, ok = true) {
            flash.textContent = msg;
            flash.className = ok ? 'mb-3 text-green-600' : 'mb-3 text-red-600';
            if (msg) setTimeout(() => { flash.textContent = ''; }, 3000);
        }

        // Token lekérése (UI token az API-hívásokhoz)
        let token = '';
        try {
            const tRes = await fetch('{{ route('admin.users.ui-token') }}', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const tJson = await tRes.json();
            token = tJson.token || '';
            if (!token) throw new Error('Token missing');
        } catch (e) {
            setFlash('Nem sikerült UI tokent kérni. Jogosultság vagy beállítás hiba.', false);
            return;
        }

        const canWrite = {!! auth()->user()->can('users.write') ? 'true' : 'false' !!};

        async function loadUsers() {
            tbody.innerHTML = '<tr><td class="p-2" colspan="4">Loading...</td></tr>';
            try {
                const res = await fetch(`${API_BASE}/users`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    credentials: 'omit'
                });
                if (!res.ok) throw new Error('Hiba a lekérésnél');
                const data = await res.json();

                if (!Array.isArray(data)) {
                    tbody.innerHTML = '<tr><td class="p-2" colspan="4">Nincs megjeleníthető adat.</td></tr>';
                    return;
                }

                tbody.innerHTML = '';
                for (const u of data) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="p-2">${u.id}</td>
                        <td class="p-2">
                            <span class="user-name" data-id="${u.id}">${u.name ?? ''}</span>
                            ${canWrite ? `<input type="text" class="hidden border px-1 py-0.5 text-sm rounded user-name-input" data-id="${u.id}" value="${u.name ?? ''}">` : ''}
                        </td>
                        <td class="p-2">${u.email ?? ''}</td>
                        <td class="p-2 space-x-2">
                            ${canWrite ? `
                                <button class="px-2 py-1 border rounded text-xs edit-btn" data-id="${u.id}">Edit</button>
                                <button class="px-2 py-1 border rounded text-xs save-btn hidden" data-id="${u.id}">Save</button>
                                <button class="px-2 py-1 border rounded text-xs change-pass-btn" data-id="${u.id}">Change Password</button>
                                <button class="px-2 py-1 border rounded text-xs delete-btn" data-id="${u.id}">Delete</button>
                            ` : `<span class="text-gray-500 text-xs">read-only</span>`}
                        </td>
                    `;
                    tbody.appendChild(tr);

                    // Password form (külön sor)
                    if (canWrite) {
                        const passTr = document.createElement('tr');
                        passTr.innerHTML = `
                            <td></td>
                            <td colspan="3" class="p-2 hidden password-row" data-id="${u.id}">
                                <div class="flex flex-col sm:flex-row gap-2 items-start">
                                    <input type="password" placeholder="New password" class="border px-2 py-1 rounded text-sm pass-input" data-id="${u.id}">
                                    <input type="password" placeholder="Confirm password" class="border px-2 py-1 rounded text-sm pass-confirm-input" data-id="${u.id}">
                                    <button class="px-2 py-1 border rounded text-xs apply-pass-btn" data-id="${u.id}">Apply</button>
                                    <button class="px-2 py-1 border rounded text-xs cancel-pass-btn" data-id="${u.id}">Cancel</button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(passTr);
                    }
                }

                bindActions();
            } catch (e) {
                tbody.innerHTML = `<tr><td class="p-2" colspan="4">Hiba történt a lista betöltésekor.</td></tr>`;
            }
        }

        function bindActions() {
            if (!canWrite) return;

            // Edit -> input megjelenítése
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', (ev) => {
                    const id = ev.currentTarget.dataset.id;
                    const input = document.querySelector(`.user-name-input[data-id="${id}"]`);
                    const span = document.querySelector(`.user-name[data-id="${id}"]`);
                    const save = document.querySelector(`.save-btn[data-id="${id}"]`);
                    if (input && span && save) {
                        input.classList.remove('hidden');
                        save.classList.remove('hidden');
                        span.classList.add('hidden');
                    }
                });
            });

            // Save -> PUT /users/{id}
            document.querySelectorAll('.save-btn').forEach(btn => {
                btn.addEventListener('click', async (ev) => {
                    const id = ev.currentTarget.dataset.id;
                    const input = document.querySelector(`.user-name-input[data-id="${id}"]`);
                    if (!input) return;
                    try {
                        const res = await fetch(`${API_BASE}/users/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${token}`
                            },
                            body: JSON.stringify({ name: input.value })
                        });
                        if (!res.ok) {
                            const err = await res.json().catch(()=>({}));
                            throw new Error(err.message || 'Update failed');
                        }
                        setFlash('Mentve.');
                        await loadUsers();
                    } catch (e) {
                        setFlash('Mentés sikertelen: ' + e.message, false);
                    }
                });
            });

            // Delete -> DELETE /users/{id}
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', async (ev) => {
                    const id = ev.currentTarget.dataset.id;
                    if (!confirm('Biztosan törlöd ezt a felhasználót?')) return;
                    try {
                        const res = await fetch(`${API_BASE}/users/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${token}`
                            }
                        });
                        if (res.status !== 204 && !res.ok) {
                            const err = await res.json().catch(()=>({}));
                            throw new Error(err.message || 'Delete failed');
                        }
                        setFlash('Törölve.');
                        await loadUsers();
                    } catch (e) {
                        setFlash('Törlés sikertelen: ' + e.message, false);
                    }
                });
            });

            // Change Password -> form mutatása/elrejtése
            document.querySelectorAll('.change-pass-btn').forEach(btn => {
                btn.addEventListener('click', (ev) => {
                    const id = ev.currentTarget.dataset.id;
                    const row = document.querySelector(`.password-row[data-id="${id}"]`);
                    if (row) row.classList.toggle('hidden');
                });
            });

            // Cancel Password -> elrejtés + ürítés
            document.querySelectorAll('.cancel-pass-btn').forEach(btn => {
                btn.addEventListener('click', (ev) => {
                    const id = ev.currentTarget.dataset.id;
                    const row = document.querySelector(`.password-row[data-id="${id}"]`);
                    const p = document.querySelector(`.pass-input[data-id="${id}"]`);
                    const pc = document.querySelector(`.pass-confirm-input[data-id="${id}"]`);
                    if (p) p.value = '';
                    if (pc) pc.value = '';
                    if (row) row.classList.add('hidden');
                });
            });

            // Apply Password -> PUT /users/{id} password+confirmation
document.querySelectorAll('.apply-pass-btn').forEach(btn => {
  btn.addEventListener('click', async (ev) => {
    const id = ev.currentTarget.dataset.id;
    const p = document.querySelector(`.pass-input[data-id="${id}"]`);
    const pc = document.querySelector(`.pass-confirm-input[data-id="${id}"]`);
    if (!p || !pc) return;

    const password = p.value || '';
    const password_confirmation = pc.value || '';

    if (password.length < 8) {
      setFlash('A jelszónak legalább 8 karakteresnek kell lennie.', false);
      return;
    }
    if (password !== password_confirmation) {
      setFlash('A jelszó és megerősítése nem egyezik.', false);
      return;
    }

    try {
      const res = await fetch(`${API_BASE}/users/${id}`, {
        method: 'PUT',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ password, password_confirmation })
      });
      if (!res.ok) {
        const err = await res.json().catch(()=>({}));
        throw new Error(err.message || 'Password update failed');
      }

      setFlash('Jelszó frissítve. Tokenek érvénytelenítve, új token kérése...');

      // 🔁 ÚJ TOKEN KÉRÉSE (mert az előzőt most érvénytelenítettük)
      const tRes2 = await fetch('{{ route('admin.users.ui-token') }}', {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      });
      const tJson2 = await tRes2.json();
      token = tJson2.token || '';
      if (!token) throw new Error('Token re-issue failed');

      // ürítés + elrejtés + frissítés
      p.value = ''; pc.value = '';
      const row = document.querySelector(`.password-row[data-id="${id}"]`);
      if (row) row.classList.add('hidden');

      await loadUsers();
      setFlash('Új token kiadva, lista frissítve.');
    } catch (e) {
      setFlash('Hiba jelszó frissítéskor: ' + e.message, false);
    }
  });
});

        }

        await loadUsers();
    });
    </script>
</x-app-layout>
