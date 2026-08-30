<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\InternshipParticipant;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternshipParticipantController extends Controller
{
    public function index()
    {
        $participants = InternshipParticipant::with([
            'program.company',
            'intern.user',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'root.internship-participants.index',
            compact('participants')
        );
    }

    public function create()
    {
        $programs = InternshipProgram::with('company')
            ->latest()
            ->get();

        $interns = Intern::with('user')
            ->latest()
            ->get();

        return view(
            'root.internship-participants.create',
            compact('programs', 'interns')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'joined_at' => [
                'nullable',
                'date',
            ],

            'removed_at' => [
                'nullable',
                'date',
                'after_or_equal:joined_at',
            ],
        ]);

        InternshipParticipant::create([
            'program_id' => $validated['program_id'],
            'intern_id' => $validated['intern_id'],
            'joined_at' =>
                $validated['joined_at'] ?? now(),
            'removed_at' =>
                $validated['removed_at'] ?? null,
        ]);

        return redirect()
            ->route(
                'root.internship-participants.index'
            )
            ->with(
                'success',
                'Peserta berhasil ditambahkan.'
            );
    }

    public function show(
        InternshipParticipant $internshipParticipant
    ) {
        $internshipParticipant->load([
            'program.company',
            'intern.user',
        ]);

        return view(
            'root.internship-participants.show',
            compact('internshipParticipant')
        );
    }

    public function edit(
        InternshipParticipant $internshipParticipant
    ) {
        $programs = InternshipProgram::with('company')
            ->latest()
            ->get();

        $interns = Intern::with('user')
            ->latest()
            ->get();

        return view(
            'root.internship-participants.edit',
            compact(
                'internshipParticipant',
                'programs',
                'interns'
            )
        );
    }

    public function update(
        Request $request,
        InternshipParticipant $internshipParticipant
    ) {
        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'joined_at' => [
                'required',
                'date',
            ],

            'removed_at' => [
                'nullable',
                'date',
                'after_or_equal:joined_at',
            ],
        ]);

        $internshipParticipant->update($validated);

        return redirect()
            ->route(
                'root.internship-participants.index'
            )
            ->with(
                'success',
                'Data peserta berhasil diperbarui.'
            );
    }

    public function destroy(
        InternshipParticipant $internshipParticipant
    ) {
        $internshipParticipant->delete();

        return redirect()
            ->route(
                'root.internship-participants.index'
            )
            ->with(
                'success',
                'Peserta berhasil dihapus.'
            );
    }
}