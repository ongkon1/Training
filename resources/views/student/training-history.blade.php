@extends('layouts.app')

@section('title', 'Training History')
@section('heading', 'Training History')
@section('subheading', 'Review your language training attempts and scores.')

@push('styles')
    <link href="{{ asset('asset/css/voice-training.css') }}?v={{ filemtime(public_path('asset/css/voice-training.css')) }}" rel="stylesheet">
@endpush

@section('actions')
    <a class="btn btn-primary" href="{{ route('student.voice-exam') }}">
        <i class="bi bi-mic me-2" aria-hidden="true"></i>Language Training
    </a>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-12 mx-auto training-history-shell">
            <section class="card training-history" aria-labelledby="training-history-title">
                <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <div>
                        <strong id="training-history-title">Training History</strong>
                        <p class="text-muted small mb-0 mt-1">Your language training attempts and scores out of 10.</p>
                    </div>
                    <div class="training-attempt-count">
                        <i class="bi bi-clock-history" aria-hidden="true"></i>
                        <span>Total attempts: <strong>{{ $attempts->total() }}</strong></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">Attempt</th>
                            <th scope="col">Recorded</th>
                            <th scope="col">Training</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Score / 10</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($attempts as $attempt)
                            @php
                                $result = $historyResults->get($attempt->result_id);
                                $scored = $attempt->status === \App\Models\ExamTranscript::STATUS_EVALUATED && $result;
                                $statusLabel = match ($attempt->status) {
                                    'evaluated' => 'Completed',
                                    'pending' => 'Processing',
                                    'failed' => 'Evaluation failed',
                                    'unmatched' => 'Awaiting matching',
                                    default => 'Awaiting result',
                                };
                                $statusVariant = match ($attempt->status) {
                                    'evaluated' => 'success',
                                    'failed' => 'danger',
                                    'unmatched' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td class="fw-semibold">#{{ $attempts->total() - $attempts->firstItem() - $loop->index + 1 }}</td>
                                <td>
                                    <time datetime="{{ \Illuminate\Support\Carbon::parse($attempt->attempted_at)->toIso8601String() }}">
                                        {{ \Illuminate\Support\Carbon::parse($attempt->attempted_at)->format('d M Y, g:i A') }}
                                    </time>
                                </td>
                                <td>{{ $attempt->subject ?: 'Language Training' }}</td>
                                <td><span class="badge bg-{{ $statusVariant }}">{{ $statusLabel }}</span></td>
                                <td class="text-end text-nowrap">
                                    @if ($scored)
                                        <a href="{{ route('student.results.show', $result) }}" class="training-history-score"
                                           aria-label="View result for attempt {{ $attempts->total() - $attempts->firstItem() - $loop->index + 1 }}, score {{ number_format($result->percentage / 10, 2) }} out of 10">
                                            {{ number_format($result->percentage / 10, 2) }} / 10
                                        </a>
                                    @else
                                        <span class="text-muted small">Not scored yet</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-clock-history fs-3 text-muted d-block mb-2" aria-hidden="true"></i>
                                    <strong>No training attempts yet</strong>
                                    <p class="text-muted small mt-1 mb-0">Your attempts and scores will appear here after you start training.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($attempts->hasPages())
                    <div class="card-footer">{{ $attempts->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
