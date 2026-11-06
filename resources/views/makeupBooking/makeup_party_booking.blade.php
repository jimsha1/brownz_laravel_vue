@extends('layouts.vcapp')

@section('content')

<div class="fbox-shadow mt-2 fd-card respo-margin-top ">
    <div class="m-3">
        <h5 style="color: #1D1D1D; font-size: 1.53125rem;">Makeup Party Booking Confirmation</h5>
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-primary btn_dark-x" data-toggle="modal" data-target="#exampleModal">New Booking</button>
        </div>

        <div class="table-responsive">
          <table class="table mt-4 brownz-table-list-v1">
            <thead>
              <tr>
                <th>#</th>
                <th>Client Name</th>
                <th>Client Contact</th>
                <th>Client Email</th>
                <th>Client Date Booked </th>
                <th>Client Services </th>
                <th>Client Time Slot</th>
                <th>Client Address</th>
                <th>Manage</th>
              </tr>
            </thead>
            <tbody>
              @foreach($makeupBookings as $key => $booking)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $booking->makeup_client_name }}</td>
                    <td>{{ $booking->makeup_contact_number }}</td>
                    <td>{{ $booking->makeup_client_email }}</td>
                    <td>{{ $booking->makeup_date_booked }}</td>
                    <td>
                      @php
                          $serviceIds = $booking->makeup_service_name; // Decoded JSON
                          $serviceNames = \App\Models\Service::whereIn('service_id', $serviceIds)->pluck('name')->join(', ');
                      @endphp
                      {{ $serviceNames }}
                  </td>
                    <td>{{ $booking->makeup_time_slot }}</td>
                    <td>{{ $booking->makeup_clent_address }}</td>
                    <td>
                      <button type="button"
                      class="btn btn-primary edit-btn brownz-btn-yellow-d"
                      data-toggle="modal"
                      data-target="#editModal"
                      data-id="{{ $booking->id }}"
                      data-name="{{ $booking->makeup_client_name }}"
                      data-contact="{{ $booking->makeup_contact_number }}"
                      data-place="{{ $booking->makeup_place }}"
                      data-email="{{ $booking->makeup_client_email }}"
                      data-date="{{ $booking->makeup_date_booked }}"
                      data-time="{{ $booking->makeup_time_slot }}"
                      data-address="{{ $booking->makeup_clent_address }}"
                      data-services="{{ json_encode($booking->makeup_service_name) }}"
                      data-beautician="{{ $booking->beautician_name }}">
                  Edit
                    </button>
              </td>
                  </tr>
              @endforeach

            </tbody>
          </table>
        </div>


    </div>
</div>



<div class="modal fade brownz-custom-popup" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Added modal-lg for larger width -->
      <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">New Booking</h5>
              <button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body mb-3">
              <form action="{{ route('makeup.booking.party.store') }}" method="POST">
                  @csrf

                  <!-- Client Information -->
                  <div class="row mb-4">
                      <div class="col-md-4">
                          <div class="form-outline">
                            <label class="form-label" for="clientName">Client Name</label>
                              <input type="text" id="clientName" class="form-control" placeholder="Client Name" name="client_name" required />
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-outline">
                              <label class="form-label" for="contactNumber">Contact Number</label>
                              <input type="number" id="contactNumber" class="form-control" placeholder="Contact Number" name="contact_number" required />
                          </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-outline">
                            <label class="form-label" for="place">Place</label>
                            <input type="text" id="place" class="form-control" placeholder="Place" name="place" placeholder="Place"
                                required />
                        </div>
                    </div>
                  </div>

                  <!-- Service Details -->
                  <div class="row mb-4">
                      <div class="col-md-4">
                          <div class="form-outline">
                              <label class="form-label" for="email">Email</label>
                              <input type="email" id="email" class="form-control" placeholder="Email" name="email"  />
                          </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-outline">
                            <label class="form-label" for="timeSlot">Time Slot</label>
                            <input type="time" id="timeSlot" class="form-control" name="time_slot" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-outline">
                          <label class="form-label" for="date">Date</label>
                          <input type="date" id="date" class="form-control" name="date" required />
                      </div>
                  </div>
                  </div>

                  <!-- Booking Details -->
                  <div class="row mb-4">

                    <div class="col-md-4 brownz-changes-rebove-border" >
                      <label class="form-label" for="timeSlot">Select Service</label>
                      <select id="service" class="form-control form-select-border-width" aria-label="Default select example" name="services[]" multiple style="width: 250px;">
                          <option value="" disabled >Select Services</option>
                          @foreach($services as $service)
                              <option value="{{ $service->service_id }}">{{ $service->name }}</option>
                          @endforeach
                      </select>
                    </div>

                    {{-- <div class="col-md-4">
                      <label class="form-label" for="timeSlot">Service</label>
                        <select id="service" class="form-control form-select-border-width" aria-label="Default select example" name="service">
                            <option value="" disabled selected>Select a Service</option>
                            @foreach($services as $service)
                            <option value="{{ $service->service_id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <script>
                      $(document).ready(function() {
                          $('#service').select2(); // Apply Select2 to the multi-select dropdown
                      });
                  </script>
                      <div class="col-md-4">
                        <label class="form-label" for="timeSlot">Beautician</label>
                          <select id="beautician" class="form-control form-select-border-width" aria-label="Default select example" name="beautician">
                              <option value="" disabled selected>Select a Beautician</option>
                              @foreach($beauticians as $beautician)
                                  <option value="{{ $beautician->name }}">{{ $beautician->name }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <!-- Address -->
                  <div class="form-outline mb-4">
                      <label class="form-label" for="address">Address</label>
                      <textarea class="form-control" id="address" rows="4" name="address" required></textarea>
                  </div>

                  <!-- Submit Button -->
                  <div class="text-end">
                      <button type="submit" class="btn btn-primary btn_dark-x">Book</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>


<div class="modal fade brownz-custom-popup" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">New Booking</h5>
              <button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body mb-3">
              <form action="{{ route('makeup.booking.party.update') }}" method="POST">
                  @csrf
                  <input type="hidden" name="id" id="bookingId" value="">
                  <div class="row mb-4">
                      <div class="col-md-4">
                          <div class="form-outline">
                            <label class="form-label" for="clientName">Client Name</label>
                              <input type="text" id="clientName" class="form-control" name="client_name" required />
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-outline">
                              <label class="form-label" for="contactNumber">Contact Number</label>
                              <input type="number" id="contactNumber" class="form-control" name="contact_number" required />
                          </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-outline">
                            <label class="form-label" for="place">Place</label>
                            <input type="text" id="place" class="form-control" name="place" placeholder="Place"
                                required />
                        </div>
                    </div>
                  </div>


                  <div class="row mb-4">
                      <div class="col-md-4">
                          <div class="form-outline">
                              <label class="form-label" for="email">Email</label>
                              <input type="email" id="email" class="form-control" name="email"  />
                          </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-outline">
                            <label class="form-label" for="timeSlot">Time Slot</label>
                            <input type="time" id="timeSlot" class="form-control" name="time_slot" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-outline">
                          <label class="form-label" for="date">Date</label>
                          <input type="date" id="date" class="form-control" name="date" required />
                      </div>
                  </div>
                  </div>

                  <!-- Booking Details -->
                  <div class="row mb-4">

                    <div class="col-md-4 brownz-changes-rebove-border" >
                      <label class="form-label" for="timeSlot">Select Service</label>
                      <select id="edit_service" class="form-control form-select-border-width" aria-label="Default select example" name="services[]" multiple style="width: 250px;">
                          <option value="" disabled >Select Services</option>
                          @foreach($services as $service)
                              <option value="{{ $service->service_id }}">{{ $service->name }}</option>
                          @endforeach
                      </select>
                    </div>
                    <script>
                      $(document).ready(function() {
                          $('#edit_service').select2();
                      });
                  </script>
                      <div class="col-md-4">
                        <label class="form-label" for="timeSlot">Beautician</label>
                          <select id="beautician" class="form-control form-select-border-width" aria-label="Default select example" name="beautician">
                              <option value="" disabled selected>Select a Beautician</option>
                              @foreach($beauticians as $beautician)
                                  <option value="{{ $beautician->name }}">{{ $beautician->name }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <!-- Address -->
                  <div class="form-outline mb-4">
                      <label class="form-label" for="address">Address</label>
                      <textarea class="form-control" id="address" rows="4" name="address" required></textarea>
                  </div>

                  <!-- Submit Button -->
                  <div class="text-end">
                      <button type="submit" class="btn btn-primary btn_dark-x">Update</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>

<script>
  $(document).on('click', '.edit-btn', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const contact = $(this).data('contact');
    const place = $(this).data('place');
    const email = $(this).data('email');
    const date = $(this).data('date');
    const time = $(this).data('time');
    const address = $(this).data('address');
    const services = $(this).data('services');
    const beautician = $(this).data('beautician');


    const time24HourFormat = convertTo24Hour(time);

    console.log({ id, name, contact, email, date, time, address, services, beautician });
    console.log('time')
    console.log('services');
    console.log("dsdsd"+services);
    console.log(time);
    console.log('beautician');
    console.log(beautician);


    $('#editModal input[name="id"]').val(id);
    $('#editModal input[name="client_name"]').val(name);
    $('#editModal input[name="contact_number"]').val(contact);
    $('#editModal input[name="email"]').val(email);
    $('#editModal input[name="place"]').val(place);
    $('#editModal input[name="date"]').val(date);
    $('#editModal input[name="time_slot"]').val(time24HourFormat);
    $('#editModal textarea[name="address"]').val(address);

    $('#editModal select[name="beautician"]').val(beautician).trigger('change');
    $('#editModal select[name="services[]"]').val(services).trigger('change');

    function convertTo24Hour(time12h) {

      const [time, modifier] = time12h.split(' ');
      let [hours, minutes] = time.split(':');

      if (modifier === 'PM' && hours !== '12') {
          hours = parseInt(hours, 10) + 12;
      }
      if (modifier === 'AM' && hours === '12') {
          hours = '00';
      }

    return `${hours}:${minutes}`;
}
});

</script>
@endsection
