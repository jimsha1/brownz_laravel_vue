@extends('layouts.vcapp')

@section('content')

    <h5 style="text-align: center"><b>{{ $user_name }}</b></h5>
    <div>
        <div class="modal-content" style="padding: 0.5rem 2rem;">
            <div class="modal-header border-0 px-0">
                <h5 class="modal-title" id="voco-ajax-modal-label">Booking Data</h5>

            </div>

            <table class="table brownz-table-list-v1">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"> Name</th>
                        <th scope="col">Code</th>
                        <th scope="col"> Date</th>
                        <th scope="col"> Time</th>
                        <th scope="col">Service</th>
                        <th scope="col">Status</th>
                        <th scope="col">View</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        
                    @endphp
                    @foreach ($booking_record as $booking)
                        <?php if ($booking->booking_code != "") { ?>

                        <?php
                        $now = date("Y-m-d H:i:s");
                        $appointmentDate = $booking->appointment_date . " " . $booking->slot_start;
                         if ($booking->status == 'refunded' && $appointmentDate > $now) {
                            // No need to show in the calander incase refunded before start the service
                            // Commented By Thomas sir on 24 NOV 2021(Whatsapp)
                            // Written by Hashir
                        } else {?>
                        @php
                            
                            // $status = "<span style='background-color: skyblue;padding:5px;color:white'>Booked</span>";
                            // if (date('Y-m-d') > $booking->appointment_date && $booking->status != 'completed') {
                            //     $status = "<span style='background-color:red;padding:5px;color:white'>Expired</span>";
                            // } elseif (date('Y-m-d') < $booking->appointment_date) {
                            //     $status = "<span style='background-color:green;padding:5px;color:white'>Upcoming</span>";
                            // } elseif (date('Y-m-d') == $booking->appointment_date && $booking->status == 'completed') {
                            //     $status = "<span style='background-color: skyblue;padding:5px;color:white'>Completed</span>";
                            // } elseif (date('Y-m-d') > $booking->appointment_date && $booking->status == 'completed') {
                            //     $status = "<span style='background-color: skyblue;padding:5px;color:white'>Completed</span>";
                            // }
                            
                            $status = "";

                            if ($booking->status == 'start') {
                                $color = Config::get('constants.service_status_color.on_going'); //"#ffc100"; // On Going
                                $status = "<span style='background-color:".$color.";padding:5px;color:white'>Ongoing</span>";
                            } elseif ($booking->status == 'completed') {
                                $color = Config::get('constants.service_status_color.completed'); //"#00af50"; // Completed
                                $status = "<span style='background-color:".$color.";padding:5px;color:white'>Completed</span>";
                            } else {
                                // if ($booking->appointment_date >= date('Y-m-d')) {
                                    if($appointmentDate > $now){
                                    $color = Config::get('constants.service_status_color.pending'); //"#0070c2"; // Pending
                                    $status = "<span style='background-color:".$color.";padding:5px;color:white'>Pending</span>";
                                } else {
                                    $color = Config::get('constants.service_status_color.expired'); //"#fb0200"; // Expired
                                    $status = "<span style='background-color:".$color.";padding:5px;color:#E6CD7F'>Expired</span>";
                                }
                            }
                            
                        @endphp

                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $booking->client_name }}</td>
                            <td>{{ $booking->booking_code }}</td>
                            <td>{{ $booking->appointment_date }}</td>
                            <td>{{ $booking->slot_start }}</td>
                            <td>{{ $booking->service_name }}</td>
                            <td><?= $status ?></td>
                            <td><a href="{{ route('booking.card', ['id' => $booking->booking_service_id]) }}"><button
                                        type="button" class="btn btn-primary brownz-btn-yellow-d">View</button></a></td>
                        </tr>

                        <?php } ?>
                        <?php } ?>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
