<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\InternshipParticipant;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;

class InternshipParticipantController extends Controller
{
    /**
     * Menampilkan semua participant dari program tertentu.
     */
    public function index(InternshipProgram $internshipProgram)
    {
        $this->authorizeCompany($internshipProgram);

        $participants = $internshipProgram->participants()
            ->with('intern')
            ->latest('joined_at')
            ->get();

        return view(
            'company.internship-participants.index',
            compact('internshipProgram', 'participants')
        );
    }

    /**
     * Menampilkan form tambah participant.
     *
     * Hanya intern yang sudah diterima pada program
     * yang dapat dijadikan participant.
     */
    public function create(InternshipProgram $internshipProgram)
    {
        $this->authorizeCompany($internshipProgram);

        $acceptedRegistrations = $internshipProgram->registrations()
            ->where('status', 'accepted')
            ->with([
                'intern',
                'position',
            ])
            ->whereDoesntHave('intern.participants', function ($query) use ($internshipProgram) {
                $query->where('program_id', $internshipProgram->id)
                    ->whereNull('removed_at');
            })
            ->latest()
            ->get();

        return view(
            'company.internship-participants.create',
            compact(
                'internshipProgram',
                'acceptedRegistrations'
            )
        );
    }

    /**
     * Menyimpan participant baru.
     *
     * Hanya registration dengan status accepted
     * yang dapat menjadi participant.
     */
    public function store(
        Request $request,
        InternshipProgram $internshipProgram
    ) {
        $this->authorizeCompany($internshipProgram);

        $validated = $request->validate([
            'intern_id' => [
                'required',
                'integer',
                'exists:interns,id',
            ],
        ]);

        /*
         * Pastikan intern memang memiliki registration
         * yang sudah accepted pada program ini.
         */
        $acceptedRegistration = $internshipProgram
            ->registrations()
            ->where('intern_id', $validated['intern_id'])
            ->where('status', 'accepted')
            ->first();

        abort_unless(
            $acceptedRegistration,
            422,
            'Intern belum memiliki pendaftaran yang diterima pada program ini.'
        );

        /*
         * Cek participant yang sudah ada.
         *
         * Tidak menggunakan withTrashed()
         * karena InternshipParticipant tidak menggunakan SoftDeletes.
         */
        $participant = InternshipParticipant::query()
            ->where('program_id', $internshipProgram->id)
            ->where('intern_id', $validated['intern_id'])
            ->first();

        if ($participant) {

            /*
             * Jika participant sebelumnya sudah dikeluarkan,
             * aktifkan kembali participant tersebut.
             */
            if ($participant->removed_at !== null) {
                $participant->update([
                    'removed_at' => null,
                    'joined_at' => now(),
                ]);

                return redirect()
                    ->route(
                        'company.internship-programs.participants.index',
                        $internshipProgram
                    )
                    ->with(
                        'success',
                        'Participant berhasil diaktifkan kembali.'
                    );
            }

            abort(
                422,
                'Intern tersebut sudah menjadi participant pada program ini.'
            );
        }

        /*
         * Buat participant baru.
         */
        InternshipParticipant::create([
            'program_id' => $internshipProgram->id,
            'intern_id' => $validated['intern_id'],
            'joined_at' => now(),
            'removed_at' => null,
        ]);

        return redirect()
            ->route(
                'company.internship-programs.participants.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Participant berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail participant.
     */
    public function show(
        InternshipProgram $internshipProgram,
        InternshipParticipant $participant
    ) {
        $this->authorizeCompany($internshipProgram);

        $this->authorizeParticipant(
            $internshipProgram,
            $participant
        );

        $participant->load([
            'intern',
            'program',
        ]);

        return view(
            'company.internship-participants.show',
            compact(
                'internshipProgram',
                'participant'
            )
        );
    }

    /**
     * Menampilkan form edit participant.
     */
    public function edit(
        InternshipProgram $internshipProgram,
        InternshipParticipant $participant
    ) {
        $this->authorizeCompany($internshipProgram);

        $this->authorizeParticipant(
            $internshipProgram,
            $participant
        );

        return view(
            'company.internship-participants.edit',
            compact(
                'internshipProgram',
                'participant'
            )
        );
    }

    /**
     * Memperbarui data participant.
     */
    public function update(
        Request $request,
        InternshipProgram $internshipProgram,
        InternshipParticipant $participant
    ) {
        $this->authorizeCompany($internshipProgram);

        $this->authorizeParticipant(
            $internshipProgram,
            $participant
        );

        $validated = $request->validate([
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

        $participant->update($validated);

        return redirect()
            ->route(
                'company.internship-programs.participants.show',
                [
                    'internshipProgram' => $internshipProgram,
                    'participant' => $participant,
                ]
            )
            ->with(
                'success',
                'Data participant berhasil diperbarui.'
            );
    }

    /**
     * Mengeluarkan participant dari program.
     *
     * Tidak menggunakan delete.
     * removed_at digunakan sebagai penanda bahwa
     * participant sudah keluar dari program.
     */
    public function destroy(
        InternshipProgram $internshipProgram,
        InternshipParticipant $participant
    ) {
        $this->authorizeCompany($internshipProgram);

        $this->authorizeParticipant(
            $internshipProgram,
            $participant
        );

        abort_if(
            $participant->removed_at !== null,
            422,
            'Participant ini sudah dikeluarkan dari program.'
        );

        $participant->update([
            'removed_at' => now(),
        ]);

        return redirect()
            ->route(
                'company.internship-programs.participants.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Participant berhasil dikeluarkan dari program.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    /**
     * Memastikan program memang milik company yang sedang login.
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
     * Memastikan participant memang milik program tersebut.
     */
    protected function authorizeParticipant(
        InternshipProgram $internshipProgram,
        InternshipParticipant $participant
    ): void {
        abort_unless(
            $participant->program_id === $internshipProgram->id,
            404
        );
    }
}