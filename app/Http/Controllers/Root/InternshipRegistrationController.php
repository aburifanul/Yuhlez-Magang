<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\InternshipPosition;
use App\Models\InternshipProgram;
use App\Models\InternshipRegistration;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InternshipRegistrationController extends Controller
{
    public function index()
    {
        $registrations = InternshipRegistration::with([
            'program.company',
            'position',
            'intern.user',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'root.internship-registrations.index',
            compact('registrations')
        );
    }

    public function create()
    {
        $programs = InternshipProgram::with('company')
            ->latest()
            ->get();

        $positions = InternshipPosition::with('program')
            ->latest()
            ->get();

        $interns = Intern::with('user')
            ->latest()
            ->get();

        return view(
            'root.internship-registrations.create',
            compact(
                'programs',
                'positions',
                'interns'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

            'position_id' => [
                'required',
                'exists:internship_positions,id',
            ],

            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'status' => [
                'required',
                'in:pending,accepted,rejected',
            ],

            'rejection_reason' => [
                'nullable',
                'string',
            ],
        ]);

        $this->validatePositionBelongsToProgram(
            $validated['position_id'],
            $validated['program_id']
        );

        InternshipRegistration::create([
            'program_id' => $validated['program_id'],
            'position_id' => $validated['position_id'],
            'intern_id' => $validated['intern_id'],
            'status' => $validated['status'],
            'rejection_reason' =>
                $validated['rejection_reason'] ?? null,
            'decided_at' =>
                $validated['status'] === 'pending'
                    ? null
                    : now(),
        ]);

        return redirect()
            ->route('root.internship-registrations.index')
            ->with(
                'success',
                'Lamaran magang berhasil dibuat.'
            );
    }

    public function show(
        InternshipRegistration $internshipRegistration
    ) {
        $internshipRegistration->load([
            'program.company',
            'position',
            'intern.user',
        ]);

        return view(
            'root.internship-registrations.show',
            compact('internshipRegistration')
        );
    }

    public function edit(
        InternshipRegistration $internshipRegistration
    ) {
        $programs = InternshipProgram::with('company')
            ->latest()
            ->get();

        $positions = InternshipPosition::with('program')
            ->latest()
            ->get();

        $interns = Intern::with('user')
            ->latest()
            ->get();

        return view(
            'root.internship-registrations.edit',
            compact(
                'internshipRegistration',
                'programs',
                'positions',
                'interns'
            )
        );
    }

    public function update(
        Request $request,
        InternshipRegistration $internshipRegistration
    ) {
        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

            'position_id' => [
                'required',
                'exists:internship_positions,id',
            ],

            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'status' => [
                'required',
                'in:pending,accepted,rejected',
            ],

            'rejection_reason' => [
                'nullable',
                'string',
            ],
        ]);

        $this->validatePositionBelongsToProgram(
            $validated['position_id'],
            $validated['program_id']
        );

        $internshipRegistration->update([
            'program_id' => $validated['program_id'],
            'position_id' => $validated['position_id'],
            'intern_id' => $validated['intern_id'],
            'status' => $validated['status'],
            'rejection_reason' =>
                $validated['rejection_reason'] ?? null,
            'decided_at' =>
                $validated['status'] === 'pending'
                    ? null
                    : now(),
        ]);

        return redirect()
            ->route(
                'root.internship-registrations.index'
            )
            ->with(
                'success',
                'Data lamaran berhasil diperbarui.'
            );
    }

    public function destroy(
        InternshipRegistration $internshipRegistration
    ) {
        $internshipRegistration->delete();

        return redirect()
            ->route(
                'root.internship-registrations.index'
            )
            ->with(
                'success',
                'Lamaran berhasil dihapus.'
            );
    }

    protected function validatePositionBelongsToProgram(
        int $positionId,
        int $programId
    ): void {
        $position = InternshipPosition::findOrFail(
            $positionId
        );

        if ($position->program_id != $programId) {
            throw ValidationException::withMessages([
                'position_id' =>
                    'Posisi tersebut bukan milik program yang dipilih.',
            ]);
        }
    }
}