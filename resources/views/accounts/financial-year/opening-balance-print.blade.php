@include('accounts.financial-year.opening-balance-pdf-view')

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 350);
    });
</script>
