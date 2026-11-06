<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.4.0/perfect-scrollbar.min.js" integrity="sha256-pOydVY7re8c1n+fEgg3uoslR/di9NMsOFXJ0Esf2xjQ=" crossorigin="anonymous"></script>

{{-- <script src="{{ asset('theme-admin/cui/dist/js/coreui.min.js') }}" type="text/javascript"></script> --}}
<script src="{{ asset('web/js/coreui.min.js') }}"></script>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@include('layouts.theme.vocomi.default.partials.select2-helpers')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{ asset('components/toastr/build/toastr.min.js') }}"></script>

<script src="{{ asset('/js/vue.min.js') }}"></script>

<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.js"></script>

<script>
Vue.filter('vc_calendar_date', function (value) {
  return get_voco_moment(value, "{{ config('vocomi.settings.format.jscalendar.format_moment') }}");
});

Vue.filter('vc_duration', function (value) {
  var dur = moment.duration(value, "minutes");
  var hours = dur.hours();
  var minutes = dur.minutes();

  var dur_string = "";
  if(hours > 0) {
    dur_string += hours + " hour" + (hours > 1 ? "s " : " ");
  }
  if(minutes > 0) {
    dur_string += minutes + " minute" + (minutes > 1 ? "s" : "");
  }

  return dur_string;
});

Vue.filter('vc_duration_short', function (value) {
  return moment.utc(moment.duration(parseInt(value), "minutes").asMilliseconds()).format("HH:mm");
});
</script>

<script>
var v_breadcrumb = {
  breadcrumb_links: @json([['text' => __('Dashboard'), 'url' => route('dashboard')]])
};
jQuery(function ($) {
  // Breadcrumb mount element may be absent when the layout omits #vc-breadcrumb
  if (document.querySelector("#vc-breadcrumb")) {
    v_breadcrumb = new Vue({
      el: "#vc-breadcrumb",
      data: {
        breadcrumb_links: v_breadcrumb.breadcrumb_links
      }
    });
  }
});
</script>

<script>
$(document).ajaxError(function(event, jqxhr, settings) {
  if (jqxhr.status === 401) {
    window.location.href = "{{ url('/login') }}";
  }
});
</script>

@stack('scripts')

<script>
$.fn.datepicker.defaults.format = "{{ config('vocomi.settings.format.jscalendar.format_js') }}";
</script>

<script src="{{ asset('/js/script.pack.js') }}"></script>
<script src="{{ asset('/js/ats-sp.pack.js') }}"></script>
<script src="{{ asset('/js/ats-vue.js') }}"></script>
<script src="{{ asset('/js/custom-vue.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/underscore@1.12.0/underscore-min.js"></script>

@stack('inline-scripts')
