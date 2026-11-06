{{ voco_ga() }}
<script>
var vc_moment_date_offset = new Date("<?php echo (new DateTime())->format('Y-m-d\TH:i:s.vP'); ?>").getTime() - Date.now();
var vc_jscalendar_moment_format = "{{ config('vocomi.settings.format.jscalendar.format_moment') }}";
</script>
