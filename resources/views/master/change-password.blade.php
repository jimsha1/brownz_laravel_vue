@extends('layouts.vcapp')

@section('content')
<div class="card fbox-shadow mt-5 fd-card respo-margin-top">
  <div class="card-body">
    <div id="vue-details-tab-edit" class="brw-form-la">
      <form class="voco-ajax-form" method="post" action="{{ route('admin.save-password') }}">
        @csrf
        
        <div class="form-group row">
          <label for="current-password" class="col-sm-2 col-form-label">
            Current Password
          </label>
          <div class="col-sm-10">
            <input type="password" class="form-control" id="current_password" name="current_password" value="">
          </div>
        </div>

        <div class="form-group row">
          <label for="new-password" class="col-sm-2 col-form-label">
            New Password
          </label>
          <div class="col-sm-10">
            <input type="password" class="form-control" id="password" name="password" value="">
          </div>
        </div>

        <div class="form-group row">
          <label for="new-password" class="col-sm-2 col-form-label">
            Confirm Password
          </label>
          <div class="col-sm-10">
            <input type="password" class="form-control" id="confirm_password" name="password_confirmation" value="">
          </div>
        </div>

        <div class="vc-form-buttons">
          <a class="btn btn-secondary vc-form-btn-cancel mr-2 brownz-btn-yellow-d" href="{{ route('dashboard') }}">
            <i class="fa fa-undo"></i> {{ __('Cancel') }}
          </a>
          <button type="submit" class="btn btn-primary btn_dark-x">
            <i class="fa fa-check"></i> {{ __('Save') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
