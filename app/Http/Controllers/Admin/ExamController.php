<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with('creator')->withCount(['questions', 'attempts']);

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $exams = $query->latest()->paginate(15);

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0|lte:total_marks',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:draft,published',
            'max_attempts' => 'required|integer|min:1',
        ]);

        $exam = Exam::create([
            'created_by' => auth('admin')->id(),
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'total_marks' => $request->total_marks,
            'passing_marks' => $request->passing_marks,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'show_results' => $request->boolean('show_results'),
            'face_verification_required' => $request->boolean('face_verification_required'),
            'max_attempts' => $request->max_attempts,
        ]);

        ActivityLog::log('exam_created', "Created exam: {$exam->title}", $exam);

        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Exam created. Now add questions.');
    }

    public function show(Exam $exam)
    {
        $exam->load(['questions', 'attempts.user', 'creator']);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0|lte:total_marks',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:draft,published,completed,archived',
            'max_attempts' => 'required|integer|min:1',
        ]);

        $oldValues = $exam->toArray();

        $exam->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'total_marks' => $request->total_marks,
            'passing_marks' => $request->passing_marks,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'show_results' => $request->boolean('show_results'),
            'face_verification_required' => $request->boolean('face_verification_required'),
            'max_attempts' => $request->max_attempts,
        ]);

        ActivityLog::log('exam_updated', "Updated exam: {$exam->title}", $exam, $oldValues, $exam->toArray());

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        ActivityLog::log('exam_deleted', "Deleted exam: {$exam->title}", $exam);
        $exam->delete();

        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function results(Exam $exam)
    {
        $attempts = $exam->attempts()
            ->with('user')
            ->where('status', 'completed')
            ->orderByDesc('percentage')
            ->paginate(20);

        return view('admin.exams.results', compact('exam', 'attempts'));
    }
}
