@include('accounts.financial-statement.profit-loss-pdf-view')

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 350);
    });
</script>
