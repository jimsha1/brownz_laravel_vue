<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<div id="vue-details-tab-edit" class="has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
            @csrf
            <input type="hidden" name="vendor_id" v-model="vendor_id">
            <input type="hidden" name="tab_name" v-model="tab_name">

            <div class="row">
                <vcinput-basic v-model="vendor_info.name" id="name" label="Name"></vcinput-basic>
                <vcinput-basic v-model="vendor_info.email" id="email" label="Email"></vcinput-basic>

                <vcinput-basic-number v-model="vendor_info.phone" id="phone" label="{{ __('Phone') }}">
                </vcinput-basic-number>

                <div class="form-group col-md-12">
                    <div class="form-check">
                        <label class="form-check-label brw-checkbox-browz-dds mb-0" for="is_active">
                            <input v-model="vendor_info.is_active" class="form-check-input brow-checkbox-dd" type="checkbox"
                                value="1" id="is_active" name="is_active" checked>
                                {{ __('Is Active') }}
                                <span class="brow-checkmark"></span>
                        </label>
                    </div>
                </div>


                <div class="vc-form-buttons text-right ml-auto">
                    <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d mr-2" v-bind:href="urls.button_cancel_url">
                        <i class="fa fa-undo"></i> {{ __('Cancel') }}
                    </a>

                    <button type="submit" class="btn btn-primary btn_dark-x">
                        <i class="fa fa-check"></i> {{ __('Save') }}
                    </button>
                </div>
        </form>
    </template>
</div>

<script>
    var vue_details_tab_edit = null;
    jQuery(function($) {
        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: <?php echo json_encode($details_edit_vue); ?>
        });
    });
</script>
