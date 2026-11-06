<style>
  #vue-list-tab-all .purchase-table th,
  #vue-list-tab-all .purchase-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .purchase-ref {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
    cursor: pointer;
  }

  #vue-list-tab-all .purchase-ref:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .purchase-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  #vue-list-tab-all .amount-pill,
  #vue-list-tab-all .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  #vue-list-tab-all .amount-pill {
    color: #1D1D1D;
    background: #F0F0F3;
    font-variant-numeric: tabular-nums;
  }

  #vue-list-tab-all .status-pill.is-return {
    color: #8A6D00;
    background: rgba(230, 205, 127, 0.35);
  }

  #vue-list-tab-all .manage-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #vue-list-tab-all .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #F0F0F3;
    color: #1D1D1D;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    flex: 0 0 auto;
  }

  #vue-list-tab-all .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  #vue-list-tab-all .password-unlock {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #F7F7F9;
    border-radius: 999px;
    padding: 4px 6px 4px 12px;
  }

  #vue-list-tab-all .password-unlock input {
    border: none;
    background: transparent;
    outline: none;
    box-shadow: none;
    min-width: 120px;
    font-size: 13px;
    height: 28px;
    padding: 0;
  }

  #vue-list-tab-all .password-unlock .unlock-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }

  #vue-list-tab-all .password-unlock .unlock-btn.is-ok {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  #vue-list-tab-all .password-unlock .unlock-btn.is-cancel {
    background: #fff;
    color: #D0342C;
  }

  #vue-list-tab-all .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  #vue-list-tab-all .empty-state i {
    font-size: 28px;
    margin-bottom: 10px;
    color: #b7b7b7;
  }

  #vue-list-tab-all .tab-pagination {
    margin-top: 16px;
  }

  #purchaseDetailsModal .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
  }

  #purchaseDetailsModal .modal-header {
    border-bottom: 1px solid #eee;
    background: #F7F7F9;
  }

  #purchaseDetailsModal .modal-title {
    color: #1D1D1D;
    font-weight: 600;
  }

  #purchaseDetailsModal .modal-body {
    padding: 18px;
  }

  #purchaseDetailsModal .detail-meta {
    margin-bottom: 14px;
    color: #6c757d;
    font-size: 13px;
  }

  #purchaseDetailsModal .detail-meta strong {
    color: #1D1D1D;
  }

  #purchaseDetailsModal .modal-dialog {
    max-width: 980px;
  }

  #purchaseDetailsModal .products-table {
    width: 100%;
  }

  #purchaseDetailsModal .products-table th,
  #purchaseDetailsModal .products-table td {
    font-size: 13px;
    vertical-align: middle;
  }

  #purchaseDetailsModal .products-table th {
    white-space: normal;
  }

  #purchaseDetailsModal .products-table td:not(:first-child) {
    white-space: nowrap;
  }

  #purchaseDetailsModal .detail-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 14px;
  }

  @media (max-width: 767px) {
    #purchaseDetailsModal .detail-summary {
      grid-template-columns: 1fr;
    }
  }

  #purchaseDetailsModal .summary-card {
    background: #F7F7F9;
    border-radius: 12px;
    padding: 12px 14px;
  }

  #purchaseDetailsModal .summary-card .label {
    display: block;
    font-size: 11px;
    color: #6c757d;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #purchaseDetailsModal .summary-card .value {
    font-size: 1rem;
    font-weight: 700;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  #purchaseDetailsModal .detail-info-table td {
    border-top: none;
    border-bottom: 1px solid #ececf1;
    padding: 8px 0;
    vertical-align: top;
    font-size: 13px;
  }

  #purchaseDetailsModal .detail-info-table tr:last-child td {
    border-bottom: none;
  }

  #purchaseDetailsModal .detail-info-label {
    width: 34%;
    color: #6c757d;
    font-weight: 600;
  }

  #purchaseDetailsModal .detail-info-value {
    color: #1D1D1D;
  }

  #purchaseDetailsModal .detail-section-title {
    font-size: 12px;
    font-weight: 700;
    color: #1D1D1D;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    margin: 14px 0 8px;
  }

  #purchaseDetailsModal .totals-card {
    background: #F7F7F9;
    border-radius: 12px;
    padding: 12px 14px;
    margin-top: 14px;
  }

  #purchaseDetailsModal .totals-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 4px 0;
    font-size: 13px;
    color: #6c757d;
    font-variant-numeric: tabular-nums;
  }

  #purchaseDetailsModal .totals-row.is-grand {
    margin-top: 6px;
    padding-top: 8px;
    border-top: 1px solid #dedee4;
    font-size: 14px;
    font-weight: 700;
    color: #1D1D1D;
  }

  #purchaseDetailsModal .payment-list {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  #purchaseDetailsModal .payment-list li {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 4px 0;
    font-size: 13px;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  #purchaseDetailsModal .payment-list li span:first-child {
    color: #6c757d;
    text-transform: capitalize;
  }
</style>

<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 purchase-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Purchase') }}</th>
            <th>{{ __('Vendor') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Invoice No') }}</th>
            <th>{{ __('Amount') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 160px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.purchase_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="purchase-ref" href="javascript:void(0)" @click="showPurchaseDetails(item)">
                @{{ item.purchase_ref_no || ('#' + item.purchase_id) }}
              </a>
              <span class="purchase-meta" v-if="item.is_refund">
                <span class="status-pill is-return">{{ __('Return') }}</span>
              </span>
            </td>
            <td>@{{ (item.vendor && item.vendor.name) || item.vendor_name || '—' }}</td>
            <td>@{{ item.purchase_date || '—' }}</td>
            <td>@{{ item.purchase_invoice_no || '—' }}</td>
            <td>
              <span class="amount-pill">
                @{{ Number(item.total_purchase_amount || 0).toFixed(2) }}
              </span>
            </td>
            <td class="hidden-print text-center">
              <div class="manage-actions">
                <template v-if="item.is_refund">
                  <a class="manage-btn" v-bind:href="item.info_url" title="{{ __('View Return') }}">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </a>
                </template>
                <template v-else>
                  <a class="manage-btn" v-bind:href="item.info_url" title="{{ __('View') }}">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </a>

                  <template v-if="item.can_edit">
                    <span v-if="passwordRowId === item.purchase_id && passwordPurpose === 'edit'" class="password-unlock">
                      <input type="password"
                        v-model="passwordInput"
                        placeholder="{{ __('Password') }}"
                        @keyup.enter="submitPassword()" />
                      <button type="button" class="unlock-btn is-ok" @click="submitPassword()" title="{{ __('Confirm') }}">
                        <i class="fa fa-check"></i>
                      </button>
                      <button type="button" class="unlock-btn is-cancel" @click="cancelPasswordInput" title="{{ __('Cancel') }}">
                        <i class="fa fa-times"></i>
                      </button>
                    </span>
                    <button v-else type="button" class="manage-btn"
                      @click="showPasswordInput(item.purchase_id, 'edit', item.edit_url)"
                      title="{{ __('Edit') }}">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>
                  </template>

                  <template v-if="item.can_return">
                    <span v-if="passwordRowId === item.purchase_id && passwordPurpose === 'return'" class="password-unlock">
                      <input type="password"
                        v-model="passwordInput"
                        placeholder="{{ __('Password') }}"
                        @keyup.enter="submitPassword()" />
                      <button type="button" class="unlock-btn is-ok" @click="submitPassword()" title="{{ __('Confirm') }}">
                        <i class="fa fa-check"></i>
                      </button>
                      <button type="button" class="unlock-btn is-cancel" @click="cancelPasswordInput" title="{{ __('Cancel') }}">
                        <i class="fa fa-times"></i>
                      </button>
                    </span>
                    <button v-else type="button" class="manage-btn"
                      @click="showPasswordInput(item.purchase_id, 'return', item.return_url)"
                      title="{{ __('Purchase Return') }}">
                      <i class="fa fa-undo" aria-hidden="true"></i>
                    </button>
                  </template>
                </template>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="modal fade" id="purchaseDetailsModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Purchase Details') }}</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="detail-summary">
              <div class="summary-card">
                <span class="label">{{ __('Grand Total') }}</span>
                <span class="value">@{{ formatMoney(selected_purchase.total_purchase_amount) }}</span>
              </div>
              <div class="summary-card">
                <span class="label">{{ __('Vendor') }}</span>
                <span class="value">@{{ selected_purchase.vendor_name || '—' }}</span>
              </div>
              <div class="summary-card">
                <span class="label">{{ __('Payment Method') }}</span>
                <span class="value" style="text-transform: capitalize;">
                  @{{ selected_purchase.payment_method || '—' }}
                </span>
              </div>
            </div>

            <table class="table detail-info-table mb-0">
              <tbody>
                <tr>
                  <td class="detail-info-label">{{ __('Purchase Ref No') }}</td>
                  <td class="detail-info-value">@{{ selected_purchase.purchase_ref_no || '—' }}</td>
                </tr>
                <tr>
                  <td class="detail-info-label">{{ __('Date') }}</td>
                  <td class="detail-info-value">@{{ selected_purchase.purchase_date || '—' }}</td>
                </tr>
                <tr>
                  <td class="detail-info-label">{{ __('Invoice No') }}</td>
                  <td class="detail-info-value">@{{ selected_purchase.purchase_invoice_no || '—' }}</td>
                </tr>
                <tr v-if="selected_purchase.purchase_description">
                  <td class="detail-info-label">{{ __('Description') }}</td>
                  <td class="detail-info-value">@{{ selected_purchase.purchase_description }}</td>
                </tr>
              </tbody>
            </table>

            <div class="detail-section-title">{{ __('Products') }}</div>
            <div class="table-responsive">
              <table class="table brownz-table-list-v1 products-table mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('HSN/SAC') }}</th>
                    <th class="text-right">{{ __('Qty') }}</th>
                    <th class="text-right">{{ __('Rate (Incl. of tax)') }}</th>
                    <th class="text-right">{{ __('Rate') }}</th>
                    <th class="text-right">{{ __('Disc. %') }}</th>
                    <th class="text-right">{{ __('Amount') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(product, pIndex) in selected_purchase.products" :key="pIndex">
                    <td>@{{ product.product_name }}</td>
                    <td>@{{ product.hsn_code || '—' }}</td>
                    <td class="text-right">@{{ product.quantity }}</td>
                    <td class="text-right">@{{ formatMoney(product.rate_incl_tax) }}</td>
                    <td class="text-right">@{{ formatMoney(product.cost_price) }}</td>
                    <td class="text-right">@{{ discountLabel(product) }}</td>
                    <td class="text-right">@{{ formatMoney(lineAmount(product)) }}</td>
                  </tr>
                  <tr v-if="!selected_purchase.products || selected_purchase.products.length == 0">
                    <td colspan="7" class="text-center text-muted">{{ __('No products found') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="totals-card">
              <div class="totals-row">
                <span>{{ __('Taxable amount') }}</span>
                <span>@{{ formatMoney(purchaseTaxableTotal) }}</span>
              </div>
              <div class="totals-row">
                <span>{{ __('Freight') }}</span>
                <span>@{{ formatMoney(selected_purchase.freight_amount) }}</span>
              </div>
              <div class="totals-row">
                <span>{{ __('Freight tax %') }}</span>
                <span>@{{ formatMoney(selected_purchase.freight_tax_percent) }}</span>
              </div>
              <div class="totals-row">
                <span>{{ __('CGST') }}</span>
                <span>@{{ formatMoney(purchaseCgst) }}</span>
              </div>
              <div class="totals-row">
                <span>{{ __('SGST') }}</span>
                <span>@{{ formatMoney(purchaseSgst) }}</span>
              </div>
              <div class="totals-row">
                <span>{{ __('Round off') }}</span>
                <span>@{{ formatMoney(selected_purchase.round_off) }}</span>
              </div>
              <div class="totals-row is-grand">
                <span>{{ __('Grand Total') }}</span>
                <span>@{{ formatMoney(selected_purchase.total_purchase_amount) }}</span>
              </div>
            </div>

            <div v-if="selected_purchase.payment_summary && selected_purchase.payment_summary.length">
              <div class="detail-section-title">{{ __('Payment Summary') }}</div>
              <ul class="payment-list">
                <li v-for="(payment, payIndex) in selected_purchase.payment_summary" :key="payIndex">
                  <span>@{{ payment.payment_mode || '—' }}</span>
                  <span>@{{ formatMoney(payment.amount_taken) }}</span>
                </li>
                <li style="margin-top: 6px; padding-top: 8px; border-top: 1px solid #dedee4; font-weight: 700;">
                  <span>{{ __('Total Paid') }}</span>
                  <span>@{{ formatMoney(purchasePaidTotal) }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pagination" v-html="paginator.links"></div>
  </template>

  <template v-else>
    <div class="empty-state">
      <div><i class="fa fa-shopping-cart" aria-hidden="true"></i></div>
      <div><strong>{{ __('No purchases found') }}</strong></div>
      <div>{{ __('Try adjusting the date range or search text.') }}</div>
    </div>
  </template>
</div>

<script>
  var vue_list_tab_all = null;

  jQuery(function($) {
    vue_list_tab_all = new Vue({
      el: "#vue-list-tab-all",
      data: function() {
        return Object.assign({
          passwordInput: '',
          passwordRowId: null,
          passwordPurpose: '',
          passwordTargetUrl: '',
          selected_purchase: {
            purchase_ref_no: '',
            vendor_name: '',
            purchase_date: '',
            purchase_invoice_no: '',
            purchase_description: '',
            total_purchase_amount: 0,
            freight_amount: 0,
            freight_tax_percent: 0,
            round_off: 0,
            payment_method: '',
            payment_summary: [],
            products: []
          }
        }, {!! json_encode($db_results['vue_data']['vue_list_tab_all']) !!});
      },
      computed: {
        purchaseTaxableTotal: function() {
          var self = this;
          return (this.selected_purchase.products || []).reduce(function(sum, row) {
            return sum + self.lineAmount(row);
          }, 0);
        },
        purchaseTaxTotal: function() {
          return (this.selected_purchase.products || []).reduce(function(sum, row) {
            return sum + (parseFloat(row.tax_amount) || 0);
          }, 0);
        },
        purchaseFreightTax: function() {
          var freight = parseFloat(this.selected_purchase.freight_amount) || 0;
          var percent = parseFloat(this.selected_purchase.freight_tax_percent) || 0;
          return Math.round(freight * percent) / 100;
        },
        purchaseCgst: function() {
          var tax = (this.purchaseTaxTotal || 0) + (this.purchaseFreightTax || 0);
          return Math.round(tax * 50) / 100;
        },
        purchaseSgst: function() {
          var tax = (this.purchaseTaxTotal || 0) + (this.purchaseFreightTax || 0);
          return Math.round((tax - this.purchaseCgst) * 100) / 100;
        },
        purchasePaidTotal: function() {
          return (this.selected_purchase.payment_summary || []).reduce(function(sum, row) {
            return sum + (parseFloat(row.amount_taken) || 0);
          }, 0);
        }
      },
      methods: {
        formatMoney: function(value) {
          return Number(value || 0).toFixed(2);
        },
        lineAmount: function(product) {
          var qty = parseFloat(product.quantity) || 0;
          var rate = parseFloat(product.cost_price) || 0;
          if (product.discount_percent !== null && product.discount_percent !== undefined && product.discount_percent !== '') {
            var disc = parseFloat(product.discount_percent) || 0;
            return Math.round(qty * rate * (1 - disc / 100) * 100) / 100;
          }
          return Math.round(((parseFloat(product.purchase_total) || 0) - (parseFloat(product.tax_amount) || 0)) * 100) / 100;
        },
        discountLabel: function(product) {
          if (product.discount_percent !== null && product.discount_percent !== undefined && product.discount_percent !== '') {
            return Number(product.discount_percent).toFixed(2);
          }
          var gross = (parseFloat(product.quantity) || 0) * (parseFloat(product.cost_price) || 0);
          var discount = parseFloat(product.discount) || 0;
          if (gross > 0 && discount > 0) {
            return ((discount / gross) * 100).toFixed(2);
          }
          return '0.00';
        },
        showPurchaseDetails: function(item) {
          this.selected_purchase = {
            purchase_ref_no: item.purchase_ref_no,
            vendor_name: (item.vendor && item.vendor.name) || item.vendor_name || '',
            purchase_date: item.purchase_date,
            purchase_invoice_no: item.purchase_invoice_no,
            purchase_description: item.purchase_description || '',
            total_purchase_amount: item.total_purchase_amount || 0,
            freight_amount: item.freight_amount || 0,
            freight_tax_percent: item.freight_tax_percent || 0,
            round_off: item.round_off || 0,
            payment_method: item.payment_method || '',
            payment_summary: item.payment_summary || [],
            products: item.products || []
          };
          $('#purchaseDetailsModal').modal('show');
        },
        showPasswordInput: function(rowId, purpose, targetUrl) {
          this.passwordInput = '';
          this.passwordRowId = rowId;
          this.passwordPurpose = purpose || 'edit';
          this.passwordTargetUrl = targetUrl || '';
        },
        cancelPasswordInput: function() {
          this.passwordInput = '';
          this.passwordRowId = null;
          this.passwordPurpose = '';
          this.passwordTargetUrl = '';
        },
        submitPassword: function() {
          if (!this.passwordInput) {
            if (typeof js_notify === 'function') {
              js_notify('{{ __('Password is required.') }}', 'error');
            } else {
              alert('{{ __('Password is required.') }}');
            }
            return;
          }

          var self = this;
          var password = this.passwordInput;
          var targetUrl = this.passwordTargetUrl;
          var purpose = this.passwordPurpose || 'edit';

          $.ajax({
            url: "{{ route('purchase.auth-check') }}",
            method: 'POST',
            data: {
              password: password,
              purpose: purpose,
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
              if (response.valid) {
                self.cancelPasswordInput();
                window.location.href = targetUrl;
              } else {
                if (typeof js_notify === 'function') {
                  js_notify(response.statusText || '{{ __('Invalid password.') }}', 'error');
                } else {
                  alert(response.statusText || '{{ __('Invalid password.') }}');
                }
              }
            },
            error: function(xhr) {
              var msg = (xhr.responseJSON && xhr.responseJSON.statusText)
                ? xhr.responseJSON.statusText
                : '{{ __('An error occurred while checking the password.') }}';
              if (typeof js_notify === 'function') {
                js_notify(msg, 'error');
              } else {
                alert(msg);
              }
            }
          });
        }
      }
    });
  });
</script>
