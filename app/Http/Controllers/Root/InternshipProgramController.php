<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\InternshipProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InternshipProgramController extends Controller
{
    public function index()
    {
        $programs = InternshipProgram::with('company')
            ->latest()
            ->paginate(10);

        return view(
            'root.internship-programs.index',
            compact('programs')
        );
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();

        return view(
            'root.internship-programs.create',
            compact('companies')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => [
                'required',
                'exists:companies,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            'registration_open_at' => ['required', 'date'],
            'registration_close_at' => [
                'required',
                'date',
                'after_or_equal:registration_open_at',
            ],

            'program_start_at' => ['required', 'date'],
            'program_end_at' => [
                'required',
                'date',
                'after_or_equal:program_start_at',
            ],
        ]);

        $program = InternshipProgram::create([
            'company_id' => $validated['company_id'],

            'slug' => Str::slug($validated['title'])
                . '-' . Str::random(5),

            'title' => $validated['title'],

            'short_description' =>
                $validated['short_description'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'registration_open_at' =>
                $validated['registration_open_at'],

            'registration_close_at' =>
                $validated['registration_close_at'],

            'program_start_at' =>
                $validated['program_start_at'],

            'program_end_at' =>
                $validated['program_end_at'],
        ]);

        return redirect()
            ->route('root.internship-programs.index')
            ->with(
                'success',
                'Program magang berhasil dibuat.'
            );
    }

    public function show(InternshipProgram $internshipProgram)
    {
        $internshipProgram->load([
            'company',
            'banners',
            'positions',
            'registrations.intern.user',
            'participants.intern.user',
            'certificates.intern.user',
        ]);

        return view(
            'root.internship-programs.show',
            compact('internshipProgram')
        );
    }

    public function edit(InternshipProgram $internshipProgram)
    {
        $companies = Company::orderBy('name')->get();

        return view(
            'root.internship-programs.edit',
            compact(
                'internshipProgram',
                'companies'
            )
        );
    }

    public function update(
        Request $request,
        InternshipProgram $internshipProgram
    ) {
        $validated = $request->validate([
            'company_id' => [
                'required',
                'exists:companies,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            'registration_open_at' => ['required', 'date'],

            'registration_close_at' => [
                'required',
                'date',
                'after_or_equal:registration_open_at',
            ],

            'program_start_at' => ['required', 'date'],

            'program_end_at' => [
                'required',
                'date',
                'after_or_equal:program_start_at',
            ],
        ]);

        $internshipProgram->update([
            'company_id' => $validated['company_id'],
            'title' => $validated['title'],

            'short_description' =>
                $validated['short_description'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'registration_open_at' =>
                $validated['registration_open_at'],

            'registration_close_at' =>
                $validated['registration_close_at'],

            'program_start_at' =>
                $validated['program_start_at'],

            'program_end_at' =>
                $validated['program_end_at'],
        ]);

        return redirect()
            ->route('root.internship-programs.index')
            ->with(
                'success',
                'Program magang berhasil diperbarui.'
            );
    }

    public function destroy(
        InternshipProgram $internshipProgram
    ) {
        $internshipProgram->delete();

        return redirect()
            ->route('root.internship-programs.index')
            ->with(
                'success',
                'Program magang berhasil dihapus.'
            );
    }
}