<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;

class ReportController extends Controller
{
    private function getReportableType(string $type, int $id)
    {

        return match ($type) {
            'post' => Post::find($id),
            'comment' => Comment::find($id),
            'user' => User::find($id),
            default => null
        };
    }

    public function store(ReportRequest $request, $reportable_type, $reportable_id)
    {
        $reportable = $this->getReportableType($reportable_type, $reportable_id);
        if (! $reportable) {
            return $this->sendError('reportable type or id not found', 404);
        }
        if ($reportable->reports()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => ucfirst($request->reportable_type) . ' already reported'], 422);
        }
        $validated = $request->validated();
        $report = $reportable->reports()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);


        return $this->sendResponse(
            [
                'report' => new ReportResource($report)
            ],
            class_basename($report->reportable_type) . ' has been reported successfully',
            200
        );
    }

    public function index(Report $report)
    {
        $this->authorize('index', $report);

        $reports = Report::with(['reportable', 'reporter'])
            ->latest()
            ->paginate(20);

        return $this->sendResponse([
            'reports' => ReportResource::collection($reports),
            'pagination' => [
                'total' => $reports->total(),
                'per_page' => $reports->perPage(),
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
            ],
        ], 'Reports retrieved successfully');
    }

    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);
        $report->delete();

        return $this->sendResponse([], 'report destroyed');
    }
}
