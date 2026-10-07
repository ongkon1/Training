@extends('layouts.app')

@section('title', 'Chat Training')
@section('heading', 'Chat Training')
@section('subheading', 'Build your language skills, one conversation at a time.')

@push('styles')
    <link href="{{ asset('asset/css/chat-training.css') }}?v={{ filemtime(public_path('asset/css/chat-training.css')) }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('asset/js/chat-training-embed.js') }}?v={{ filemtime(public_path('asset/js/chat-training-embed.js')) }}" defer></script>
    <script id="chat-training-script" src="https://app.speaklar.com/js/ai-chatbot-widget.js?v=1784779435"
            data-chatbot-id="4a6f7efaadd5a5a8df27f874da0dcfcbf2ee82bcc5d9800c"
            data-config-endpoint="https://app.speaklar.com/api/ai-chatbot-widget"
            data-response-endpoint="https://app.speaklar.com/api/ai-chatbot-widget/message"
            data-human-poll-endpoint="https://app.speaklar.com/api/ai-chatbot-widget/human-poll" async></script>
@endpush

@section('content')
    <section class="chat-training card" aria-labelledby="chat-trainer-title">
        <header class="chat-training-header">
            <span class="chat-trainer-avatar" aria-hidden="true"><i class="bi bi-chat-heart"></i></span>
            <div>
                <h2 id="chat-trainer-title" class="h5 mb-1">Your language trainer</h2>
                <p class="text-muted small mb-0">A space to practice, learn, and grow.</p>
            </div>
        </header>
        <div id="chat-training-widget" class="chat-training-widget"
             data-chatbot-id="4a6f7efaadd5a5a8df27f874da0dcfcbf2ee82bcc5d9800c">
            <div id="chat-training-loading" class="chat-training-loading" role="status">
                <div class="spinner-border text-primary mb-3" aria-hidden="true"></div>
                <p class="text-muted mb-0">Loading your chat trainer...</p>
            </div>
            <p id="chat-training-error" class="alert alert-warning m-3" role="alert" hidden>
                The chat trainer could not load. Please refresh the page to try again.
            </p>
        </div>
    </section>
@endsection
