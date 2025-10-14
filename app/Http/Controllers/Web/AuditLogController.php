<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $q = Activity::query()->with(['causer', 'subject'])->latest();

        if ($request->filled('event')) {
            $q->where('description', $request->string('event'));
        }
        if ($request->filled('causer')) {
            $q->whereHas('causer', function ($qq) use ($request) {
                $term = '%'.$request->string('causer').'%';
                $qq->where('email', 'like', $term)->orWhere('name', 'like', $term);
            });
        }
        if ($request->filled('subject_type')) {
            $q->where('subject_type', $request->string('subject_type'));
        }

        $logs = $q->paginate(25)->withQueryString();

        // subject type-ok gyors listához
        $subjectTypes = Activity::query()
            ->select('subject_type')
            ->whereNotNull('subject_type')
            ->distinct()
            ->pluck('subject_type');

        return view('admin.audit.index', [
            'logs' => $logs,
            'subjectTypes' => $subjectTypes,
        ]);
    }
}
