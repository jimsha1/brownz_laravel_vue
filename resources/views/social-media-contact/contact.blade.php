<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz-social-contact-us</title>
    <link rel="icon" type="image/x-icon" href="{{asset('contact/img/fav_icon.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('contact/css/style.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="row brownz-form-full-container">
        <div class="col-lg-6 col-md-12">
            <img src="{{asset('contact/img/bg-form.jpg')}}" alt="form">
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="bg-form-dark">
                <header>
                    <a class="d-block" href="#"><img src="{{asset('contact/img/logo.png')}}"></a>
                </header>
                <form action="{{route('contact.store')}}" method="POST">
                  @csrf
                    <div class="row row-gap-5">
                        <div class="col-xl-12 col-lg-12 col">
                            <label for="name">Full Name</label>
                            <input placeholder="Full Name" id="full_name" type="text" name="full_name">
                        </div>
                        <div class="col-xl-12 col-lg-12">
                            <label for="">Phone Number</label>
                            <input placeholder="Phone Number" id="phone_number" type="number" name="phone_number">
                        </div>
                        <div class="col-lg-12">
                            <label for="">Address</label>
                            <textarea rows="5"  id="address" type="text" name="address"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button>Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            confirmButtonText: 'OK',
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            confirmButtonText: 'Try Again',
        });
    @endif

  </script>
</body>
</html>