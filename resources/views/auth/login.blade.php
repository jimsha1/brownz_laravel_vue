<?php
header_remove('X-Powered-By');
?>
<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sign in to {{ config('app.name') }}">
    <meta name="author" content="Raju Koyilandy">
    <link rel="icon" type="image/x-icon" href="{{ asset('admin/images/fav_icon.jpg') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/images/fav_icon.jpg') }}">
    <title>Sign In | @yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('components/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/voco-login.css') }}">
    <script>
        var vc_moment_date_offset = new Date("<?php echo (new DateTime())->format('Y-m-d\TH:i:s.vP'); ?>").getTimezoneOffset();
    </script>
    <style>
        .card-login .brownz-btn-yellow-d {
            border: none !important;
            background-color: #E6CD7F !important;
            padding: 14px 16px !important;
            border-radius: 50px;
            font-family: "Roboto", sans-serif !important;
            color: #1d1d1d !important;
            font-size: 16px !important;
            background-image: none;
        }

        .card-login .brownz-btn-yellow-d:hover {
            background-color: #fbda74 !important;
        }

        .brownz-bg-login {
            background-image: none;
            background-color: #1d1d1d;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .brownz-bg-login .card-login .card-title {
            font-size: 5vh;
            color: #E6CD7F !important;
        }

        .brownz-bg-login .card-login {
            max-width: 90% !important;
            background-color: transparent;
            margin: 0 auto;
        }

        .brownz-bg-login .card-login input {
            width: 100%;
            font-weight: 500;
            font-size: 15px;
            border: none;
            border-radius: 36px;
            box-sizing: border-box;
            background-color: #f0f0f0;
            outline: none;
            box-shadow: none;
        }

        .brownz-bg-login .card-login input {
            color: #1d1d1d;
            outline: none;
            border: transparent !important;
            background: -webkit-linear-gradient(to top, rgba(255, 255, 255, 0) 0%, #0093FF 0%) !important;
        }


        .forgot-pass-x {
            color: #fff;
        }

        .forgot-pass-x:hover {
            color: #E6CD7F;
        }

        .brownz-bg-login .card-login .input-box span {
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            margin-top: 0;
        }

        .brownz-bg-login .text-muted {
            color: #f0f0f0 !important;
        }

        .brownz-bg-login .card-login input::placeholder {
            color: #aaa !important;
            opacity: 1;
            /* Firefox */
        }

        .brownz-bg-login .card-login input::-ms-input-placeholder {
            /* Edge 12-18 */
            color: #1d1d1d !important;
        }

        .brownz-bg-login .card-login .form-group span {
            color: #1d1d1d !important;
        }
    </style>
</head>

<body class="bg-gra-03 brownz-bg-login">
    <main role="main" class="">
        <div class="row justify-content-center align-items-center" style="height:calc(100vh - 40px); overflow:hidden;">
            <div class="col-lg-6 pr-0 d-none d-lg-block" style="height: 100vh; width: 100%; background-color: #1d1d1d;">
                <img src="{{ asset('admin/images/bg-login.jpg') }}" width="100%" height="100%"
                    style="object-fit:cover;" alt="">
            </div>
            <div class="col-lg-6"
                style="background-color: #1d1d1d; height: 100vh;
    display: flex;
    align-items: center;">
                <div
                    class="card fbox-shadow fd-card respo-margin-top card-login {{ $errors->has('username') || $errors->has('password') ? ' login-error-animate' : '' }}">
                    <div class="card-body ">
                        <h5 class="card-title">
                            {{ strtoupper(config('app.name')) }} LOGIN
                        </h5>

                        <div class="card-subtitle mb-5">
                            <small>
                                Type your credentials to continue
                            </small>
                        </div>

                        <div class="card-logo d-md-down-none">
                        </div>

                        @if ($errors->has('username'))
                            <div class="text-center">
                                <span class="form-text text-danger">
                                    {{ $errors->first('username') }}
                                </span>
                            </div>
                        @endif

                        @if ($errors->has('password'))
                            <div class="text-center">
                                <small class="form-text text-danger">
                                    {{ $errors->first('password') }}
                                </small>
                            </div>
                        @endif

                        <form class="form mt-4 " role="form" method="post" action="{{ route('login') }}">
                            @csrf

                            <div class="form-body border-0" style="background: none;">
                                <div class="form-group input-box mb-5">
                                    <input type="text" class="form-control" name="username"
                                        value="{{ old('username') }}" placeholder="Email or username"
                                        autocomplete="off" required autofocus>
                                    <span>
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div>
                                {{-- <div class="form-group input-box mb-0">
                  <input type="password" class="form-control" name="password" value="" placeholder="Password" required>
                  <span>
                    <i class="fa fa-key"></i>
                  </span>
                </div> --}}

                                <div class="form-group input-box mb-0 position-relative">

                                    <input type="password" class="form-control pr-5" id="password" name="password"
                                        placeholder="Password" required>

                                    <!-- Left key icon -->
                                    <span>
                                        <i class="fa fa-key"></i>
                                    </span>

                                    <!-- Right eye toggle icon -->
                                    <span id="togglePassword"
                                        style="
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
            color: #555;
            left: auto;
        ">
                                        <i class="fa fa-eye"></i>
                                    </span>

                                </div>
                            </div>

                            {{-- <div class="text-right mt-2 ">
                <a class="forgot-pass-x" href="{{ route('password.request') }}">
                  <small>
                    Forgot your password?
                  </small>
                </a>
              </div> --}}

                            <button type="submit" class="btn btn-gradient brownz-btn-yellow-d w-100 mt-5">
                                Login <i class="fa fa-sign-in" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-lg-6">
                </div>
                <div class="col-lg-6" style="background-color: #1d1d1d; height: 100%; color: #E6CD7F ;">
                    <span class="text-muted">
                        &copy;{{ date('Y') }}
                    </span>
                    {{ config('app.name') }}
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="{{ asset('/js/script.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function() {

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }

            });

        });
    </script>
</body>

</html>
