<?php

namespace App\Http\Controllers\Root;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InternController extends Controller
{
    /**
     * Menampilkan semua intern.
     */
    public function index()
    {
        $interns = Intern::with('user')
            ->latest()
            ->paginate(10);

        return view('root.interns.index', compact('interns'));
    }

    /**
     * Form membuat intern.
     */
    public function create()
    {
        return view('root.interns.create');
    }

    /**
     * Root membuat akun User + profile Intern.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            'photo' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:30',
            ],

            'contact_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => ['nullable', 'string'],

            'cv_path' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
        ]);

        DB::transaction(function () use ($validated, $request) {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => UserRole::INTERN,
            ]);

            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request
                    ->file('photo')
                    ->store('interns/photos', 'public');
            }

            $cvPath = null;

            if ($request->hasFile('cv_path')) {
                $cvPath = $request
                    ->file('cv_path')
                    ->store('interns/cv', 'public');
            }

            Intern::create([
                'user_id' => $user->id,

                'slug' => Str::slug($validated['name'])
                    . '-' . Str::random(5),

                'short_description' =>
                    $validated['short_description'] ?? null,

                'description' =>
                    $validated['description'] ?? null,

                'photo' => $photoPath,

                'whatsapp' =>
                    $validated['whatsapp'] ?? null,

                'contact_email' =>
                    $validated['contact_email'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'cv_path' => $cvPath,

                'is_profile_complete' => 0,
            ]);
        });

        return redirect()
            ->route('root.interns.index')
            ->with('success', 'Akun intern berhasil dibuat.');
    }

    /**
     * Detail intern.
     */
    public function show(Intern $intern)
    {
        $intern->load([
            'user',
            'registrations.program',
            'registrations.position',
            'participants.program',
            'certificates.program',
            'workMembers.work',
        ]);

        return view(
            'root.interns.show',
            compact('intern')
        );
    }

    /**
     * Form edit intern.
     */
    public function edit(Intern $intern)
    {
        $intern->load('user');

        return view(
            'root.interns.edit',
            compact('intern')
        );
    }

    /**
     * Update intern.
     */
    public function update(
        Request $request,
        Intern $intern
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $intern->user_id,
            ],

            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            'photo' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:30',
            ],

            'contact_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => ['nullable', 'string'],

            'cv_path' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],

            'is_profile_complete' => [
                'nullable',
                'boolean',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $request,
            $intern
        ) {
            $intern->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            $data = [
                'short_description' =>
                    $validated['short_description'] ?? null,

                'description' =>
                    $validated['description'] ?? null,

                'whatsapp' =>
                    $validated['whatsapp'] ?? null,

                'contact_email' =>
                    $validated['contact_email'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'is_profile_complete' =>
                    $validated['is_profile_complete'] ?? 0,
            ];

            if ($request->hasFile('photo')) {
                $data['photo'] = $request
                    ->file('photo')
                    ->store('interns/photos', 'public');
            }

            if ($request->hasFile('cv_path')) {
                $data['cv_path'] = $request
                    ->file('cv_path')
                    ->store('interns/cv', 'public');
            }

            $intern->update($data);
        });

        return redirect()
            ->route('root.interns.index')
            ->with('success', 'Data intern berhasil diperbarui.');
    }

    /**
     * Soft delete intern + user.
     */
    public function destroy(Intern $intern)
    {
        DB::transaction(function () use ($intern) {
            $intern->delete();
            $intern->user->delete();
        });

        return redirect()
            ->route('root.interns.index')
            ->with('success', 'Intern berhasil dihapus.');
    }
}