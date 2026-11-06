<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
    #vue-details-tab-commission.stock-item-commission {
        padding: 4px 0 8px;
    }

    #vue-details-tab-commission .edit-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 16px 16px 6px;
        margin-bottom: 16px;
    }

    #vue-details-tab-commission .edit-section-title {
        font-size: 13px;
        font-weight: 600;
        color: #1D1D1D;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    #vue-details-tab-commission .edit-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 14px;
    }

    #vue-details-tab-commission .vc-form-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
</style>

<div id="vue-details-tab-commission" class="stock-item-commission has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
            @csrf
            <input type="hidden" name="item_id" v-model="item_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="commission_status" :value="Number(stock_item_info.commission_status) === 1 ? 1 : 0">

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Commission') }}</div>
                <p class="edit-hint">{{ __('Set the commission window, percentage, and active status for this item.') }}</p>
                <div class="row">
                    <vcinput-datepicker id="start_date" label="{{ __('Start Date') }}"
                        v-model="stock_item_info.start_date"></vcinput-datepicker>
                    <vcinput-datepicker id="end_date" label="{{ __('End Date') }}"
                        v-model="stock_item_info.end_date"></vcinput-datepicker>
                    <vcinput-basic id="commission_percentage" label="{{ __('Commission Percentage') }}"
                        v-model="stock_item_info.commission_percentage" type="text"></vcinput-basic>

                    <div class="form-group col-md-6">
                        <label for="commission_status_select">{{ __('Status') }}</label>
                        <select class="form-control" id="commission_status_select"
                            v-model="stock_item_info.commission_status">
                            <option :value="1">{{ __('Active') }}</option>
                            <option :value="0">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    v-bind:href="urls.button_cancel_url">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-primary btn_dark-x">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </form>
    </template>
</div>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_details_tab_commission = null;
    jQuery(function($) {
        vue_details_tab_commission = new Vue({
            el: "#vue-details-tab-commission",
            data: function () {
                var payload = <?php echo json_encode($details_edit_vue); ?>;
                if (!payload.stock_item_info || typeof payload.stock_item_info !== 'object') {
                    payload.stock_item_info = {};
                }
                payload.stock_item_info.commission_status = Number(
                    payload.stock_item_info.commission_status != null
                        ? payload.stock_item_info.commission_status
                        : 1
                );
                return payload;
            }
        });
    });
</script>
