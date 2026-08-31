<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\InternshipPosition;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;

class InternshipPositionController extends Controller
{
    /**
     * Menampilkan semua posisi dari program tertentu.
     */
    public function index(InternshipProgram $internshipProgram)
    {
        $this->authorizeCompany($internshipProgram);

        $positions = $internshipProgram->positions()
            ->orderBy('name')
            ->get();

        return view(
            'company.internship-positions.index',
            compact('internshipProgram', 'positions')
        );
    }

    /**
     * Menampilkan form tambah posisi.
     */
    public function create(InternshipProgram $internshipProgram)
    {
        $this->authorizeCompany($internshipProgram);

        return view(
            'company.internship-positions.create',
            compact('internshipProgram')
        );
    }

    /**
     * Menyimpan posisi baru.
     */
    public function store(
        Request $request,
        InternshipProgram $internshipProgram
    ) {
        $this->authorizeCompany($internshipProgram);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'quota' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $internshipProgram->positions()->create([
            'name' => $validated['name'],
            'quota' => $validated['quota'],
        ]);

        return redirect()
            ->route(
                'company.internship-programs.positions.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Posisi magang berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail posisi.
     */
    public function show(
        InternshipProgram $internshipProgram,
        InternshipPosition $position
    ) {
        $this->authorizePosition(
            $internshipProgram,
            $position
        );

        $position->load([
            'registrations.intern',
        ]);

        return view(
            'company.internship-positions.show',
            compact('internshipProgram', 'position')
        );
    }

    /**
     * Menampilkan form edit posisi.
     */
    public function edit(
        InternshipProgram $internshipProgram,
        InternshipPosition $position
    ) {
        $this->authorizePosition(
            $internshipProgram,
            $position
        );

        return view(
            'company.internship-positions.edit',
            compact('internshipProgram', 'position')
        );
    }

    /**
     * Memperbarui posisi.
     */
    public function update(
        Request $request,
        InternshipProgram $internshipProgram,
        InternshipPosition $position
    ) {
        $this->authorizePosition(
            $internshipProgram,
            $position
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'quota' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $position->update([
            'name' => $validated['name'],
            'quota' => $validated['quota'],
        ]);

        return redirect()
            ->route(
                'company.internship-programs.positions.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Posisi magang berhasil diperbarui.'
            );
    }

    /**
     * Menghapus posisi.
     */
    public function destroy(
        InternshipProgram $internshipProgram,
        InternshipPosition $position
    ) {
        $this->authorizePosition(
            $internshipProgram,
            $position
        );

        $position->delete();

        return redirect()
            ->route(
                'company.internship-programs.positions.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Posisi magang berhasil dihapus.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    /**
     * Memastikan program adalah milik company yang sedang login.
     */
    protected function authorizeCompany(
        InternshipProgram $internshipProgram
    ): void {
        $company = auth()->user()->company;

        abort_unless(
            $company &&
            $internshipProgram->company_id === $company->id,
            403
        );
    }

    /**
     * Memastikan posisi milik program yang sedang dibuka
     * dan program tersebut milik company yang sedang login.
     */
    protected function authorizePosition(
        InternshipProgram $internshipProgram,
        InternshipPosition $position
    ): void {
        $this->authorizeCompany($internshipProgram);

        abort_unless(
            $position->program_id === $internshipProgram->id,
            404
        );
    }
}