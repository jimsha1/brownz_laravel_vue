@extends('layouts.vcapp')

@section('content')
<style>
  .company-profile-page .page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .company-profile-page .page-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 6px;
  }

  .company-profile-page .page-subtitle {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
    max-width: 720px;
  }

  .company-profile-page .profile-section {
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #ececf1;
  }

  .company-profile-page .profile-section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }

  .company-profile-page .section-title {
    color: #1D1D1D;
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 4px;
  }

  .company-profile-page .section-description {
    margin: 0 0 18px;
    color: #6c757d;
    font-size: 13px;
  }

  .company-profile-page .form-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .company-profile-page .form-group .invalid-feedback {
    display: block;
    margin-top: 6px;
    font-size: 12px;
  }

  .company-profile-page .form-control,
  .company-profile-page textarea.form-control {
    border: none;
    outline: none;
    border-radius: 999px;
    background: #F0F0F3;
    padding: 10px 16px;
    font-size: 14px;
    color: #1D1D1D;
    min-height: 42px;
    box-shadow: none;
  }

  .company-profile-page textarea.form-control {
    border-radius: 16px;
    min-height: 96px;
    resize: vertical;
  }

  .company-profile-page .form-control.is-invalid {
    background: #fdecea;
  }

  .company-profile-page .form-control:focus {
    background: #e8e8ed;
    box-shadow: none;
  }

  .company-profile-page .field-hint {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #8a8f98;
  }

  .company-profile-page .field-required {
    color: #D0342C;
    margin-left: 2px;
  }

  .company-profile-page .empty-state {
    text-align: center;
    padding: 48px 24px;
    color: #6c757d;
  }

  .company-profile-page .empty-state i {
    font-size: 2rem;
    margin-bottom: 12px;
    color: #adb5bd;
  }

  .company-profile-page .form-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #ececf1;
  }
</style>

<div class="company-profile-page">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ __('Business Profile') }}</h1>
          <p class="page-subtitle">
            {{ __('Manage your company details, contact information, payment settings, and payroll defaults.') }}
          </p>
        </div>
      </div>

      @if ($business_configs->isEmpty())
        <div class="empty-state">
          <i class="fa fa-building-o" aria-hidden="true"></i>
          <p>{{ __('No business profile fields are configured yet.') }}</p>
        </div>
      @else
        <div id="vue-details-tab-edit" class="brw-form-la">
          <form class="voco-ajax-form" method="post" action="{{ route('admin.business.profile.save') }}">
            @csrf

            @foreach ($config_groups as $group)
              @php
                $groupConfigs = collect($group['keys'])
                    ->filter(fn ($key) => isset($business_configs[$key]))
                    ->map(fn ($key) => $business_configs[$key]);
              @endphp

              @if ($groupConfigs->isNotEmpty())
                <section class="profile-section">
                  <h2 class="section-title">{{ $group['title'] }}</h2>
                  <p class="section-description">{{ $group['description'] }}</p>

                  <div class="row">
                    @foreach ($groupConfigs as $config)
                      @php
                        $label = $config_labels[$config->config_key] ?? ucwords(str_replace('_', ' ', $config->config_key));
                        $hint = $config_hints[$config->config_key] ?? null;
                        $isRequired = str_contains($config->validation ?? '', 'required');
                        $isTextarea = $config->input_type === 'textarea';
                        $columnClass = ($isTextarea ? 'col-12 mb-3' : 'col-md-6 mb-3').' form-group';
                      @endphp

                      <div class="{{ $columnClass }}">
                        <label for="{{ $config->config_key }}" class="form-label">
                          {{ $label }}
                          @if ($isRequired)
                            <span class="field-required">*</span>
                          @endif
                        </label>

                        @if ($isTextarea)
                          <textarea
                            class="form-control"
                            id="{{ $config->config_key }}"
                            name="{{ $config->config_key }}"
                            rows="4"
                          >{{ old($config->config_key, $config->config_value) }}</textarea>
                        @else
                          <input
                            type="text"
                            class="form-control"
                            id="{{ $config->config_key }}"
                            name="{{ $config->config_key }}"
                            value="{{ old($config->config_key, $config->config_value) }}"
                          >
                        @endif

                        @if ($hint)
                          <small class="field-hint">{{ $hint }}</small>
                        @endif
                      </div>
                    @endforeach
                  </div>
                </section>
              @endif
            @endforeach

            @php
              $groupedKeys = collect($config_groups)->flatMap(fn ($group) => $group['keys']);
              $ungroupedConfigs = $business_configs->reject(fn ($config) => $groupedKeys->contains($config->config_key));
            @endphp

            @if ($ungroupedConfigs->isNotEmpty())
              <section class="profile-section">
                <h2 class="section-title">{{ __('Other Settings') }}</h2>
                <p class="section-description">{{ __('Additional business configuration fields.') }}</p>

                <div class="row">
                  @foreach ($ungroupedConfigs as $config)
                    @php
                      $label = $config_labels[$config->config_key] ?? ucwords(str_replace('_', ' ', $config->config_key));
                      $hint = $config_hints[$config->config_key] ?? null;
                      $isRequired = str_contains($config->validation ?? '', 'required');
                      $isTextarea = $config->input_type === 'textarea';
                      $columnClass = ($isTextarea ? 'col-12 mb-3' : 'col-md-6 mb-3').' form-group';
                    @endphp

                    <div class="{{ $columnClass }}">
                      <label for="{{ $config->config_key }}" class="form-label">
                        {{ $label }}
                        @if ($isRequired)
                          <span class="field-required">*</span>
                        @endif
                      </label>

                      @if ($isTextarea)
                        <textarea
                          class="form-control"
                          id="{{ $config->config_key }}"
                          name="{{ $config->config_key }}"
                          rows="4"
                        >{{ old($config->config_key, $config->config_value) }}</textarea>
                      @else
                        <input
                          type="text"
                          class="form-control"
                          id="{{ $config->config_key }}"
                          name="{{ $config->config_key }}"
                          value="{{ old($config->config_key, $config->config_value) }}"
                        >
                      @endif

                      @if ($hint)
                        <small class="field-hint">{{ $hint }}</small>
                      @endif
                    </div>
                  @endforeach
                </div>
              </section>
            @endif

            <div class="form-actions vc-form-buttons">
              <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d mr-2"
                href="{{ route('dashboard') }}">
                <i class="fa fa-undo"></i> {{ __('Cancel') }}
              </a>
              <button type="submit" class="btn btn-primary btn_dark-x">
                <i class="fa fa-check"></i> {{ __('Save Changes') }}
              </button>
            </div>
          </form>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
