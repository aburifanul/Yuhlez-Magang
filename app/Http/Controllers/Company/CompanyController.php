<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Menampilkan profil company yang sedang login.
     */
    public function index(Request $request)
    {
        $company = $request->user()->company;

        abort_if(!$company, 404, 'Data company tidak ditemukan.');

        return view('company.company.index', compact('company'));
    }

    /**
     * Menampilkan form edit profil company.
     */
    public function edit(Request $request)
    {
        $company = $request->user()->company;

        abort_if(!$company, 404, 'Data company tidak ditemukan.');

        return view('company.company.edit', compact('company'));
    }

    /**
     * Memperbarui profil company.
     */
    public function update(Request $request)
    {
        $company = $request->user()->company;

        abort_if(!$company, 404, 'Data company tidak ditemukan.');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:companies,slug,' . $company->id,
            ],

            'short_description' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'logo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:30',
            ],

            'contact_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'gmap_embed' => [
                'nullable',
                'string',
            ],
        ]);

        $company->update($validated);

        return redirect()
            ->route('company.company.index')
            ->with(
                'success',
                'Profil company berhasil diperbarui.'
            );
    }
}