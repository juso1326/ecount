<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        
        $tags = Tag::query()
            ->when($type, fn($q) => $q->ofType($type))
            ->ordered()
            ->get();

        $types = Tag::getTypes();

        return view('tenant.tags.index', compact('tags', 'types', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $types = Tag::getTypes();
        $selectedType = $request->get('type');
        
        return view('tenant.tags.create', compact('types', 'selectedType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Tag::getTypes())),
            'name' => 'required|string|max:100',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'type.required' => '請選擇標籤類型',
            'name.required' => '請輸入標籤名稱',
            'color.regex' => '顏色格式錯誤，請使用 Hex 格式（如：#3B82F6）',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Tag::create($validated);

        return redirect()
            ->route('tenant.tags.index', ['type' => $validated['type']])
            ->with('success', '標籤已新增');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        $tag->load(['projects', 'companies', 'users']);
        $types = Tag::getTypes();
        return view('tenant.tags.show', compact('tag', 'types'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        $types = Tag::getTypes();
        
        return view('tenant.tags.edit', compact('tag', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Tag::getTypes())),
            'name' => 'required|string|max:100',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? $tag->sort_order;

        $tag->update($validated);

        return redirect()
            ->route('tenant.tags.index', ['type' => $validated['type']])
            ->with('success', '標籤已更新');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $type = $tag->type;
        $tag->delete();

        return redirect()
            ->route('tenant.tags.index', ['type' => $type])
            ->with('success', '標籤已刪除');
    }
}
