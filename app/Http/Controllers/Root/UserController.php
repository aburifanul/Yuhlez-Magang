<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan semua user.
     */
    public function index(): View
    {
        $users = User::query()
            ->with(['company', 'intern'])
            ->latest()
            ->paginate(10);

        return view('root.users.index', compact('users'));
    }

    /**
     * Form tambah user.
     */
    public function create(): View
    {
        return view('root.users.create');
    }

    /**
     * Menyimpan user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role' => [
                'required',
                Rule::in([
                    'root',
                    'company',
                    'intern',
                ]),
            ],
        ]);

        User::create($validated);

        return redirect()
            ->route('root.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail user.
     */
    public function show(User $user): View
    {
        $user->load([
            'company',
            'intern',
        ]);

        return view('root.users.show', compact('user'));
    }

    /**
     * Form edit user.
     */
    public function edit(User $user): View
    {
        return view('root.users.edit', compact('user'));
    }

    /**
     * Memperbarui user.
     */
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in([
                    'root',
                    'company',
                    'intern',
                ]),
            ],
        ]);

        $user->update($validated);

        return redirect()
            ->route('root.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('root.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}