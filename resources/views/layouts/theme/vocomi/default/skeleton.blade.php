<?php
// header_remove('X-Powered-By');
?>
<!doctype html>
<html lang="{{ Session::get('user_session_locale') }}" {!! Session::get('user_session_locale') === 'ar' ? ' dir="rtl"' : '' !!}>

<head>
    @include('layouts.theme.vocomi.default.partials.head-loader')
</head>

<body>


    {{-- <div class="app-body"> --}}
    <div id="mySidebar" class="main_sidebar">
        <div class="sidebar_dashbord-relative">
            <div class="sidebar-header">
                <a class="log-a" href="{{ route('dashboard') }}"><img src="{{ asset('admin/images/logo.png?id=4') }}"></a>
            </div>
            @include('layouts.theme.vocomi.default.partials.navigation-menu-admin1')
        </div>
        {{-- <button class="sidebar-minimizer brand-minimizer" type="button"></button> --}}
    </div>


    <main id="main" class="main">
        <div class="position-relative pt-3 mb-5">
            <div class="top-bar-f">
                <button class="toggle-btn my-custom" onclick="toggleNav()">
                    <div class="nav-close-and-btn">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </div>
                </button>
                <div class="dropdown top-bar-dropdown">
                    <button class="btn dropdown-toggle" type="button" onclick="toggleDropdown()">
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu" id="dropdown-menu">
                        {{-- <li class="dropdown-head">Settings</li> --}}
                        <li><a class="dropdown-item" href="{{ route('admin.change-password') }}"><i
                                    class="fa fa-unlock-alt"></i> Change Password</a></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('Logout-user') }}">
                                <i class="fa fa-sign-out"></i> Log Out
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form" action="{{ route('Logout-user') }}" method="post" style="display: none">
                        @csrf
                    </form>
                </div>

            </div>
        </div>
        <div id="loader_frame"
            style="
            position: fixed;
            z-index: 9999;
            background-color: rgb(0 0 0 / 60%);
            width: 100%;

          ">
            <div id="loader"></div>
        </div>
        <!-- Breadcrumb -->
        {{-- <ol class="breadcrumb" id="vc-breadcrumb"> --}}

        <template v-for="bclink in breadcrumb_links">

            <!-- IF -->
            {{-- <li v-if="bclink.url" class="breadcrumb-item">
                        <a v-bind:href="bclink.url">
                            @{{ bclink.text }}
                        </a>
                    </li> --}}

            <!-- ELSE -->

            {{-- <li v-else class="breadcrumb-item active">
                        @{{ bclink.text }}
                    </li> --}}
            <!-- ENDIF -->

        </template>

        <!-- Breadcrumb Menu-->
        {{-- @if (config('vocomi.domain.website'))
                    <li class="breadcrumb-menu">
                        <div class="btn-group" role="group" aria-label="Button group">
                            <a class="btn text-success" href="{{ '//' . config('vocomi.domain.website') }}"
                                target="_blank">
                                {{ fa_icon('globe') }}
                                {{ __('Website') }}
                                {{ fa_icon('external-link') }}
                            </a>
                        </div>
                    </li>
                @endif --}}
        {{-- </ol> --}}

        <div>
            <div class="animated fadeIn">
                {!! voco_flash()->getAsString() !!}

                <div class="flash-data"></div>

                @yield('content')
            </div>
        </div>

    </main>

    <!-- general purpose modal -->
    <div class="modal fade brownz-custom-popup" id="voco-ajax-modal" tabindex="-1" role="dialog" aria-labelledby="voco-ajax-modal-label"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voco-ajax-modal-label">Modal title</h5>
                    <button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-label="Close">
                        ×
                    </button>

                </div>
                <div class="modal-body">Loading...</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary brownz-btn-yellow-d" data-dismiss="modal">✖ {{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary btn_dark-x btn-voco-save"
                        id="save_button"><i class="fa fa-check"></i> {{ __('Save') }}</button>
                        <div id="loading_spinner" style="display: none;">
                            Processing...
                        </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    {{-- </div> --}}

    {{-- <footer class="app-footer">
        <div>
            <span class="text-muted" style="font-size:.75rem">
                &copy;{{ date('Y') }}
                {{ __(config('app.name')) }}
            </span>
        </div>
        <div class="ml-auto" style="font-size:.75rem">
            <a href="//printechs.com/" target="_blank">
                my_website.com
            </a>
        </div>
    </footer> --}}
    @include('layouts.theme.vocomi.default.partials.foot-scripts')
    @yield('scripts')

</body>
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> --}}
<script src="{{ asset('admin/js/side-navbar.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#save_button').on('click', function () {
            // Show the loading spinner
            $('#loading_spinner').show();

            setTimeout(function () {
                // Hide the loading spinner
                $('#loading_spinner').hide();

                // Trigger your form submission, API call, or any action here
                // alert("Action completed!");
                // Alternatively, close the modal
                // $('#voco-ajax-modal').modal('hide');
            }, 2000); // Adjust delay as needed (2000ms = 2 seconds)
        });
    });
</script>

{{-- multiselect --}}
<link href="{{ asset('assets/vuejs/multi_select/vue-multiselect.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/multi_select/vue-multiselect.min.js') }}"></script>
<script>
    Vue.component("vue-multiselect", window.VueMultiselect.default);
</script>

</html>
