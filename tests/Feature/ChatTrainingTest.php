<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTrainingTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_widget_is_scoped_to_chat_training_with_the_supplied_endpoints(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->get(route('student.chat-training'))
            ->assertOk()
            ->assertSee('id="chat-training-widget"', false)
            ->assertSee('https://app.speaklar.com/js/ai-chatbot-widget.js?v=1784779435', false)
            ->assertSee('data-chatbot-id="4a6f7efaadd5a5a8df27f874da0dcfcbf2ee82bcc5d9800c"', false)
            ->assertSee('data-config-endpoint="https://app.speaklar.com/api/ai-chatbot-widget"', false)
            ->assertSee('data-response-endpoint="https://app.speaklar.com/api/ai-chatbot-widget/message"', false)
            ->assertSee('data-human-poll-endpoint="https://app.speaklar.com/api/ai-chatbot-widget/human-poll"', false)
            ->assertDontSee('Chat preview')
            ->assertDontSee('webcall-bd%201.js', false);

        foreach (['student.voice-exam', 'student.training-history', 'student.profile'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertDontSee('ai-chatbot-widget.js', false);
        }
    }

    public function test_chat_training_requires_a_signed_in_student(): void
    {
        $this->get(route('student.chat-training'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->teacher()->create())
            ->get(route('student.chat-training'))->assertForbidden();
    }
}
