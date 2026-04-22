@extends('layouts.user')

@section('title', 'الدعم الفني')

@section('user_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-3">
                <i class="fas fa-headset me-2 text-primary"></i>
                الدعم الفني
            </h4>
        </div>
    </div>

    @if(isset($contactInfo) && ($contactInfo['phone'] || $contactInfo['whatsapp']))
    <div class="row g-3 mb-4">
        @if(isset($contactInfo['phone']) && $contactInfo['phone'])
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="contact-icon me-3">
                        <i class="fas fa-phone fa-2x text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">رقم الهاتف</p>
                        <h5 class="fw-bold mb-0">{{ $contactInfo['phone'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(isset($contactInfo['whatsapp']) && $contactInfo['whatsapp'])
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="contact-icon me-3">
                        <i class="fab fa-whatsapp fa-2x text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">واتساب</p>
                        <a href="https://wa.me/{{ $contactInfo['whatsapp'] }}" target="_blank" class="btn btn-success btn-sm">
                            <i class="fab fa-whatsapp me-1"></i>
                            تواصل عبر واتساب
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-comments me-2"></i>
                المحادثة
            </h5>
        </div>
        <div class="card-body chat-container" style="max-height: 400px; overflow-y: auto;" id="chatContainer">
            @if($chats->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <p>ابدأ محادثة جديدة</p>
                </div>
            @else
                @foreach($chats as $chat)
                    <div class="mb-3 {{ $chat->sender_type === 'user' ? 'text-end' : '' }}">
                        <div class="d-inline-block p-3 rounded {{ $chat->sender_type === 'user' ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                            <p class="mb-0">{{ $chat->message }}</p>
                            <small class="{{ $chat->sender_type === 'user' ? 'text-white-50' : 'text-muted' }}">{{ $chat->created_at->format('H:i') }}</small>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="card-footer bg-white">
            <form method="POST" action="{{ route('send_message') }}">
                @csrf
                <div class="input-group">
                    <input type="text" name="message" class="form-control" placeholder="اكتب رسالتك..." required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 @endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('chatContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});
</script>
@endsection

<style>
.contact-icon {
    width: 50px;
    height: 50px;
    background: var(--gray-100);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
