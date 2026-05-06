{{-- Toast container — يتم استدعاؤها مرة واحدة في master.blade.php --}}
{{-- استخدام من JS: window.toast({ type: 'success', message: '...', timeout: 4000 }) --}}
{{-- يدعم: success | danger | warning | info --}}

<div id="appToastContainer" class="mt-toast-container" aria-live="polite" aria-atomic="true"></div>
