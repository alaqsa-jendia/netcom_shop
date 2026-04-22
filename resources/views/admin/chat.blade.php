@extends('layouts.admin')

@section('title', 'المحادثات')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">
                <i class="fas fa-comments me-2 text-primary"></i>
                المحادثات
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="row g-0">
            <!-- Users List Sidebar -->
            <div class="col-md-4 border-end">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">المستخدمون</h5>
                </div>
                <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                    @forelse($users as $user)
                        <a href="{{ route('admin.chat', ['user_id' => $user->id]) }}" 
                           class="list-group-item list-group-item-action {{ $selectedUser && $selectedUser->id == $user->id ? 'active' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->chats->isNotEmpty())
                                        <br><small class="text-muted">{{ $user->chats->last()->message }}</small>
                                    @endif
                                </div>
                                @if($user->chats->isNotEmpty())
                                    <small class="text-muted">{{ $user->chats->last()->created_at->format('H:i') }}</small>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <p class="mb-0">لا توجد محادثات</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="col-md-8">
                @if($selectedUser)
                    <div class="p-3 border-bottom bg-light">
                        <h5 class="mb-0">{{ $selectedUser->name }}</h5>
                        <small class="text-muted">{{ $selectedUser->phone }}</small>
                    </div>

                    <!-- Messages Area -->
                    <div class="chat-messages p-3" id="chatMessages" style="max-height: 400px; overflow-y: auto;">
                        @forelse($chats as $chat)
                            <div class="mb-3 {{ $chat->sender_type === 'admin' ? 'text-end' : '' }}">
                                <div class="d-inline-block p-3 rounded {{ $chat->sender_type === 'admin' ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                                    <p class="mb-1">{{ $chat->message }}</p>
                                    <small class="{{ $chat->sender_type === 'admin' ? 'text-light' : 'text-muted' }}">
                                        {{ $chat->created_at->format('Y-m-d H:i') }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-comment-slash fa-3x mb-2"></i>
                                <p>لا توجد رسائل</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Message Form -->
                    <div class="p-3 border-top">
                        <form method="POST" action="{{ route('admin.send_chat') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                            <div class="input-group">
                                <input type="text" name="message" class="form-control" placeholder="اكتب رسالتك..." required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x mb-2"></i>
                            <p>اختر محادثة من الجهة اليسرى</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
@endsection
@endsection