<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\InternshipProgram;
use App\Models\InternshipProgramBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InternshipProgramBannerController extends Controller
{
    /**
     * Menampilkan semua banner dari program tertentu.
     */
    public function index(InternshipProgram $internshipProgram)
    {
        $this->authorizeCompany($internshipProgram);

        $banners = $internshipProgram->banners()
            ->orderBy('order')
            ->get();

        return view(
            'company.internship-program-banners.index',
            compact('internshipProgram', 'banners')
        );
    }

    /**
     * Menampilkan form tambah banner.
     */
    public function create(InternshipProgram $internshipProgram)
    {
        $this->authorizeCompany($internshipProgram);

        return view(
            'company.internship-program-banners.create',
            compact('internshipProgram')
        );
    }

    /**
     * Menyimpan banner baru.
     */
    public function store(
        Request $request,
        InternshipProgram $internshipProgram
    ) {
        $this->authorizeCompany($internshipProgram);

        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $path = $request->file('image')->store(
            'internship-program-banners',
            'public'
        );

        $internshipProgram->banners()->create([
            'image_path' => $path,
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()
            ->route(
                'company.internship-programs.banners.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Banner program magang berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail banner.
     */
    public function show(
        InternshipProgram $internshipProgram,
        InternshipProgramBanner $banner
    ) {
        $this->authorizeCompany($internshipProgram);
        $this->authorizeBanner($internshipProgram, $banner);

        return view(
            'company.internship-program-banners.show',
            compact('internshipProgram', 'banner')
        );
    }

    /**
     * Menampilkan form edit banner.
     */
    public function edit(
        InternshipProgram $internshipProgram,
        InternshipProgramBanner $banner
    ) {
        $this->authorizeCompany($internshipProgram);
        $this->authorizeBanner($internshipProgram, $banner);

        return view(
            'company.internship-program-banners.edit',
            compact('internshipProgram', 'banner')
        );
    }

    /**
     * Memperbarui banner.
     */
    public function update(
        Request $request,
        InternshipProgram $internshipProgram,
        InternshipProgramBanner $banner
    ) {
        $this->authorizeCompany($internshipProgram);
        $this->authorizeBanner($internshipProgram, $banner);

        $validated = $request->validate([
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete(
                    $banner->image_path
                );
            }

            $banner->image_path = $request
                ->file('image')
                ->store(
                    'internship-program-banners',
                    'public'
                );
        }

        $banner->order = $validated['order'] ?? 0;

        $banner->save();

        return redirect()
            ->route(
                'company.internship-programs.banners.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Banner program magang berhasil diperbarui.'
            );
    }

    /**
     * Menghapus banner.
     */
    public function destroy(
        InternshipProgram $internshipProgram,
        InternshipProgramBanner $banner
    ) {
        $this->authorizeCompany($internshipProgram);
        $this->authorizeBanner($internshipProgram, $banner);

        if ($banner->image_path) {
            Storage::disk('public')->delete(
                $banner->image_path
            );
        }

        $banner->delete();

        return redirect()
            ->route(
                'company.internship-programs.banners.index',
                $internshipProgram
            )
            ->with(
                'success',
                'Banner program magang berhasil dihapus.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
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

    protected function authorizeBanner(
        InternshipProgram $internshipProgram,
        InternshipProgramBanner $banner
    ): void {
        abort_unless(
            $banner->program_id === $internshipProgram->id,
            404
        );
    }
}