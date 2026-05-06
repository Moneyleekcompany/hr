<script>
$(document).ready(function () {
    const initial = document.documentElement.getAttribute('data-theme') || '{{ \App\Helpers\AppHelper::getTheme() }}';
    syncToggleIcons(initial);

    $('#moon').on('click', () => requestThemeSwitch('dark'));
    $('#sun').on('click', () => requestThemeSwitch('light'));

    function requestThemeSwitch(intended) {
        // طبّق الثيم محليًا فورًا (smooth، بدون انتظار السيرفر)
        document.documentElement.setAttribute('data-theme', intended);
        try { localStorage.setItem('hr-theme', intended); } catch (e) {}
        syncToggleIcons(intended);

        // مزامنة مع السيرفر — بدون reload عشان مفيش flash
        $.ajax({
            type: 'GET',
            url: "{{ route('admin.change-theme') }}",
            error: function () {
                // لو فشل، رجّع للثيم القديم
                const fallback = intended === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', fallback);
                try { localStorage.setItem('hr-theme', fallback); } catch (e) {}
                syncToggleIcons(fallback);
            }
        });
    }

    function syncToggleIcons(theme) {
        if (theme === 'dark') {
            $('#sun').show();
            $('#moon').hide();
        } else {
            $('#moon').show();
            $('#sun').hide();
        }
    }
});
</script>
