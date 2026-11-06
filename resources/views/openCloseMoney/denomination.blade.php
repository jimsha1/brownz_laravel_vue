@extends('layouts.vcapp')

@section('content')
<style>
  .denom-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .denom-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .denom-list .status-pill,
  .denom-form .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .denom-list .status-pill.is-on,
  .denom-form .status-pill.is-on {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .denom-list .status-pill.is-off,
  .denom-form .status-pill.is-off {
    color: #6c757d;
    background: rgba(108, 117, 125, 0.12);
  }

  .denom-list .manage-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
  }

  .denom-list .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #F0F0F3;
    color: #1D1D1D;
    border: none;
    cursor: pointer;
  }

  .denom-list .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .denom-list .manage-btn.is-danger:hover {
    background: #D0342C;
    color: #fff;
  }

  .denom-list .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .denom-form .edit-section {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px 16px 6px;
  }

  .denom-form .edit-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .denom-form .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: -2px 0 12px;
  }

  .denom-form .form-control {
    border-radius: 999px;
    background: #fff;
    border: none;
    height: 42px;
    padding: 8px 16px;
  }

  .denom-setting-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .denom-setting-card .setting-copy {
    flex: 1 1 240px;
  }

  .denom-setting-card .setting-title {
    font-weight: 600;
    color: #1D1D1D;
    margin: 0 0 4px;
  }

  .denom-setting-card .setting-hint {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
  }
</style>

@php
  $isNeed = (int) ($is_need ?? 0);
@endphp

<div class="fbox-shadow mt-2 fd-card respo-margin-top denom-form">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Denominations') }}
    </h5>

    <div class="edit-section mb-3">
      <p class="edit-section-title">{{ __('Close Register Setting') }}</p>
      <div class="denom-setting-card">
        <div class="setting-copy">
          <p class="setting-title">{{ __('Require denomination counts when closing') }}</p>
          <p class="setting-hint">
            {{ $isNeed === 1
              ? __('Cashiers must enter denomination quantities to close the register.')
              : __('Cashiers can close without denomination line counts.') }}
          </p>
        </div>
        <form method="POST" action="{{ route('denomination.permission') }}" class="d-flex align-items-center gap-2">
          @csrf
          <input type="hidden" name="is_need" value="{{ $isNeed === 1 ? 0 : 1 }}">
          <span class="status-pill {{ $isNeed === 1 ? 'is-on' : 'is-off' }}">
            {{ $isNeed === 1 ? __('Enabled') : __('Disabled') }}
          </span>
          <button type="submit" class="btn btn-sm btn_dark-x">
            {{ $isNeed === 1 ? __('Disable') : __('Enable') }}
          </button>
        </form>
      </div>
    </div>

    <div class="edit-section">
      <p class="edit-section-title" id="denom-form-title">{{ __('Add Denomination') }}</p>
      <p class="edit-hint" id="denom-form-hint">{{ __('Set a label and face value used during close register.') }}</p>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0 pl-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('denomination') }}" id="denominationForm">
        @csrf
        <input type="hidden" name="denomination_id" id="denomination_id" value="{{ old('denomination_id', '') }}">
        <div class="row">
          <div class="form-group col-md-6">
            <label for="title">{{ __('Title') }}</label>
            <input type="text" required name="title" id="title" class="form-control"
              value="{{ old('title') }}" placeholder="{{ __('e.g. 100 Note') }}">
          </div>
          <div class="form-group col-md-6">
            <label for="denomination">{{ __('Value') }}</label>
            <input type="number" required step="0.01" min="0.01" name="denomination" id="denomination"
              class="form-control" value="{{ old('denomination') }}"
              placeholder="{{ __('e.g. 100') }}">
          </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mb-2">
          <button type="button" class="btn brownz-btn-yellow-d d-none" id="cancelEditBtn">
            <i class="fa fa-undo"></i> {{ __('Cancel Edit') }}
          </button>
          <button type="submit" class="btn btn_dark-x" id="denomSubmitBtn">
            <i class="fa fa-check"></i> <span id="denomSubmitLabel">{{ __('Save') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="fbox-shadow mt-2 fd-card denom-list">
  <div class="card-body">
    <div class="list-toolbar">
      <h5 class="list-title">{{ __('Denomination List') }}</h5>
    </div>

    @if ($denomination->count())
      <div class="table-responsive">
        <table class="table brownz-table-list-v1 m-0">
          <thead>
            <tr>
              <th style="width: 56px;">{{ __('SL#') }}</th>
              <th>{{ __('Title') }}</th>
              <th>{{ __('Value') }}</th>
              <th class="text-center" style="width: 110px;">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($denomination as $money)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                  <strong style="color:#1D1D1D;">{{ $money->title }}</strong>
                </td>
                <td>{{ number_format((float) $money->denomination, 2, '.', '') }}</td>
                <td class="text-center">
                  <div class="manage-actions">
                    <button type="button"
                      class="manage-btn edit-denom-btn"
                      title="{{ __('Edit') }}"
                      data-id="{{ $money->denomination_id }}"
                      data-title="{{ $money->title }}"
                      data-denomination="{{ number_format((float) $money->denomination, 2, '.', '') }}">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>
                    <form method="GET" action="{{ route('delete.denomination') }}"
                      onsubmit="return confirm('{{ __('Delete this denomination?') }}');">
                      <input type="hidden" name="denomination_id" value="{{ $money->denomination_id }}">
                      <button type="submit" class="manage-btn is-danger" title="{{ __('Delete') }}">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="empty-state">
        <div><i class="fa fa-money" aria-hidden="true"></i></div>
        <div><strong>{{ __('No denominations found') }}</strong></div>
        <div>{{ __('Add denomination values used when closing the register.') }}</div>
      </div>
    @endif
  </div>
</div>

<script>
  jQuery(function ($) {
    function resetDenomForm() {
      $('#denomination_id').val('');
      $('#title').val('');
      $('#denomination').val('');
      $('#denom-form-title').text('{{ __('Add Denomination') }}');
      $('#denom-form-hint').text('{{ __('Set a label and face value used during close register.') }}');
      $('#denomSubmitLabel').text('{{ __('Save') }}');
      $('#cancelEditBtn').addClass('d-none');
    }

    $(document).on('click', '.edit-denom-btn', function () {
      var $btn = $(this);
      $('#denomination_id').val($btn.attr('data-id') || '');
      $('#title').val($btn.attr('data-title') || '');
      $('#denomination').val($btn.attr('data-denomination') || '');
      $('#denom-form-title').text('{{ __('Edit Denomination') }}');
      $('#denom-form-hint').text('{{ __('Update the label or face value, then save.') }}');
      $('#denomSubmitLabel').text('{{ __('Update') }}');
      $('#cancelEditBtn').removeClass('d-none');
      $('#title').trigger('focus');
      $('html, body').animate({ scrollTop: $('#denominationForm').offset().top - 80 }, 250);
    });

    $('#cancelEditBtn').on('click', function () {
      resetDenomForm();
    });
  });
</script>
@endsection
