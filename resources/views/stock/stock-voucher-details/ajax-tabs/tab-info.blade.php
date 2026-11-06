<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<div id="vue-details-tab-info" class="info-sec-tab-cont" style="margin: 0px -1rem;">
    <template>
        <table class="table">
            <tbody>
                <tr>
                    <td style="width:20%">ID</td>
                    <td>@{{ '#' + stock_voucher_info.id }}</td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>@{{ stock_voucher_info.date }}</td>
                </tr>
                <tr>
                    <td>Voucher Type</td>
                    <td>@{{ stock_voucher_info.voucher_type }}</td>
                </tr>
                <tr>
                    <td>Voucher No</td>
                    <td>@{{ stock_voucher_info.voucher_no }}</td>
                </tr>
                <tr>
                    <td>Narration</td>
                    <td>@{{ stock_voucher_info.description }}</td>
                </tr>
                <tr v-if="stock_voucher_info.edit_logs && stock_voucher_info.edit_logs.length">
                    <td>{{ __('Edit Reason') }}</td>
                    <td>
                        <div v-for="(log, index) in stock_voucher_info.edit_logs" :key="log.id" :class="{ 'mt-2 pt-2 border-top': index > 0 }">
                            <div>@{{ log.edit_reason }}</div>
                            <div class="text-muted font-italic">
                                @{{ (log.edited_user && log.edited_user.name) ? log.edited_user.name : '' }}
                                <span v-if="log.created_at">
                                    · @{{ log.created_at | vc_datetime_expanded }}
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </template>

    <template>
        <div class="table-responsive">
            <table class="table brownz-table-list-v1">
                <thead>
                    <tr>
                        <th>{{ __('SL#') }}</th>
                        <th>Account</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(txn, index) in stock_voucher_info.transactions" :key="txn.id">
                        <td>@{{ index + 1 }}</td>
                        <td>@{{ accountName(txn) }}</td>
                        <td>@{{ txn.debit }}</td>
                        <td>@{{ txn.credit }}</td>
                        <td>@{{ txn.remarks || '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_details_tab_info = null;
    jQuery(function($) {
        vue_details_tab_info = new Vue({
            el: "#vue-details-tab-info",
            data: <?php echo json_encode($details_info_vue); ?>,
            methods: {
                accountName: function(txn) {
                    if (txn && txn.journal && txn.journal.account) {
                        return txn.journal.account.name;
                    }
                    return '-';
                }
            }
        });
    });
</script>
