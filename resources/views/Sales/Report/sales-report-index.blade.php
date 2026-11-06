@extends('layouts.vcapp')

@section('content')
<div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="m-4">
        <h5 class id="voco-ajax-modal-label">{{ __('Sales Report') }}</h5>
        <form method="post" action="{{route('sales.report.filter')}}">
            @csrf
            <div class="row align-items-end">
                <div class="form-group col-md-4 mb-0">
                    <label for="name">{{ __('Start Date') }}</label>
                    <input type="date"  name="start_date" placeholder="start date" id="start_date"
                        class="form-control" value="{{ old('start_date', request('start_date')) }}">
                </div>
                <div class="form-group col-md-4 mb-0">
                    <label for="name">{{ __('End Date') }}</label>
                    <input type="date"  name="end_date" placeholder="end date" id="end_date"
                        class="form-control" value="{{ old('end_date', request('end_date')) }}">
                </div>
                <div class="form-group col-md-4 mb-0">
                    <label for="name">{{ __('Beautision Name') }}</label>
                    <input type="text"  name="beautician_name" placeholder="Enter Booking Code" id="beautician_name"
                        class="form-control" value="{{ old('beautician_name', request('beautician_name')) }}">
                </div>
                <div class="vc-form-buttons justify-content-center" style="padding-left: 80%; padding-top: 20px;">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>{{ __('Search') }}
                    </button>
                    <a href="{{ route('sales.report.index') }}" class="btn btn-secondary">
                        <i class="fa fa-times"></i> {{ __('Reset') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

</div>

<div class="fbox-shadow mt-2 fd-card respo-margin-top">



      <div class="m-3">
        <p>{{ __('Sales Report') }}</p>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end m-2">
            <a href="{{route('sales.pdf.genarate')}}"class="btn btn-success me-md-2 mr-2" type="button">PDF</a>
            <a href="{{route('sales.excel.genarate')}}" class="btn btn-danger" type="button">Excel</a>
          </div>
        <table  class="table m-1">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('Booking Date') }} </th>
                <th scope="col">{{ __('Booking Code') }} </th>
                <th scope="col">{{ __('Client Name') }}</th>
                <th scope="col">{{ __('Booking Total') }}</th>
                <th scope="col">{{ __('Payment Total') }}</th>
              </tr>
            </thead>
            <tbody>
                @foreach($allReports as $key => $report)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d-m-Y') }}</td>
                        <td>{{ $report->booking_code }}</td>
                        <td>{{ $report->client_name }}</td>
                        <td>{{ $report->booking_total }}</td>
                        <td>{{ $report->payment_total }}</td>
                    </tr>
                @endforeach

            </tbody>
          </table>
      </div>

</div>
@endsection
