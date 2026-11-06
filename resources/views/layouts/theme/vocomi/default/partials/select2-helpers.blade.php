<script>
    window.vocoHasSelect2 = function () {
        return typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 === 'function';
    };

    window.vocoInitSelect2 = function ($element, options) {
        if (!window.vocoHasSelect2() || !$element || !$element.length) {
            return $element;
        }

        if ($element.hasClass('select2-hidden-accessible')) {
            return $element;
        }

        return $element.select2(options || {});
    };

    window.vocoDestroySelect2 = function ($element) {
        if (!window.vocoHasSelect2() || !$element || !$element.length) {
            return $element;
        }

        if ($element.hasClass('select2-hidden-accessible')) {
            $element.select2('destroy');
        }

        return $element;
    };
</script>
