<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Intern;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates.
     */
    public function index()
    {
        $certificates = Certificate::with([
            'intern.user',
            'program',
        ])
            ->latest()
            ->get();

        return view(
            'root.certificates.index',
            compact('certificates')
        );
    }

    /**
     * Show the form for creating a new certificate.
     */
    public function create()
    {
        $interns = Intern::with('user')
            ->orderBy('id')
            ->get();

        $programs = InternshipProgram::orderBy('id')
            ->get();

        return view(
            'root.certificates.create',
            compact(
                'interns',
                'programs'
            )
        );
    }

    /**
     * Store a newly created certificate.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'program_id' => [
                'required',
                'exists:internship_programs,id',
                Rule::unique('certificates')
                    ->where(function ($query) use ($request) {
                        return $query
                            ->where('intern_id', $request->intern_id);
                    }),
            ],

            'file_path' => [
                'required',
                'string',
                'max:255',
            ],

            'generated_at' => [
                'nullable',
                'date',
            ],
        ]);

        Certificate::create($validated);

        return redirect()
            ->route('root.certificates.index')
            ->with(
                'success',
                'Certificate berhasil dibuat.'
            );
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load([
            'intern.user',
            'program',
        ]);

        return view(
            'root.certificates.show',
            compact('certificate')
        );
    }

    /**
     * Show the form for editing the specified certificate.
     */
    public function edit(Certificate $certificate)
    {
        $interns = Intern::with('user')
            ->orderBy('id')
            ->get();

        $programs = InternshipProgram::orderBy('id')
            ->get();

        return view(
            'root.certificates.edit',
            compact(
                'certificate',
                'interns',
                'programs'
            )
        );
    }

    /**
     * Update the specified certificate.
     */
    public function update(
        Request $request,
        Certificate $certificate
    ) {
        $validated = $request->validate([
            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'program_id' => [
                'required',
                'exists:internship_programs,id',
                Rule::unique('certificates')
                    ->where(function ($query) use ($request) {
                        return $query
                            ->where('intern_id', $request->intern_id);
                    })
                    ->ignore($certificate->id),
            ],

            'file_path' => [
                'required',
                'string',
                'max:255',
            ],

            'generated_at' => [
                'nullable',
                'date',
            ],
        ]);

        $certificate->update($validated);

        return redirect()
            ->route('root.certificates.index')
            ->with(
                'success',
                'Certificate berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified certificate.
     */
    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()
            ->route('root.certificates.index')
            ->with(
                'success',
                'Certificate berhasil dihapus.'
            );
    }
}