<style>
  #vue-account-transactions.account-transactions {
    padding: 4px 0 8px;
  }

  #vue-account-transactions .txn-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
  }

  #vue-account-transactions .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  #vue-account-transactions .search-box-bar-container {
    display: flex;
    align-items: center;
    background-color: #FFFFFF;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
    border-radius: 26px;
    min-width: 220px;
    padding-left: 16px;
    min-height: 44px;
  }

  #vue-account-transactions .brawnz-search-bar-in {
    background-color: transparent;
    border: none;
    box-shadow: none;
    outline: none;
    padding: 10px 16px 10px 8px;
    font-size: 14px;
    width: 100%;
    height: 44px;
  }

  #vue-account-transactions .running-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #F7F7F9;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
  }

  #vue-account-transactions .running-pill .label {
    color: #6c757d;
    font-weight: 600;
  }

  #vue-account-transactions .amount-pill {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    min-width: 88px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 10px;
    line-height: 1.2;
    font-variant-numeric: tabular-nums;
  }

  #vue-account-transactions .amount-pill.is-debit {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-account-transactions .amount-pill.is-credit {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-account-transactions .amount-pill.is-balance {
    color: #1D1D1D;
    background: #F0F0F3;
  }

  #vue-account-transactions .amount-pill.is-empty {
    color: #9aa0a6;
    background: transparent;
    padding: 0;
    min-width: 0;
  }

  #vue-account-transactions .txn-table th,
  #vue-account-transactions .txn-table td {
    vertical-align: middle;
  }

  #vue-account-transactions .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  #vue-account-transactions .empty-state i {
    font-size: 28px;
    margin-bottom: 10px;
    color: #b7b7b7;
  }

  #vue-account-transactions .loading-state {
    text-align: center;
    padding: 28px 16px;
    color: #6c757d;
  }
</style>

<div id="vue-account-transactions" class="account-transactions">
  <div class="txn-toolbar">
    <div>
      <label class="filter-label" for="financial_year_id">{{ __('Financial Year') }}</label>
      <div class="search-box-bar-container">
        <select id="financial_year_id" class="form-control brawnz-search-bar-in"
          v-model="selectedFinancialYear" @change="loadTransactions">
          <option v-for="fy in financialYears" :key="fy.id" :value="fy.id">
            @{{ fy.label }}
          </option>
        </select>
      </div>
    </div>

    <div class="running-pill">
      <span class="label">{{ __('Running Balance') }}</span>
      <span>@{{ Number(running_balance || 0).toFixed(2) }}</span>
    </div>
  </div>

  <div v-if="loading" class="loading-state">
    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i> {{ __('Loading transactions...') }}
  </div>

  <template v-else-if="transactions && transactions.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 txn-table">
        <thead>
          <tr>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Voucher No') }}</th>
            <th class="text-right">{{ __('Debit') }}</th>
            <th class="text-right">{{ __('Credit') }}</th>
            <th class="text-right">{{ __('Balance') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaction in transactions" :key="transaction.id">
            <td>@{{ transaction.date }}</td>
            <td>@{{ transaction.transaction_group ? transaction.transaction_group.description : '—' }}</td>
            <td>@{{ transaction.transaction_group ? transaction.transaction_group.voucher_no : '—' }}</td>
            <td class="text-right">
              <span v-if="Number(transaction.debit) > 0" class="amount-pill is-debit">
                @{{ Number(transaction.debit).toFixed(2) }}
              </span>
              <span v-else class="amount-pill is-empty">—</span>
            </td>
            <td class="text-right">
              <span v-if="Number(transaction.credit) > 0" class="amount-pill is-credit">
                @{{ Number(transaction.credit).toFixed(2) }}
              </span>
              <span v-else class="amount-pill is-empty">—</span>
            </td>
            <td class="text-right">
              <span class="amount-pill is-balance">
                @{{ Number(transaction.running_balance).toFixed(2) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <div v-else class="empty-state">
    <div><i class="fa fa-exchange" aria-hidden="true"></i></div>
    <div><strong>{{ __('No transactions found') }}</strong></div>
    <div>{{ __('Try another financial year.') }}</div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    new Vue({
        el: '#vue-account-transactions',

        data: Object.assign(
            <?php echo json_encode($details_info_vue); ?>,
            { loading: false }
        ),

        methods: {
            loadTransactions() {
                var app = this;
                app.loading = true;

                axios.post(
                        "{{ route('tab.accounts.account.details.transactions.filter', $details_info_vue['account']->account_id) }}", {
                            financial_year_id: app.selectedFinancialYear
                        }
                    )
                    .then(function(response) {
                        app.transactions = response.data.transactions;
                        app.running_balance = response.data.running_balance;
                    })
                    .catch(function(error) {
                        console.log(error);
                    })
                    .finally(function() {
                        app.loading = false;
                    });
            }
        }
    });
</script>
