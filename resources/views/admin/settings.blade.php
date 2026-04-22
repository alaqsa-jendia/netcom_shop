@extends('layouts.admin')

@section('title', 'الإعدادات')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">
                <i class="fas fa-cog me-2 text-primary"></i>
                الإعدادات
            </h4>
        </div>
    </div>

     <div class="row g-4">
         <div class="col-md-12">
             <div class="card">
                 <div class="card-header">
                     <h5 class="fw-bold mb-0">
                         <i class="fas fa-store me-2"></i>
                         إعدادات المتجر
                     </h5>
                 </div>
                 <div class="card-body">
                      <form method="POST" action="{{ route('admin.settings') }}" enctype="multipart/form-data">
                          @csrf
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="mb-3">
                                      <label class="form-label">اسم المتجر</label>
                                      <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $telegram->system_name ?? '') }}" placeholder="NetCom">
                                      <small class="text-muted">اسم المتجر - سيظهر في الصفحات الرئيسية</small>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="mb-3">
                                      <label class="form-label">شعار المتجر</label>
                                      <input type="file" name="logo" class="form-control" accept="image/*">
                                      @if($telegram && !empty($telegram->logo))
                                          <div class="mt-2">
                                              <img src="{{ asset('storage/' . $telegram->logo) }}" alt="Logo" style="max-height: 60px; max-width: 200px;">
                                              <div class="form-check mt-2">
                                                  <input class="form-check-input" type="checkbox" name="delete_logo" id="deleteLogo" value="1">
                                                  <label class="form-check-label" for="deleteLogo">
                                                      حذف الشعار
                                                  </label>
                                              </div>
                                          </div>
                                      @endif
                                  </div>
                              </div>
                          </div>
                          <button type="submit" class="btn btn-primary w-100">
                               <i class="fas fa-save me-2"></i>
                               حفظ إعدادات المتجر
                           </button>
                      </form>
                 </div>
             </div>
         </div>

         <div class="col-md-6">
             <div class="card h-100">
                 <div class="card-header">
                     <h5 class="fw-bold mb-0">
                         <i class="fab fa-telegram me-2"></i>
                         إعدادات Telegram
                     </h5>
                 </div>
                 <div class="card-body">
                     <form method="POST" action="{{ route('admin.settings') }}">
                         @csrf
                         <div class="mb-3">
                             <label class="form-label">Bot Token</label>
                             <input type="text" name="bot_token" class="form-control" value="{{ old('bot_token', $telegram->bot_token ?? '') }}" required>
                         </div>
                         <div class="mb-3">
                             <label class="form-label">Chat ID</label>
                             <input type="text" name="chat_id" class="form-control" value="{{ old('chat_id', $telegram->chat_id ?? '') }}">
                             <small class="text-muted">أدخل معرف القناة أو المجموعة</small>
                         </div>
                         <div class="mb-3 form-check">
                             <input type="checkbox" name="notifications_enabled" class="form-check-input" id="notifications" {{ old('notifications_enabled', $telegram->notifications_enabled ?? true) ? 'checked' : '' }}>
                             <label class="form-check-label" for="notifications">تفعيل الإشعارات</label>
                         </div>
<button type="submit" class="btn btn-primary w-100">
                              <i class="fas fa-save me-2"></i>
                              حفظ إعدادات Telegram
                          </button>
                     </form>
                 </div>
             </div>
         </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-address-book me-2"></i>
                        معلومات التواصل
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-phone me-1"></i>
                                رقم الهاتف
                            </label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $telegram->contact_phone ?? '') }}" placeholder="0591234567">
                            <small class="text-muted">رقم الهاتف للتواصل المباشر</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fab fa-whatsapp me-1"></i>
                                رقم الواتساب
                            </label>
                            <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $telegram->whatsapp_number ?? '') }}" placeholder="970591234567+">
                            <small class="text-muted">رقم الواتساب مع رمز الدولة (مثال: 970591234567+)</small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save me-2"></i>
                            حفظ معلومات التواصل
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
