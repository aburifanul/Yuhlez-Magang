<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\Work;
use App\Models\WorkMember;
use Illuminate\Http\Request;

class WorkMemberController extends Controller
{
    public function index()
    {
        $members = WorkMember::with([
            'work.company',
            'intern.user',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'root.work-members.index',
            compact('members')
        );
    }

    public function create()
    {
        $works = Work::with('company')
            ->latest()
            ->get();

        $interns = Intern::with('user')
            ->latest()
            ->get();

        return view(
            'root.work-members.create',
            compact('works', 'interns')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_id' => [
                'required',
                'exists:works,id',
            ],

            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'added_at' => [
                'nullable',
                'date',
            ],

            'removed_at' => [
                'nullable',
                'date',
                'after_or_equal:added_at',
            ],
        ]);

        WorkMember::create([
            'work_id' => $validated['work_id'],
            'intern_id' => $validated['intern_id'],
            'added_at' =>
                $validated['added_at'] ?? now(),
            'removed_at' =>
                $validated['removed_at'] ?? null,
        ]);

        return redirect()
            ->route('root.work-members.index')
            ->with(
                'success',
                'Member karya berhasil ditambahkan.'
            );
    }

    public function show(WorkMember $workMember)
    {
        $workMember->load([
            'work.company',
            'intern.user',
        ]);

        return view(
            'root.work-members.show',
            compact('workMember')
        );
    }

    public function edit(WorkMember $workMember)
    {
        $works = Work::with('company')
            ->latest()
            ->get();

        $interns = Intern::with('user')
            ->latest()
            ->get();

        return view(
            'root.work-members.edit',
            compact(
                'workMember',
                'works',
                'interns'
            )
        );
    }

    public function update(
        Request $request,
        WorkMember $workMember
    ) {
        $validated = $request->validate([
            'work_id' => [
                'required',
                'exists:works,id',
            ],

            'intern_id' => [
                'required',
                'exists:interns,id',
            ],

            'added_at' => [
                'required',
                'date',
            ],

            'removed_at' => [
                'nullable',
                'date',
                'after_or_equal:added_at',
            ],
        ]);

        $workMember->update($validated);

        return redirect()
            ->route('root.work-members.index')
            ->with(
                'success',
                'Member karya berhasil diperbarui.'
            );
    }

    public function destroy(WorkMember $workMember)
    {
        $workMember->delete();

        return redirect()
            ->route('root.work-members.index')
            ->with(
                'success',
                'Member karya berhasil dihapus.'
            );
    }
}