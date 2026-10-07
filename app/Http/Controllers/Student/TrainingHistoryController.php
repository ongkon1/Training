<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrainingHistoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $student = $request->user();
        $attempts = $this->historyFor($student);

        return view('student.training-history', [
            'student' => $student,
            'attempts' => $attempts,
            'historyResults' => Result::query()
                ->where('student_id', $student->id)
                ->whereIn('id', $attempts->getCollection()->pluck('result_id')->filter())
                ->get()->keyBy('id'),
        ]);
    }

    /** Include callback records and call attempts that have not yet been reconciled. */
    protected function historyFor(User $student): LengthAwarePaginator
    {
        $transcripts = DB::table('exam_transcripts')
            ->where('student_id', $student->id)
            ->selectRaw("id, 'transcript' as source, created_at as attempted_at, subject, status, result_id");

        $sessions = DB::table('call_sessions')
            ->where('student_id', $student->id)
            // Provider reconciliation already represents these sessions as transcripts,
            // even when the browser's SIP id differs from the provider's call id.
            ->whereNull('matched_at')
            ->whereNotExists(function ($query) use ($student) {
                $query->selectRaw('1')->from('exam_transcripts')
                    ->where('exam_transcripts.student_id', $student->id)
                    ->whereColumn('exam_transcripts.external_id', 'call_sessions.call_id');
            })
            ->selectRaw("id, 'session' as source, COALESCE(started_at, created_at) as attempted_at, subject, 'awaiting_result' as status, NULL as result_id");

        return DB::query()->fromSub($transcripts->unionAll($sessions), 'training_attempts')
            ->orderByDesc('attempted_at')->orderByDesc('id')->orderBy('source')
            ->paginate(10)->withQueryString();
    }
}
