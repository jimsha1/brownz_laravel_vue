@include('accounts.financial-statement.day-book-pdf-view')

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 350);
    });
</script>
