<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables/expressions/functions directly inside html
 */
?>
<div id="vue-details-tab-web-content" class="has-vue-form">
  <template v-if="category_id > 0">
    <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
      @csrf
      <input type="hidden" name="category_id" v-model="category_id">
      <input type="hidden" name="tab_name" v-model="tab_name">

      <div class="row">
        <vcinput-textarea v-model="category_info.tagline_en" id="tagline_en" label="{{ __('Tagline') }}"></vcinput-textarea>
        <vcinput-textarea v-model="category_info.tagline_ar" id="tagline_ar" label="{{ __('Tagline Arabic') }}"></vcinput-textarea>
      </div>

      <div class="row">
        <vcinput-textarea v-model="category_info.description_en" id="description_en" label="{{ __('Description') }}"></vcinput-textarea>
        <vcinput-textarea v-model="category_info.description_ar" id="description_ar" label="{{ __('Description Arabic') }}"></vcinput-textarea>
      </div>

      <div class="row">
        <vcinput-textarea v-model="category_info.long_description_en" id="long_description_en" label="{{ __('Long Description') }}" rows="7"></vcinput-textarea>
        <vcinput-textarea v-model="category_info.long_description_ar" id="long_description_ar" label="{{ __('Long Description Arabic') }}" rows="7"></vcinput-textarea>
      </div>

      <div class="vc-form-buttons text-right">
        <a class="btn btn-secondary vc-form-btn-cancel" v-bind:href="urls.button_cancel_url">
          <i class="fa fa-undo"></i> {{ __('Cancel') }}
        </a>

        <button type="submit" class="btn btn-primary">
          <i class="fa fa-check"></i> {{ __('Save') }}
        </button>
      </div>
    </form>
  </template>
</div>

<script>
var vue_details_tab_web_content = null;
jQuery(function ($) {
  vue_details_tab_web_content = new Vue({
    el: "#vue-details-tab-web-content",
    data: <?php echo json_encode($details_web_content_vue); ?>
  });
});
</script>
