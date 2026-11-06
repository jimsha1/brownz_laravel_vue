@include('accounts.financial-year.details-pdf-view')

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 350);
    });
</script>
