<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\InternshipProgram;
use App\Models\InternshipProgramBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InternshipProgramBannerController extends Controller
{
    /**
     * Display a listing of internship program banners.
     */
    public function index(): View
    {
        $banners = InternshipProgramBanner::with('program')
            ->orderBy('order')
            ->latest()
            ->paginate(10);

        return view(
            'root.internship-program-banners.index',
            compact('banners')
        );
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create(): View
    {
        $programs = InternshipProgram::orderBy('title')->get();

        return view(
            'root.internship-program-banners.create',
            compact('programs')
        );
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $path = $request->file('image')
            ->store('internship-program-banners', 'public');

        InternshipProgramBanner::create([
            'program_id' => $validated['program_id'],
            'image_path' => $path,
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()
            ->route('root.internship-program-banners.index')
            ->with(
                'success',
                'Banner program magang berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified banner.
     */
    public function show(
        InternshipProgramBanner $internshipProgramBanner
    ): View {

        $internshipProgramBanner->load('program');

        return view(
            'root.internship-program-banners.show',
            compact('internshipProgramBanner')
        );
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(
        InternshipProgramBanner $internshipProgramBanner
    ): View {

        $programs = InternshipProgram::orderBy('title')->get();

        return view(
            'root.internship-program-banners.edit',
            compact(
                'internshipProgramBanner',
                'programs'
            )
        );
    }

    /**
     * Update the specified banner.
     */
    public function update(
        Request $request,
        InternshipProgramBanner $internshipProgramBanner
    ): RedirectResponse {

        $validated = $request->validate([
            'program_id' => [
                'required',
                'exists:internship_programs,id',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $data = [
            'program_id' => $validated['program_id'],
            'order' => $validated['order'] ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | UPDATE IMAGE
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            if (
                $internshipProgramBanner->image_path &&
                Storage::disk('public')->exists(
                    $internshipProgramBanner->image_path
                )
            ) {
                Storage::disk('public')->delete(
                    $internshipProgramBanner->image_path
                );
            }

            $data['image_path'] =
                $request->file('image')
                    ->store(
                        'internship-program-banners',
                        'public'
                    );
        }

        $internshipProgramBanner->update($data);

        return redirect()
            ->route('root.internship-program-banners.index')
            ->with(
                'success',
                'Banner program magang berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(
        InternshipProgramBanner $internshipProgramBanner
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | DELETE IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            $internshipProgramBanner->image_path &&
            Storage::disk('public')->exists(
                $internshipProgramBanner->image_path
            )
        ) {
            Storage::disk('public')->delete(
                $internshipProgramBanner->image_path
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SOFT DELETE
        |--------------------------------------------------------------------------
        */

        $internshipProgramBanner->delete();

        return redirect()
            ->route('root.internship-program-banners.index')
            ->with(
                'success',
                'Banner program magang berhasil dihapus.'
            );
    }
}