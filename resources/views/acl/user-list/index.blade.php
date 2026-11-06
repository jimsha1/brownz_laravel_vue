@extends('layouts.vcapp')

@section('content')
    <div class="fbox-shadow mt-2 fd-card respo-margin-top">
        <div class="card-body">
            <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
                <input type="hidden" id="tab_search" name="tab_search" value="0">

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-3 col-sm-8">
                        <div class="search-box-bar-container jus-heigh-search-b ">
                            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
                            <input type="text" class="form-control brawnz-search-bar-in" name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search...') }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-8">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select name="role" class="form-control brawnz-search-bar-in">
                                <option value="">{{ __('All Roles') }}</option>
                                @foreach (($roles ?? []) as $roleName)
                                    <option value="{{ $roleName }}" {{ request('role') === $roleName ? 'selected' : '' }}>
                                        {{ $roleName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-8">
                        @php $selectedStatus = request('status', 'active'); @endphp
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select name="status" class="form-control brawnz-search-bar-in">
                                <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
                                <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-4 d-flex gap-2">

                        <button type="submit" class="btn btn-sm btn-primary btn_dark-x mr-2">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>

                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" onclick="refreshPage()">
                            <i class="fa fa-refresh"></i> {{ __('Refresh') }}
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('global-ajax-tab-frame.tabbed-frame')
@endsection

@push('inline-scripts')
    @if ($activate_via_js === true)
        <script>
            jQuery(function($) {
                $("#{{ 'vtab-' . $tab_name }}").tab("show");
            });
        </script>
    @endif
    <script type="text/javascript">
        function delete_user(user_id) {
            if (confirm("Do you want to delete this user?")) {
                window.location.href = '/acl/user/delete/' + user_id;
            }
        }

        function changestatus(user_id, status) {
            if (confirm("Do you want tochange this user status?")) {
                window.location.href = '/acl/user/changestatus/' + user_id + '/' + status;
            }
        }
    </script>
    <script>
        function refreshPage() {
            window.location.href = "{{ route('tab.acl.user.list') }}";
        }
    </script>
@endpush
