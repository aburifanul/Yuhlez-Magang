<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\InternshipPosition;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;

class InternshipPositionController extends Controller
{
    public function index()
    {
        $positions = InternshipPosition::with('program.company')
            ->latest()
            ->paginate(10);

        return view(
            'root.internship-positions.index',
            compact('positions')
        );
    }

    public function create()
    {
        $programs = InternshipProgram::with('company')
            ->latest()
            ->get();

        return view(
            'root.internship-positions.create',
            compact('programs')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

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

        InternshipPosition::create($validated);

        return redirect()
            ->route('root.internship-positions.index')
            ->with(
                'success',
                'Posisi magang berhasil dibuat.'
            );
    }

    public function show(InternshipPosition $internshipPosition)
    {
        $internshipPosition->load([
            'program.company',
            'registrations.intern.user',
        ]);

        return view(
            'root.internship-positions.show',
            compact('internshipPosition')
        );
    }

    public function edit(
        InternshipPosition $internshipPosition
    ) {
        $programs = InternshipProgram::with('company')
            ->latest()
            ->get();

        return view(
            'root.internship-positions.edit',
            compact(
                'internshipPosition',
                'programs'
            )
        );
    }

    public function update(
        Request $request,
        InternshipPosition $internshipPosition
    ) {
        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

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

        $internshipPosition->update($validated);

        return redirect()
            ->route('root.internship-positions.index')
            ->with(
                'success',
                'Posisi magang berhasil diperbarui.'
            );
    }

    public function destroy(
        InternshipPosition $internshipPosition
    ) {
        $internshipPosition->delete();

        return redirect()
            ->route('root.internship-positions.index')
            ->with(
                'success',
                'Posisi magang berhasil dihapus.'
            );
    }
}