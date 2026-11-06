@include('accounts.financial-statement.profit-loss-account-transactions-pdf-view')

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 350);
    });
</script>
