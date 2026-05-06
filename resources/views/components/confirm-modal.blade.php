{{-- نافذة تأكيد عامة — يتم استدعاؤها مرة واحدة في master.blade.php --}}
{{-- استخدام: --}}
{{-- <a href="..." data-confirm="هل أنت متأكد؟" data-confirm-variant="danger" data-confirm-label="حذف">حذف</a> --}}
{{-- أو data-method="DELETE" يحوّل الرابط إلى form POST مع _method=DELETE --}}

<div class="modal fade" id="appConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div class="mt-confirm-icon mb-3" data-icon-wrap>
                    <i class="ti ti-alert-triangle"></i>
                </div>
                <h5 class="fw-bold mb-2" data-confirm-title>هل أنت متأكد؟</h5>
                <p class="text-muted mb-0 small" data-confirm-message>هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    إلغاء
                </button>
                <button type="button" class="btn btn-danger px-4" data-confirm-action>
                    تأكيد
                </button>
            </div>
        </div>
    </div>
</div>
