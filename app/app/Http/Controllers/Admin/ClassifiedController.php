<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassifiedRequest;
use App\Http\Requests\Admin\UpdateClassifiedRequest;
use App\Models\Classified;
use App\Models\ClassifiedCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClassifiedController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Classified::class);

        $q = trim((string) $request->query('q', ''));

        $classifieds = Classified::query()
            ->with('category')
            ->when($q !== '', fn ($query) => $query->where('title', 'like', '%'.$q.'%'))
            ->latest('published_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.classifieds.index', compact('classifieds', 'q'));
    }

    public function create(): View
    {
        $this->authorize('create', Classified::class);

        return view('admin.classifieds.create', [
            'classified' => new Classified,
            'categories' => ClassifiedCategory::orderBy('name')->get(),
        ]);
    }

    public function store(StoreClassifiedRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['title']);

        $classified = Classified::create($data);

        return redirect()
            ->route('admin.classifieds.edit', $classified)
            ->with('success', 'Clasificado creado.');
    }

    public function show(Classified $classified): RedirectResponse
    {
        $this->authorize('view', $classified);

        return redirect()->route('admin.classifieds.edit', $classified);
    }

    public function edit(Classified $classified): View
    {
        $this->authorize('update', $classified);

        return view('admin.classifieds.edit', [
            'classified' => $classified,
            'categories' => ClassifiedCategory::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateClassifiedRequest $request, Classified $classified): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $classified->update($data);

        return redirect()
            ->route('admin.classifieds.edit', $classified)
            ->with('success', 'Clasificado actualizado.');
    }

    public function destroy(Classified $classified): RedirectResponse
    {
        $this->authorize('delete', $classified);

        $classified->delete();

        return redirect()
            ->route('admin.classifieds.index')
            ->with('success', 'Clasificado eliminado.');
    }
}
