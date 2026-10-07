<?php

namespace Tests\Feature;

use App\Models\CallSession;
use App\Models\ExamTranscript;
use App\Models\Result;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTrainingHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_normalizes_scores_and_keeps_stored_marks_unchanged(): void
    {
        $student = User::factory()->student()->create();
        $result = Result::factory()->create([
            'student_id' => $student->id, 'full_marks' => 40, 'marks_obtained' => 35,
        ]);
        ExamTranscript::factory()->create([
            'student_id' => $student->id, 'result_id' => $result->id,
            'status' => ExamTranscript::STATUS_EVALUATED,
        ]);

        $this->actingAs($student)->get(route('student.training-history'))
            ->assertOk()->assertSee('Training History')->assertSee('8.75 / 10')
            ->assertSee(route('student.results.show', $result), false)
            ->assertViewHas('attempts', fn ($rows) => $rows->total() === 1);

        $this->assertSame('35.00', $result->fresh()->marks_obtained);
        $this->assertSame(40, $result->fresh()->full_marks);
    }

    public function test_pending_sessions_and_failed_evaluations_are_counted_without_invented_scores(): void
    {
        $student = User::factory()->student()->create();
        CallSession::factory()->create(['student_id' => $student->id, 'ended_at' => now()]);
        ExamTranscript::factory()->create([
            'student_id' => $student->id, 'status' => ExamTranscript::STATUS_FAILED,
        ]);

        $this->actingAs($student)->get(route('student.training-history'))
            ->assertOk()->assertSee('Awaiting result')->assertSee('Evaluation failed')
            ->assertSee('Not scored yet')
            ->assertViewHas('attempts', fn ($rows) => $rows->total() === 2);
    }

    public function test_matching_session_and_transcript_are_counted_once(): void
    {
        $student = User::factory()->student()->create();
        CallSession::factory()->create(['student_id' => $student->id, 'call_id' => 'shared-call']);
        ExamTranscript::factory()->create(['student_id' => $student->id, 'external_id' => 'shared-call']);
        CallSession::factory()->create([
            'student_id' => $student->id, 'call_id' => 'browser-sip-id', 'matched_at' => now(),
        ]);
        ExamTranscript::factory()->create(['student_id' => $student->id, 'external_id' => 'provider-id']);

        $this->actingAs($student)->get(route('student.training-history'))
            ->assertOk()->assertViewHas('attempts', fn ($rows) => $rows->total() === 2);
    }

    public function test_history_and_result_links_are_limited_to_the_signed_in_student(): void
    {
        $student = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        $otherResult = Result::factory()->create(['student_id' => $other->id]);
        CallSession::factory()->create(['student_id' => $other->id, 'subject' => 'Private session']);
        ExamTranscript::factory()->create([
            'student_id' => $other->id, 'subject' => 'Private transcript', 'result_id' => $otherResult->id,
        ]);
        // A corrupt link must not expose a different student's result either.
        ExamTranscript::factory()->create([
            'student_id' => $student->id, 'result_id' => $otherResult->id,
            'status' => ExamTranscript::STATUS_EVALUATED,
        ]);

        $this->actingAs($student)->get(route('student.training-history'))
            ->assertOk()->assertDontSee('Private session')->assertDontSee('Private transcript')
            ->assertDontSee(route('student.results.show', $otherResult), false)
            ->assertViewHas('attempts', fn ($rows) => $rows->total() === 1)
            ->assertViewHas('historyResults', fn ($results) => $results->isEmpty());
    }

    public function test_history_is_paginated_and_attempt_numbers_continue_on_page_two(): void
    {
        $student = User::factory()->student()->create();
        CallSession::factory()->count(12)->create(['student_id' => $student->id]);

        $this->actingAs($student)->get(route('student.training-history', ['page' => 2]))
            ->assertOk()->assertSee('#2')->assertSee('#1')
            ->assertViewHas('attempts', fn ($rows) => $rows->total() === 12 && $rows->count() === 2);
    }

    public function test_history_has_its_own_sidebar_page_without_the_call_widget(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('student.training-history'))
            ->assertOk()->assertViewIs('student.training-history')
            ->assertSee('aria-current="page"', false)
            ->assertDontSee('webcall-bd%201.js', false);

        $this->get(route('student.voice-exam'))
            ->assertOk()->assertSee(route('student.training-history'), false)
            ->assertDontSee('training-history-title', false);
    }

    public function test_history_requires_student_authentication(): void
    {
        $this->get(route('student.training-history'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->teacher()->create())
            ->get(route('student.training-history'))->assertForbidden();
    }

    public function test_empty_history_has_a_clear_empty_state(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->get(route('student.training-history'))
            ->assertOk()->assertSee('No training attempts yet')
            ->assertViewHas('attempts', fn ($rows) => $rows->total() === 0);
    }
}
