<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Work;
use App\Models\WorkPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorkPhotoController extends Controller
{
    /**
     * Display a listing of work photos.
     */
    public function index(): View
    {
        $workPhotos = WorkPhoto::with('work')
            ->latest()
            ->paginate(10);

        return view('root.work-photos.index', compact('workPhotos'));
    }

    /**
     * Show the form for creating a new work photo.
     */
    public function create(): View
    {
        $works = Work::orderBy('title')->get();

        return view('root.work-photos.create', compact('works'));
    }

    /**
     * Store a newly created work photo.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'work_id' => [
                'required',
                'exists:works,id',
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
            ->store('work-photos', 'public');

        WorkPhoto::create([
            'work_id' => $validated['work_id'],
            'image_path' => $path,
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()
            ->route('root.work-photos.index')
            ->with('success', 'Foto work berhasil ditambahkan.');
    }

    /**
     * Display the specified work photo.
     */
    public function show(WorkPhoto $workPhoto): View
    {
        $workPhoto->load('work');

        return view(
            'root.work-photos.show',
            compact('workPhoto')
        );
    }

    /**
     * Show the form for editing the specified work photo.
     */
    public function edit(WorkPhoto $workPhoto): View
    {
        $works = Work::orderBy('title')->get();

        return view(
            'root.work-photos.edit',
            compact('workPhoto', 'works')
        );
    }

    /**
     * Update the specified work photo.
     */
    public function update(
        Request $request,
        WorkPhoto $workPhoto
    ): RedirectResponse {

        $validated = $request->validate([
            'work_id' => [
                'required',
                'exists:works,id',
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
            'work_id' => $validated['work_id'],
            'order' => $validated['order'] ?? 0,
        ];

        if ($request->hasFile('image')) {

            if (
                $workPhoto->image_path &&
                Storage::disk('public')->exists(
                    $workPhoto->image_path
                )
            ) {
                Storage::disk('public')->delete(
                    $workPhoto->image_path
                );
            }

            $data['image_path'] =
                $request->file('image')
                    ->store('work-photos', 'public');
        }

        $workPhoto->update($data);

        return redirect()
            ->route('root.work-photos.index')
            ->with('success', 'Foto work berhasil diperbarui.');
    }

    /**
     * Remove the specified work photo.
     */
    public function destroy(
        WorkPhoto $workPhoto
    ): RedirectResponse {

        if (
            $workPhoto->image_path &&
            Storage::disk('public')->exists(
                $workPhoto->image_path
            )
        ) {
            Storage::disk('public')->delete(
                $workPhoto->image_path
            );
        }

        $workPhoto->delete();

        return redirect()
            ->route('root.work-photos.index')
            ->with('success', 'Foto work berhasil dihapus.');
    }
}