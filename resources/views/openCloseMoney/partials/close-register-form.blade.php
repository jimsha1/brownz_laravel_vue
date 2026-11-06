{{-- Shared Close Register / Force Close denomination form --}}
@php
    $total_count = count($denomination ?? []);
    $openFloat = $float_amonunt[0] ?? null;
    $openAmountValue = $openFloat->amonut ?? 0;
    $resolvedFloatAmtId = $float_amt_id ?? ($openFloat->float_amt_id ?? '');
    $isForceClose = !empty($is_force_close);
    $pageTitle = $page_title ?? ($isForceClose ? __('Force Close Register') : __('Close Register'));
    $pageHint = $page_hint ?? ($isForceClose
        ? __('Enter cash denomination counts to force-close this float session.')
        : __('Enter cash denomination counts to close the register.'));
    $redirectAfterClose = $redirect_after_close ?? ($isForceClose ? url('/re_print_float_close_amt') : url('/float_amt'));
    $cashierName = $cashier_name ?? null;
    $isEditClose = !empty($is_edit_close);
    $formAction = $form_action ?? route('close.float_amt');
    $submitLabel = $submit_label ?? __('Close Register');
    $submitIcon = $submit_icon ?? 'fa-lock';
    $statusLabel = $status_label ?? __('Register Open');
    $statusClass = $status_class ?? 'is-open';
    $summaryHint = $summary_hint ?? __('Review totals, then close the register.');
    $prefillTotal = $prefill_total ?? null;
@endphp

<style>
  .close-register-page .page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .close-register-page .page-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .close-register-page .page-hint {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .close-register-page .meta-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .close-register-page .meta-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .close-register-page .meta-pill.is-force {
    color: #B56A00;
    background: rgba(230, 205, 127, 0.45);
  }

  .close-register-page .meta-pill.is-open {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .close-register-page .layout-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 16px;
    align-items: start;
  }

  .close-register-page .panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px;
  }

  .close-register-page .panel-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin: 0 0 4px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .close-register-page .panel-hint {
    font-size: 12px;
    color: #6c757d;
    margin: 0 0 14px;
  }

  .close-register-page .denom-table {
    margin-bottom: 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
  }

  .close-register-page .denom-table th {
    white-space: nowrap;
    vertical-align: middle;
    border-top: none;
    font-size: 13px;
  }

  .close-register-page .denom-table td {
    vertical-align: middle;
    border-top: 1px solid #ececf0;
  }

  .close-register-page .denom-name {
    font-weight: 600;
    color: #1D1D1D;
  }

  .close-register-page .denom-sub {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .close-register-page .qty-input {
    width: 96px;
    max-width: 100%;
    height: 38px;
    border: none;
    outline: none;
    border-radius: 999px;
    background: #F0F0F3;
    padding: 8px 14px;
    font-size: 14px;
    color: #1D1D1D;
  }

  .close-register-page .row-total {
    font-weight: 600;
    color: #1D1D1D;
  }

  .close-register-page .summary-box {
    background: #fff;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 12px;
  }

  .close-register-page .summary-label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .close-register-page .summary-input {
    width: 100%;
    border: none;
    outline: none;
    background: #F0F0F3;
    border-radius: 999px;
    padding: 10px 14px;
    font-size: 15px;
    font-weight: 600;
    color: #1D1D1D;
  }

  .close-register-page .summary-input:disabled {
    opacity: 1;
    color: #1D1D1D;
  }

  .close-register-page .edit-reason {
    width: 100%;
    border: none;
    outline: none;
    background: #F0F0F3;
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 14px;
    color: #1D1D1D;
    resize: vertical;
    min-height: 84px;
  }

  .close-register-page .action-row {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 8px;
  }

  .close-register-page .action-row .btn {
    width: 100%;
  }

  .close-register-page .loading-text {
    display: none;
    text-align: center;
    font-size: 13px;
    color: #6c757d;
  }

  @media (max-width: 991px) {
    .close-register-page .layout-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="close-register-page">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="page-header">
        <div>
          <h5 class="page-title">{{ $pageTitle }}</h5>
          <p class="page-hint">{{ $pageHint }}</p>
          @if($cashierName || $resolvedFloatAmtId)
            <p class="page-hint mt-1 mb-0">
              @if($cashierName)
                {{ __('Cashier') }}: {{ $cashierName }}
              @endif
              @if($cashierName && $resolvedFloatAmtId)
                ·
              @endif
              @if($resolvedFloatAmtId)
                {{ __('Float ID') }}: #{{ $resolvedFloatAmtId }}
              @endif
            </p>
          @endif
        </div>
        <div class="meta-pills">
          @if($isForceClose)
            <span class="meta-pill is-force">{{ __('Force Close') }}</span>
          @endif
          <span class="meta-pill {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ $formAction }}" id="close-amt" class="mt-2">
    @csrf
    @if($isEditClose)
      <input type="hidden" name="edit_closed" value="1">
    @endif
    <div class="layout-grid">
      <div class="fbox-shadow fd-card">
        <div class="card-body">
          <div class="panel">
            <p class="panel-title">{{ __('Currency Media Counts') }}</p>
            <p class="panel-hint">{{ __('Enter the count for each denomination. Totals update automatically.') }}</p>

            <div class="table-responsive">
              <table class="table brownz-table-list-v1 denom-table">
                <thead>
                  <tr>
                    <th>{{ __('Denomination') }}</th>
                    <th style="width: 120px;">{{ __('Count') }}</th>
                    <th style="width: 120px;">{{ __('Total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach (($denomination ?? []) as $deno)
                    <tr>
                      <td>
                        <span class="denom-name">{{ $deno->title }}</span>
                        <span class="denom-sub">{{ $deno->denomination }}</span>
                        <input type="hidden" name="title_{{ $loop->iteration }}" value="{{ $deno->title }}">
                        <input type="hidden" name="denomination_{{ $loop->iteration }}"
                          id="denomination_{{ $loop->iteration }}"
                          value="{{ $deno->denomination }}">
                      </td>
                      <td>
                        @php
                          $prefillQty = $deno->prefill_qty ?? '';
                        @endphp
                        @if (isset($openFloat->is_closed) && (int) $openFloat->is_closed === 1 && (int) $is_need_table === 0)
                          <input onchange="amountCalculation({{ $loop->iteration }}, this.value)"
                            onkeyup="amountCalculation({{ $loop->iteration }}, this.value)"
                            inputmode="numeric"
                            type="number"
                            name="qty_{{ $loop->iteration }}"
                            class="qty-input"
                            min="0"
                            placeholder="0"
                            value="{{ $prefillQty }}"
                            disabled>
                        @else
                          <input onchange="amountCalculation({{ $loop->iteration }}, this.value)"
                            onkeyup="amountCalculation({{ $loop->iteration }}, this.value)"
                            inputmode="numeric"
                            type="number"
                            name="qty_{{ $loop->iteration }}"
                            class="qty-input"
                            min="0"
                            placeholder="0"
                            value="{{ $prefillQty }}">
                        @endif
                      </td>
                      <td>
                        <span class="row-total" id="total_{{ $loop->iteration }}">0</span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <input type="hidden" id="total_count" name="total_count" value="{{ $total_count }}">
          </div>
        </div>
      </div>

      <div class="fbox-shadow fd-card">
        <div class="card-body">
          <div class="panel">
            <p class="panel-title">{{ __('Summary') }}</p>
            <p class="panel-hint">{{ $summaryHint }}</p>

            <div class="summary-box">
              <span class="summary-label" for="openamount">{{ __('Open Amount') }}</span>
              <input type="text" id="openamount" name="openamount"
                value="{{ $openAmountValue }}"
                class="summary-input" disabled>
            </div>

            <div class="summary-box">
              <span class="summary-label" for="total_amount">{{ __('Grand Total') }}</span>
              @if ((int) $is_need_table === 1)
                <input type="number" id="total_amount" class="summary-input" value="{{ $prefillTotal ?? 0 }}" disabled>
                <input type="hidden" id="total_amount_1" name="total_amount" value="{{ $prefillTotal ?? 0 }}">
                <input type="hidden" name="denomination_Type" value="1">
              @else
                <input type="number" id="total_amount" class="summary-input" name="total_amount" value="{{ $prefillTotal ?? '' }}">
                <input type="hidden" name="denomination_Type" value="2">
              @endif
            </div>

            @if($isEditClose)
              <div class="summary-box">
                <label class="summary-label" for="edit_reason">
                  {{ __('Edit Reason') }} <span class="text-danger">*</span>
                </label>
                <textarea id="edit_reason"
                  name="edit_reason"
                  class="edit-reason"
                  rows="3"
                  maxlength="1000"
                  required
                  placeholder="{{ __('Why is this closed count being updated?') }}"></textarea>
              </div>
            @endif

            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="float_amt_id" value="{{ $resolvedFloatAmtId }}">

            <div class="action-row">
              <button type="button" id="close_save_btn" onclick="refresh(event)" class="btn btn-primary btn_dark-x">
                <i class="fa {{ $submitIcon }}"></i> {{ $submitLabel }}
              </button>
              <a href="{{ ($isForceClose || $isEditClose) ? route('re.print.bill') : route('float_amt') }}"
                class="btn brownz-btn-yellow-d">
                <i class="fa fa-undo"></i> {{ __('Cancel') }}
              </a>
              <div id="close_loading_spinner" class="loading-text">{{ __('Processing...') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  function amountCalculation(index_val, qty) {
    var grand_total = 0;
    var total_count = $('#total_count').val();
    var indexVal = index_val;
    var id = "#denomination_" + indexVal;
    var total_by_raw = "#total_" + indexVal;
    var denomination = $(id).val();
    var amount = parseFloat(denomination) || 0;
    var qtyVal = parseFloat(qty) || 0;
    var total_amt = amount * qtyVal;
    $(total_by_raw).text(total_amt);

    for (var i = 1; i <= total_count; i++) {
      var rowTotal = parseFloat($("#total_" + i).text()) || 0;
      grand_total += rowTotal;
    }

    $('#total_amount').val(grand_total);
    $('#total_amount_1').val(grand_total);
  }

  function refresh(e) {
    e.preventDefault();

    var $reason = $('#edit_reason');
    if ($reason.length) {
      var reason = ($reason.val() || '').trim();
      $reason.val(reason);
      if (!reason) {
        alert(@json(__('Please enter a reason for this edit.')));
        $reason.trigger('focus');
        return;
      }
    }

    $('#close_loading_spinner').show();
    $('#close_save_btn').prop('disabled', true);

    $.ajax({
      url: $('#close-amt').attr('action'),
      type: 'post',
      data: $('#close-amt').serialize(),
      success: function (response) {
        window.open(
          response,
          "",
          "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
        );

        setTimeout(function () {
          window.location.href = @json($redirectAfterClose);
        }, 1000);
      },
      error: function (xhr) {
        var message = (xhr.responseText && xhr.responseText.trim())
          ? xhr.responseText
          : '{{ __("Something went wrong. Please try again.") }}';
        alert(message);
      },
      complete: function () {
        $('#close_loading_spinner').hide();
        $('#close_save_btn').prop('disabled', false);
      }
    });
  }

  jQuery(function () {
    $('.qty-input').each(function () {
      var qty = $(this).val();
      if (qty !== '' && qty !== null) {
        var index = String($(this).attr('name') || '').replace('qty_', '');
        if (index) {
          amountCalculation(index, qty);
        }
      }
    });
  });
</script>
