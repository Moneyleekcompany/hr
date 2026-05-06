/**
 * Modern UI components: toasts, confirm modal, required-field markers, table scroll hints.
 * Loaded once in master.blade.php — exposes window.toast() global API.
 */
(function () {
    'use strict';

    // ============================================================
    // Toasts
    // ============================================================
    const TOAST_ICONS = {
        success: 'ti-circle-check',
        danger:  'ti-alert-circle',
        warning: 'ti-alert-triangle',
        info:    'ti-info-circle',
    };

    function createToast({ type = 'info', message = '', timeout = 4500 } = {}) {
        if (!message) return;
        const container = document.getElementById('appToastContainer');
        if (!container) return;

        const safeType = TOAST_ICONS[type] ? type : 'info';
        const el = document.createElement('div');
        el.className = `mt-toast mt-toast--${safeType}`;
        el.setAttribute('role', safeType === 'danger' ? 'alert' : 'status');
        el.innerHTML = `
            <div class="mt-toast__icon"><i class="ti ${TOAST_ICONS[safeType]}"></i></div>
            <div class="mt-toast__body"></div>
            <button type="button" class="mt-toast__close" aria-label="إغلاق">
                <i class="ti ti-x"></i>
            </button>
        `;
        // textContent = آمن من XSS
        el.querySelector('.mt-toast__body').textContent = message;
        container.appendChild(el);

        const dismiss = () => {
            if (!el.isConnected) return;
            el.classList.add('is-leaving');
            setTimeout(() => el.remove(), 280);
        };

        el.querySelector('.mt-toast__close').addEventListener('click', dismiss);
        if (timeout > 0) setTimeout(dismiss, timeout);
    }

    window.toast = createToast;
    window.toast.success = (message, opts) => createToast({ ...opts, type: 'success', message });
    window.toast.error   = (message, opts) => createToast({ ...opts, type: 'danger', message });
    window.toast.warning = (message, opts) => createToast({ ...opts, type: 'warning', message });
    window.toast.info    = (message, opts) => createToast({ ...opts, type: 'info', message });

    // Bridge: تحويل alert-modern messages إلى toasts (للحفاظ على التوافق)
    function bridgeFlashMessages() {
        const flashes = document.querySelectorAll('.alert-modern:not([data-toast-bridged])');
        flashes.forEach(el => {
            el.setAttribute('data-toast-bridged', '1');
            const body = el.querySelector('div:not(.mt-toast__icon)') || el;
            const message = body.textContent.trim();
            if (!message) return;
            let type = 'info';
            if (el.classList.contains('alert-success')) type = 'success';
            else if (el.classList.contains('alert-danger')) type = 'danger';
            else if (el.classList.contains('alert-warning')) type = 'warning';
            createToast({ type, message });
            el.style.display = 'none'; // نخفي القديم — الـ toast بقى البديل
        });
    }

    // ============================================================
    // Confirm Modal
    // ============================================================
    function setupConfirmModal() {
        const modalEl = document.getElementById('appConfirmModal');
        if (!modalEl || typeof bootstrap === 'undefined') return;
        const modal = new bootstrap.Modal(modalEl);
        const titleEl = modalEl.querySelector('[data-confirm-title]');
        const messageEl = modalEl.querySelector('[data-confirm-message]');
        const actionBtn = modalEl.querySelector('[data-confirm-action]');
        const iconWrap = modalEl.querySelector('[data-icon-wrap]');

        let pendingTrigger = null;

        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-confirm]');
            if (!trigger) return;
            // تجاهل لو البوتستراب modal تاني فاتح أو لو متفعل من قبل
            if (trigger.dataset.confirmBypass === '1') return;

            e.preventDefault();
            e.stopPropagation();

            pendingTrigger = trigger;

            const message = trigger.dataset.confirm || 'هل أنت متأكد؟';
            const variant = trigger.dataset.confirmVariant || 'danger';
            const label = trigger.dataset.confirmLabel || 'تأكيد';

            messageEl.textContent = message;
            iconWrap.setAttribute('data-variant', variant);
            actionBtn.textContent = label;
            actionBtn.className = 'btn px-4 btn-' + (variant === 'warning' ? 'warning' : variant === 'info' ? 'info' : 'danger');

            modal.show();
        }, true);

        actionBtn.addEventListener('click', function () {
            const trigger = pendingTrigger;
            modal.hide();
            if (!trigger) return;

            const url = trigger.getAttribute('href');
            const method = (trigger.dataset.method || '').toUpperCase();

            // علّم الزر بـ bypass عشان ينفّذ بدون عرض المودال تاني
            trigger.dataset.confirmBypass = '1';

            if (method && method !== 'GET' && url && url !== '#') {
                // حوّل الرابط إلى form POST مع _method spoofing
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrf) {
                    const t = document.createElement('input');
                    t.type = 'hidden'; t.name = '_token'; t.value = csrf;
                    form.appendChild(t);
                }
                if (method !== 'POST') {
                    const m = document.createElement('input');
                    m.type = 'hidden'; m.name = '_method'; m.value = method;
                    form.appendChild(m);
                }
                document.body.appendChild(form);
                form.submit();
                return;
            }

            // لو href موجود ومفيش method → اتبع الرابط طبيعي
            if (url && url !== '#') {
                window.location.href = url;
                return;
            }

            // fallback: انقر العنصر تاني (بعد تجاوز الـ confirm)
            trigger.click();
        });

        // إعادة تعيين عند الإغلاق
        modalEl.addEventListener('hidden.bs.modal', function () {
            if (pendingTrigger) {
                delete pendingTrigger.dataset.confirmBypass;
                pendingTrigger = null;
            }
        });
    }

    // ============================================================
    // Required-field auto-asterisks
    // ============================================================
    function markRequiredFields(scope) {
        const root = scope || document;
        const inputs = root.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            const id = input.id;
            let label = null;
            if (id) label = root.querySelector(`label[for="${id}"]`);
            if (!label) {
                // ابحث عن أقرب label أب
                label = input.closest('.form-group, .mb-3, .col, td')?.querySelector('label')
                     || input.previousElementSibling?.matches('label') ? input.previousElementSibling : null;
            }
            if (label && !label.classList.contains('mt-required')) {
                label.classList.add('mt-required');
            }
        });
    }

    // ============================================================
    // Table horizontal-scroll hint
    // ============================================================
    function setupTableScrollHints() {
        document.querySelectorAll('.table-responsive').forEach(wrap => {
            const update = () => {
                const hasOverflow = wrap.scrollWidth - wrap.clientWidth > 4;
                wrap.classList.toggle('mt-has-overflow', hasOverflow);
                if (!hasOverflow) return;
                const atEnd = (wrap.scrollWidth - wrap.scrollLeft - wrap.clientWidth) < 4;
                wrap.classList.toggle('mt-overflow--end', atEnd);
            };
            update();
            wrap.addEventListener('scroll', update, { passive: true });
            window.addEventListener('resize', update, { passive: true });
        });
    }

    // ============================================================
    // Init
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        bridgeFlashMessages();
        setupConfirmModal();
        markRequiredFields();
        setupTableScrollHints();
    });
})();
