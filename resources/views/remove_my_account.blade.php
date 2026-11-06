<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<body>
    <div class="container">
        <h3 class="request-otp-header"></h3>
        <form method="POST" action="{{ route('delete-account-request-submit') }}">
            @csrf
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Mobile number</label>
                <input type="number" name="mobile_no" class="form-control" id="exampleFormControlInput1"
                    placeholder="Enter mobile number">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Password</label>
                <input type="text" name="password" class="form-control" id="exampleFormControlInput1"
                    placeholder="Enter password">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Reason Note</label>
                <textarea class="form-control" name="reason_note" id="exampleFormControlTextarea1" rows="3"
                    placeholder="Please specify the reason..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Delete Account</button>
        </form>

    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.4.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
