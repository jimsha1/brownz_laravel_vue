@extends('layouts.vcapp')

@section('content')
<style>
    .beautician-calendar-search .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .beautician-calendar-search .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
    }

    .beautician-calendar-list .user-name {
        font-weight: 600;
        color: #1D1D1D;
    }

    .beautician-calendar-list .user-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .beautician-calendar-list .btn-view-calendar {
        border-radius: 20px;
        padding: 6px 16px;
        white-space: nowrap;
    }

    .beautician-calendar-list .empty-state {
        text-align: center;
        padding: 40px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .beautician-calendar-list .empty-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }
</style>

<div class="fbox-shadow mt-2 fd-card respo-margin-top beautician-calendar-search">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
            <h5 class="mb-0" style="color: #1D1D1D; font-size: 1.53125rem;">{{ __('Beautician Calendar') }}</h5>
            <small class="text-muted">{{ __('Active beauticians only') }}</small>
        </div>

        @if (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif

        <form method="get" action="{{ route('all.beatician.calender') }}">
            <div class="row align-items-end br-row-gap-2">
                <div class="col-lg-4 col-md-6">
                    <label for="name">{{ __('Search') }}</label>
                    <div class="search-box-bar-container jus-heigh-search-b">
                        <p class="mb-0"><i class="fa fa-search" aria-hidden="true"></i></p>
                        <input type="text"
                            id="name"
                            name="name"
                            class="form-control brawnz-search-bar-in"
                            value="{{ $searchName ?? '' }}"
                            placeholder="{{ __('Name, username or code...') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="role">{{ __('Role') }}</label>
                    <div class="search-box-bar-container jus-heigh-search-b">
                        <select id="role" name="role" class="form-control brawnz-search-bar-in">
                            <option value="">{{ __('All Roles') }}</option>
                            @foreach (($roles ?? []) as $roleOption)
                                <option value="{{ $roleOption }}" {{ ($roleName ?? '') === $roleOption ? 'selected' : '' }}>
                                    {{ $roleOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <button type="submit" class="btn btn-sm btn-primary btn_dark-x mr-2">
                        <i class="fa fa-search"></i> {{ __('Search') }}
                    </button>
                    <a href="{{ route('all.beatician.calender') }}" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
                        <i class="fa fa-refresh"></i> {{ __('Reset') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="fbox-shadow mt-3 fd-card respo-margin-top beautician-calendar-list">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0" style="color: #1D1D1D;">{{ __('Beautician List') }}</h5>
            <small class="text-muted">
                {{ __('Showing') }}
                {{ $user_records->firstItem() ?? 0 }}–{{ $user_records->lastItem() ?? 0 }}
                {{ __('of') }}
                {{ $user_records->total() }}
            </small>
        </div>

        @if ($user_records->count())
            <div class="table-responsive">
                <table class="table brownz-table-list-v1 mt-0 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">{{ __('SL#') }}</th>
                            <th>{{ __('Beautician') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Mobile') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th class="text-right" style="width: 140px;">{{ __('Calendar') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user_records as $user)
                            <tr>
                                <td>{{ $user_records->firstItem() + $loop->index }}</td>
                                <td>
                                    <span class="user-name">{{ $user->user_name }}</span>
                                </td>
                                <td>
                                    @if (!empty($user->beautician_code))
                                        <span class="badge badge-light border">{{ $user->beautician_code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($user->user_mobile))
                                        <a href="tel:{{ $user->user_mobile }}">{{ $user->user_mobile }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($user->role_names) && count($user->role_names))
                                        @foreach ($user->role_names as $roleName)
                                            <span class="badge badge-info text-white mr-1 mb-1">{{ $roleName }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('beatician.calender', ['user_id' => $user->user_id]) }}"
                                        class="btn btn-sm brownz-btn-yellow-d btn-view-calendar">
                                        <i class="fa fa-calendar"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {!! $user_records->links() !!}
            </div>
        @else
            <div class="empty-state">
                <div><i class="fa fa-calendar-o" aria-hidden="true"></i></div>
                <div><strong>{{ __('No beauticians found') }}</strong></div>
                <div>{{ __('Try a different name, username, or beautician code.') }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
