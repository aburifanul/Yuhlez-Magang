<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\InternshipProgram;
use App\Models\InternshipRegistration;
use Illuminate\Http\Request;

class InternshipRegistrationController extends Controller
{
    /**
     * Menampilkan semua pendaftaran dari program tertentu.
     */
    public function index(InternshipProgram $internshipProgram)
    {
        $this->authorizeCompany($internshipProgram);

        $registrations = $internshipProgram->registrations()
            ->with([
                'intern',
                'position',
            ])
            ->latest()
            ->get();

        return view(
            'company.internship-registrations.index',
            compact(
                'internshipProgram',
                'registrations'
            )
        );
    }

    /**
     * Menampilkan detail pendaftaran.
     */
    public function show(
        InternshipProgram $internshipProgram,
        InternshipRegistration $registration
    ) {
        $this->authorizeCompany($internshipProgram);

        $this->authorizeRegistration(
            $internshipProgram,
            $registration
        );

        $registration->load([
            'intern',
            'position',
            'program',
        ]);

        return view(
            'company.internship-registrations.show',
            compact(
                'internshipProgram',
                'registration'
            )
        );
    }

    /**
     * Menerima pendaftaran.
     *
     * Registration yang diterima nantinya dapat
     * dijadikan participant.
     */
    public function accept(
        InternshipProgram $internshipProgram,
        InternshipRegistration $registration
    ) {
        $this->authorizeCompany($internshipProgram);

        $this->authorizeRegistration(
            $internshipProgram,
            $registration
        );

        abort_if(
            $registration->status->value !== 'pending',
            422,
            'Pendaftaran ini sudah diproses.'
        );

        /*
         * Pastikan intern belum menjadi participant aktif
         * pada program ini.
         */
        $alreadyParticipant = $internshipProgram
            ->participants()
            ->where('intern_id', $registration->intern_id)
            ->whereNull('removed_at')
            ->exists();

        abort_if(
            $alreadyParticipant,
            422,
            'Intern tersebut sudah menjadi participant pada program ini.'
        );

        $registration->update([
            'status' => 'accepted',
            'rejection_reason' => null,
            'decided_at' => now(),
        ]);

        return redirect()
            ->route(
                'company.internship-programs.registrations.show',
                [
                    'internshipProgram' => $internshipProgram,
                    'registration' => $registration,
                ]
            )
            ->with(
                'success',
                'Pendaftaran berhasil diterima.'
            );
    }

    /**
     * Menolak pendaftaran.
     */
    public function reject(
        Request $request,
        InternshipProgram $internshipProgram,
        InternshipRegistration $registration
    ) {
        $this->authorizeCompany($internshipProgram);

        $this->authorizeRegistration(
            $internshipProgram,
            $registration
        );

        abort_if(
            $registration->status->value !== 'pending',
            422,
            'Pendaftaran ini sudah diproses.'
        );

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
            ],
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'decided_at' => now(),
        ]);

        return redirect()
            ->route(
                'company.internship-programs.registrations.show',
                [
                    'internshipProgram' => $internshipProgram,
                    'registration' => $registration,
                ]
            )
            ->with(
                'success',
                'Pendaftaran berhasil ditolak.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    /**
     * Memastikan program memang milik company
     * yang sedang login.
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
     * Memastikan registration memang milik
     * program tersebut.
     */
    protected function authorizeRegistration(
        InternshipProgram $internshipProgram,
        InternshipRegistration $registration
    ): void {
        abort_unless(
            $registration->program_id === $internshipProgram->id,
            404
        );
    }
}