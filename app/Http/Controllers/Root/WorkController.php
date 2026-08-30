<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkController extends Controller
{
    public function index()
    {
        $works = Work::with('company')
            ->latest()
            ->paginate(10);

        return view(
            'root.works.index',
            compact('works')
        );
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();

        return view(
            'root.works.create',
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

            'short_description' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        Work::create([
            'company_id' => $validated['company_id'],

            'slug' => Str::slug($validated['title'])
                . '-' . Str::random(5),

            'title' => $validated['title'],

            'short_description' =>
                $validated['short_description'] ?? null,

            'description' =>
                $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('root.works.index')
            ->with(
                'success',
                'Karya berhasil dibuat.'
            );
    }

    public function show(Work $work)
    {
        $work->load([
            'company',
            'photos',
            'members.intern.user',
        ]);

        return view(
            'root.works.show',
            compact('work')
        );
    }

    public function edit(Work $work)
    {
        $companies = Company::orderBy('name')->get();

        return view(
            'root.works.edit',
            compact('work', 'companies')
        );
    }

    public function update(
        Request $request,
        Work $work
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

            'short_description' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $work->update([
            'company_id' => $validated['company_id'],
            'title' => $validated['title'],

            'short_description' =>
                $validated['short_description'] ?? null,

            'description' =>
                $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('root.works.index')
            ->with(
                'success',
                'Karya berhasil diperbarui.'
            );
    }

    public function destroy(Work $work)
    {
        $work->delete();

        return redirect()
            ->route('root.works.index')
            ->with(
                'success',
                'Karya berhasil dihapus.'
            );
    }
}