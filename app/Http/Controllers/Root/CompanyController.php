<?php

namespace App\Http\Controllers\Root;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Menampilkan semua company.
     */
    public function index()
    {
        $companies = Company::with('user')
            ->latest()
            ->paginate(10);

        return view('root.companies.index', compact('companies'));
    }

    /**
     * Form membuat company baru.
     */
    public function create()
    {
        return view('root.companies.create');
    }

    /**
     * Root membuat akun user + profile company.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'company_name' => [
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

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | 1. Buat akun User
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => UserRole::COMPANY,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. Buat profile Company
            |--------------------------------------------------------------------------
            */

            Company::create([
                'user_id' => $user->id,

                'slug' => Str::slug(
                    $validated['company_name']
                ) . '-' . Str::random(5),

                'name' => $validated['company_name'],

                'short_description' =>
                    $validated['short_description'] ?? null,

                'description' =>
                    $validated['description'] ?? null,

                'whatsapp' =>
                    $validated['whatsapp'] ?? null,

                'contact_email' =>
                    $validated['contact_email'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'gmap_embed' =>
                    $validated['gmap_embed'] ?? null,
            ]);
        });

        return redirect()
            ->route('root.companies.index')
            ->with(
                'success',
                'Akun perusahaan berhasil dibuat.'
            );
    }

    /**
     * Menampilkan detail company.
     */
    public function show(Company $company)
    {
        $company->load([
            'user',
            'internshipPrograms',
            'works',
        ]);

        return view(
            'root.companies.show',
            compact('company')
        );
    }

    /**
     * Form edit company.
     */
    public function edit(Company $company)
    {
        $company->load('user');

        return view(
            'root.companies.edit',
            compact('company')
        );
    }

    /**
     * Update company.
     */
    public function update(
        Request $request,
        Company $company
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $company->user_id,
            ],

            'company_name' => [
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

        DB::transaction(function () use (
            $validated,
            $company
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update User
            |--------------------------------------------------------------------------
            */

            $company->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Company
            |--------------------------------------------------------------------------
            */

            $company->update([
                'name' => $validated['company_name'],

                'short_description' =>
                    $validated['short_description'] ?? null,

                'description' =>
                    $validated['description'] ?? null,

                'whatsapp' =>
                    $validated['whatsapp'] ?? null,

                'contact_email' =>
                    $validated['contact_email'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'gmap_embed' =>
                    $validated['gmap_embed'] ?? null,
            ]);
        });

        return redirect()
            ->route('root.companies.index')
            ->with(
                'success',
                'Data perusahaan berhasil diperbarui.'
            );
    }

    /**
     * Soft delete company.
     */
    public function destroy(Company $company)
    {
        DB::transaction(function () use ($company) {

            /*
            |--------------------------------------------------------------------------
            | Soft delete company
            |--------------------------------------------------------------------------
            */

            $company->delete();

            /*
            |--------------------------------------------------------------------------
            | Soft delete user
            |--------------------------------------------------------------------------
            */

            $company->user->delete();
        });

        return redirect()
            ->route('root.companies.index')
            ->with(
                'success',
                'Perusahaan berhasil dihapus.'
            );
    }
}