<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InternshipProgramController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $company = auth()->user()->company;

        abort_unless($company, 403);

        $programs = $company->internshipPrograms()
            ->latest()
            ->get();

        return view(
            'company.internship-programs.index',
            compact('programs')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        abort_unless(
            auth()->user()->company,
            403
        );

        return view(
            'company.internship-programs.create'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        abort_unless($company, 403);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'short_description' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'registration_open_at' => [
                'required',
                'date',
            ],

            'registration_close_at' => [
                'required',
                'date',
                'after_or_equal:registration_open_at',
            ],

            'program_start_at' => [
                'required',
                'date',
                'after_or_equal:registration_close_at',
            ],

            'program_end_at' => [
                'required',
                'date',
                'after_or_equal:program_start_at',
            ],
        ]);

        // Company otomatis diambil dari user yang login
        $validated['company_id'] = $company->id;

        // Generate slug otomatis
        $validated['slug'] = $this->generateUniqueSlug(
            $validated['title']
        );

        InternshipProgram::create($validated);

        return redirect()
            ->route('company.internship-programs.index')
            ->with(
                'success',
                'Program magang berhasil dibuat.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        InternshipProgram $internshipProgram
    ) {
        $this->authorizeCompany(
            $internshipProgram
        );

        $internshipProgram->load([
            'company',
            'banners',
            'positions',
            'registrations',
            'participants',
            'certificates',
        ]);

        return view(
            'company.internship-programs.show',
            compact('internshipProgram')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        InternshipProgram $internshipProgram
    ) {
        $this->authorizeCompany(
            $internshipProgram
        );

        return view(
            'company.internship-programs.edit',
            compact('internshipProgram')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        InternshipProgram $internshipProgram
    ) {
        $this->authorizeCompany(
            $internshipProgram
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'short_description' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'registration_open_at' => [
                'required',
                'date',
            ],

            'registration_close_at' => [
                'required',
                'date',
                'after_or_equal:registration_open_at',
            ],

            'program_start_at' => [
                'required',
                'date',
                'after_or_equal:registration_close_at',
            ],

            'program_end_at' => [
                'required',
                'date',
                'after_or_equal:program_start_at',
            ],
        ]);

        // Generate slug baru jika title berubah
        if (
            $internshipProgram->title !==
            $validated['title']
        ) {
            $validated['slug'] =
                $this->generateUniqueSlug(
                    $validated['title'],
                    $internshipProgram->id
                );
        }

        $internshipProgram->update(
            $validated
        );

        return redirect()
            ->route(
                'company.internship-programs.index'
            )
            ->with(
                'success',
                'Program magang berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        InternshipProgram $internshipProgram
    ) {
        $this->authorizeCompany(
            $internshipProgram
        );

        $internshipProgram->delete();

        return redirect()
            ->route(
                'company.internship-programs.index'
            )
            ->with(
                'success',
                'Program magang berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHORIZE COMPANY
    |--------------------------------------------------------------------------
    */

    protected function authorizeCompany(
        InternshipProgram $internshipProgram
    ): void {
        $company = auth()->user()->company;

        abort_unless(
            $company &&
            $internshipProgram->company_id ===
                $company->id,
            403
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE UNIQUE SLUG
    |--------------------------------------------------------------------------
    */

    protected function generateUniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $slug = Str::slug($title);

        $originalSlug = $slug;
        $counter = 1;

        while (
            InternshipProgram::where(
                'slug',
                $slug
            )
                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )
                ->exists()
        ) {
            $slug =
                $originalSlug .
                '-' .
                $counter;

            $counter++;
        }

        return $slug;
    }
}