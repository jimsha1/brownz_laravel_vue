<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckBookingFlagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-booking-flag-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("------------------------------INSIDE CHECK BOOKING-------------------------");
        try {
            $today = Carbon::today()->toDateString();
            Log::info($today);
            $bookings = Booking::where('is_booking', 1)
                ->whereHas('bookingServices', function ($query) use ($today) {
                    $query->where('appointment_date', $today);
                })
                ->with(['bookingServices' => function ($query) use ($today) {
                    $query->where('appointment_date', $today);
                }])
                ->get();
            Log::info("------------------booking----------------------");
            Log::info($bookings);
            foreach ($bookings as $booking) {
                $booking->update(['is_booking' => 0]);
                Log::info("Updated booking ID {$booking->booking_id} - is_booking set to 0");
            }

            $this->info('Booking flag check completed successfully.');
        } catch (\Exception $e) {
            Log::error('Error in CheckBookingFlagCommand: ' . $e->getMessage());
            $this->error('An error occurred while processing bookings.');
        }
    }
}
