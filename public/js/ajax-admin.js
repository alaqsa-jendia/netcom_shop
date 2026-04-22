/**
 * AJAX Admin Handler for Laravel 12
 * Enables dynamic updates without page refresh
 */

(function() {
    'use strict';
    
    // Get CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || document.querySelector('input[name="_token"]')?.value 
            || '';
    }
    
    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: ${type === 'success' ? '#22c55e' : '#ef4444'};
            color: white;
            border-radius: 8px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
    
    // AJAX form submission
    window.ajaxSubmit = function(formId, successCallback) {
        const form = document.getElementById(formId);
        if (!form) {
            console.error('Form not found:', formId);
            return;
        }
        
        const formData = new FormData(form);
        const url = form.action;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                showToast(data.message || 'تم بنجاح');
                if (successCallback) successCallback(data);
                if (data.redirect) window.location.href = data.redirect;
            } else {
                showToast(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(err => {
            console.error('AJAX Error:', err);
            showToast('حدث خطأ في الاتصال', 'error');
        });
    };
    
    // AJAX delete
    window.ajaxDelete = function(url, rowId, confirmMsg = 'هل أنت متأكد من الحذف؟') {
        if (!confirm(confirmMsg)) return;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message || 'تم الحذف');
                const row = document.getElementById(rowId);
                if (row) row.remove();
            } else {
                showToast(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(err => {
            console.error('Delete Error:', err);
            showToast('حدث خطأ', 'error');
        });
    };
    
    // AJAX toggle status
    window.ajaxToggle = function(url, rowId) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message || 'تم التحديث');
                // Reload the row or page
                if (data.is_active !== undefined) {
                    const badge = document.querySelector(`#${rowId} .badge`);
                    if (badge) {
                        badge.className = data.is_active ? 'badge bg-success' : 'badge bg-danger';
                        badge.textContent = data.is_active ? 'مفعل' : 'غير مفعل';
                    }
                }
            } else {
                showToast(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(err => {
            console.error('Toggle Error:', err);
            showToast('حدث خطأ', 'error');
        });
    };
    
    // AJAX create (for modal forms)
    window.ajaxCreate = function(formId, tableBodyId, partialUrl) {
        const form = document.getElementById(formId);
        if (!form) {
            console.error('Form not found:', formId);
            return;
        }
        
        const formData = new FormData(form);
        const url = form.action;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message || 'تم الإنشاء');
                if (data.html && tableBodyId) {
                    const tbody = document.getElementById(tableBodyId);
                    if (tbody) tbody.insertAdjacentHTML('beforeend', data.html);
                }
                if (data.item) {
                    // Add new row dynamically
                    const tbody = document.getElementById(tableBodyId);
                    if (tbody && data.item_html) {
                        tbody.insertAdjacentHTML('beforeend', data.item_html);
                    }
                }
                form.reset();
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                if (modal) modal.hide();
            } else {
                showToast(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(err => {
            console.error('Create Error:', err);
            showToast('حدث خطأ', 'error');
        });
    };
    
    // Reload table from server
    window.reloadTable = function(tableId, url) {
        fetch(url, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            const table = document.getElementById(tableId);
            if (table && data.html) {
                table.innerHTML = data.html;
            }
        })
        .catch(err => console.error('Reload Error:', err));
    };
    
    // Initialize AJAX for all admin forms with data-ajax attribute
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form[data-ajax]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const callback = form.dataset.ajaxSuccess;
                ajaxSubmit(form.id, callback ? window[callback] : null);
            });
        });
    });
    
})();