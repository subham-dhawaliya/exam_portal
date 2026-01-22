<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::withCount('questions')
            ->ordered()
            ->get();
        
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.sections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sections',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['order'] = Section::max('order') + 1;
        $validated['is_active'] = $request->boolean('is_active', true);

        Section::create($validated);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section created successfully!');
    }

    public function edit(Section $section)
    {
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sections,name,' . $section->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $section->update($validated);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section updated successfully!');
    }

    public function destroy(Section $section)
    {
        if ($section->questions()->count() > 0) {
            return back()->with('error', 'Cannot delete section with questions. Move or delete questions first.');
        }

        $section->delete();

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section deleted successfully!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*' => 'exists:sections,id',
        ]);

        foreach ($request->sections as $index => $id) {
            Section::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleStatus(Section $section)
    {
        $section->update(['is_active' => !$section->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $section->is_active,
        ]);
    }
}
