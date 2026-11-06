<style>
  #vue-details-tab-commission.service-commission {
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
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-commission .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: -4px 0 14px;
  }

  #vue-details-tab-commission .option-box {
    background: #fff;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 12px;
  }

  #vue-details-tab-commission .brw-checkbox-browz-dds {
    font-size: 14px;
    margin-bottom: 0;
  }

  #vue-details-tab-commission .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
</style>

<div id="vue-details-tab-commission" class="brw-form-la service-commission">
    <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
        @csrf
        <input type="hidden" name="service_id" v-model="service_id">
        <input type="hidden" name="tab_name" v-model="tab_name">

        <div class="edit-section">
            <p class="edit-section-title">{{ __('Add Commission') }}</p>
            <p class="edit-hint">{{ __('Set the commission period, percentage, and active status for this service.') }}</p>

            <div class="row">
                <vcinput-datepicker id="start_date" label="{{ __('Start Date') }}" v-model="service_info.start_date"></vcinput-datepicker>
                <vcinput-datepicker id="end_date" label="{{ __('End Date') }}" v-model="service_info.end_date"></vcinput-datepicker>
                <vcinput-basic id="commission_percentage" label="{{ __('Commission Percentage') }}" v-model="service_info.commission_percentage" type="text"></vcinput-basic>
                <div class="col-lg-12">
                    <div class="option-box">
                        <input type="hidden" name="commission_status" value="0">
                        <label class="brw-checkbox-browz-dds mb-0" for="commission_status">
                            <input v-model="service_info.commission_status"
                                class="brow-checkbox-dd"
                                type="checkbox"
                                value="1"
                                id="commission_status"
                                name="commission_status"
                                true-value="1"
                                false-value="0">
                            {{ __('Is Active') }}
                            <span class="brow-checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="vc-form-buttons">
            <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                href="javascript:void(0)"
                v-on:click.prevent="cancelForm">
                <i class="fa fa-undo"></i> {{ __('Cancel') }}
            </a>

            <button type="submit" class="btn btn-primary btn_dark-x">
                <i class="fa fa-check"></i> {{ __('Save') }}
            </button>
        </div>
    </form>
</div>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_details_tab_commission = null;
    jQuery(function($) {
        vue_details_tab_commission = new Vue({
            el: "#vue-details-tab-commission",
            data: function () {
                return <?php echo json_encode($details_edit_vue); ?>;
            },
            methods: {
                cancelForm: function () {
                    window.location.href = this.urls.button_cancel_url;
                }
            }
        });
    });
</script>
