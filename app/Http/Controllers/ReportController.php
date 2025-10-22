<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use App\Models\Comment;
use App\Models\Post;

class ReportController extends Controller
{
    public function storeReport(ReportRequest $request)
    {
        $reportable = match($request->reportable_type) {
            'post' => Post::find($request->reportable_id),
            'comment' => Comment::find($request->reportable_id),
            'user' => User::find($request->reportable_id),
            default => null
        };
        if (!$reportable) {
            return response()->json([
                'message' => 'Reportable type or id not found'
            ], 404);
        }
        if ($reportable->reports()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => ucfirst($request->reportable_type).' already reported'], 422);
        }
        $validated = $request->validated();
        $report = $reportable->reports()->create([
            ...$validated,
            'user_id' => auth()->id()
        ]);
        return response()->json([
           'message' => ucfirst($request->reportable_type) . " has been reported successfully",            'report' => $report
        ]);
    }

    public function index(Report $report)
    {

        $this->authorize('index', $report);
        $reports = Report::with(['reportable', 'reporter'])
            ->latest()
            ->paginate(20);
        return response()->json($reports);
    }

    public function deleteReport(Report $report)
    {
        $this->authorize('delete', $report);
        $report->delete();
        return response()->json([
            'message' => 'report has been deleted successfully'
        ]);
    }

}
