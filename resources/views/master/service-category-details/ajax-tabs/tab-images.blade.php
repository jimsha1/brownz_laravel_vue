<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables/expressions/functions directly inside html
 */
?>
<div id="vue-details-tab-images" class="has-vue-form">
  <template v-if="category_id > 0">
  	<form v-bind:action="urls.form_save_url" ref="imagePickerForm" method="post" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="category_id" v-model="category_id">
      <input type="hidden" name="image_target" value="" ref="imageTargetInput">
      <input v-on:change="$refs.imagePickerForm.submit()" ref="imageFileChooser" type="file" name="category_image_file" style="display:none">
    </form>

    <div class="row">
      <div class="col">
        <div class="text-center" style="max-width:160px">
          <img v-bind:src="category_info.banner_image_url" alt="" class="rounded">
          <button v-on:click="$refs.imageTargetInput.value='banner_image';$refs.imageFileChooser.click()" type="button" class="btn btn-link">
            {{ __('Change Banner Image') }}
          </button>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="text-center" style="max-width:160px">
          <img v-bind:src="category_info.square_image_url" alt="" class="rounded">
          <button v-on:click="$refs.imageTargetInput.value='square_image';$refs.imageFileChooser.click()" type="button" class="btn btn-link">
            {{ __('Change Square Image') }}
          </button>
        </div>
      </div>
    </div>

  </template>
</div>

<script>
var vue_details_tab_images = null;
jQuery(function ($) {
  vue_details_tab_images = new Vue({
    el: "#vue-details-tab-images",
    data: <?php echo json_encode($details_images_vue); ?>
  });
});
</script>
