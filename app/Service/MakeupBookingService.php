<?php

namespace App\Service;

use App\Models\Beautician;
use App\Models\MakeupBooking;
use App\Models\MakeupService;
use App\Models\Service;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MakeupBookingService
{
    /**
     * Normalize booking type for storage / redirects.
     */
    private function normalizeType(?string $type): string
    {
        $type = strtolower(trim((string) $type));

        return in_array($type, ['bridal', 'groom', 'party'], true) ? $type : 'bridal';
    }

    private function routeForType(string $type): string
    {
        return match ($type) {
            'groom' => 'makeup.booking.groom',
            'party' => 'makeup.booking.party',
            default => 'makeup.booking.bridal',
        };
    }

    /**
     * Convert HTML time (H:i / H:i:s) or already-formatted slots to display "g:i A".
     */
    private function formatTimeSlot(?string $timeSlot): string
    {
        $timeSlot = trim((string) $timeSlot);
        if ($timeSlot === '') {
            throw new Exception('Time slot is required.');
        }

        if (preg_match('/AM|PM/i', $timeSlot)) {
            $dt = DateTime::createFromFormat('g:i A', strtoupper($timeSlot))
                ?: DateTime::createFromFormat('h:i A', strtoupper($timeSlot));
            if (! $dt) {
                throw new Exception('Invalid time slot format.');
            }

            return $dt->format('g:i A');
        }

        $dt = DateTime::createFromFormat('H:i', $timeSlot)
            ?: DateTime::createFromFormat('H:i:s', $timeSlot);
        if (! $dt) {
            throw new Exception('Invalid time slot format.');
        }

        return $dt->format('g:i A');
    }

    /**
     * @return array<int, int|string>
     */
    private function resolveSelectedServices($request, string $type): array
    {
        $selectedServices = $request->input('services', $request->input('service'));
        if (! is_array($selectedServices)) {
            $selectedServices = $selectedServices !== null && $selectedServices !== ''
                ? [$selectedServices]
                : [];
        }

        $selectedServices = array_values(array_filter($selectedServices, function ($id) {
            return $id !== null && $id !== '';
        }));

        if (empty($selectedServices)) {
            throw new Exception('No services selected.');
        }

        if ($type === 'bridal') {
            $found = MakeupService::whereIn('id', $selectedServices)->pluck('id')->all();
        } else {
            $found = Service::whereIn('service_id', $selectedServices)->pluck('service_id')->all();
        }

        $missing = array_diff(array_map('strval', $selectedServices), array_map('strval', $found));
        if (! empty($missing)) {
            throw new Exception('One or more selected services were not found.');
        }

        return $selectedServices;
    }

    private function resolveBeauticianId(?string $beauticianName): int
    {
        if (empty($beauticianName)) {
            return 0;
        }

        $beautician = Beautician::query()
            ->whereHas('user', function ($q) use ($beauticianName) {
                $q->where('name', $beauticianName);
            })
            ->first();

        return $beautician ? (int) $beautician->beautician_id : 0;
    }

    /**
     * @return array{names: array<int, string>, primary_id: int}
     */
    private function resolveBeauticians($request): array
    {
        $selected = $request->input('beauticians', $request->input('beautician'));
        if (! is_array($selected)) {
            $selected = $selected !== null && $selected !== ''
                ? [$selected]
                : [];
        }

        $names = array_values(array_unique(array_filter(array_map(function ($name) {
            return trim((string) $name);
        }, $selected), function ($name) {
            return $name !== '';
        })));

        if (empty($names)) {
            throw new Exception('No beauticians selected.');
        }

        return [
            'names' => $names,
            'primary_id' => $this->resolveBeauticianId($names[0] ?? null),
        ];
    }

    public function storeBooking($request = null, $type = 'bridal')
    {
        $type = $this->normalizeType($type);

        try {
            DB::beginTransaction();

            $selectedServices = $this->resolveSelectedServices($request, $type);
            $convertedTime = $this->formatTimeSlot($request->time_slot);
            $beauticians = $this->resolveBeauticians($request);

            $makeupBookingBridal = MakeupBooking::create([
                'beautician_id' => $beauticians['primary_id'] ?: 0,
                'service_id' => $selectedServices[0] ?? 0,
                'makeup_service_name' => json_encode($selectedServices),
                'makeup_client_name' => $request->client_name,
                'makeup_contact_number' => $request->contact_number,
                'makeup_place' => $request->place,
                'makeup_client_email' => $request->email,
                'booking_type' => $type,
                'makeup_date_booked' => $request->date,
                'makeup_time_slot' => $convertedTime,
                'makeup_time_slot_id' => '111',
                'makeup_clent_address' => $request->address,
                'beautician_name' => $beauticians['names'],
                'status' => $request->status ?: 'active',
            ]);

            if (! $makeupBookingBridal) {
                throw new Exception('Failed to save booking information.');
            }

            DB::commit();

            voco_flash()->success(__('Booking has been made successfully.'));

            return redirect()->route($this->routeForType($type));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error storing booking: '.$e->getMessage(), ['exception' => $e]);
            voco_flash()->error(__('Failed to make Makeup Booking. :message', ['message' => $e->getMessage()]));

            return redirect()->back()->withInput();
        }
    }

    public function updateBooking($request, $type = 'bridal')
    {
        $type = $this->normalizeType($type);

        try {
            $id = $request->input('id');
            $makeupBooking = MakeupBooking::find($id);

            if (! $makeupBooking) {
                voco_flash()->error(__('Makeup Booking not found.'));

                return redirect()->back()->withInput();
            }

            $selectedServices = $this->resolveSelectedServices($request, $type);
            $convertedTime = $this->formatTimeSlot($request->input('time_slot'));
            $beauticians = $this->resolveBeauticians($request);

            $makeupBooking->beautician_id = $beauticians['primary_id'] ?: $makeupBooking->beautician_id;
            $makeupBooking->service_id = $selectedServices[0] ?? $makeupBooking->service_id;
            $makeupBooking->makeup_client_name = $request->input('client_name');
            $makeupBooking->makeup_contact_number = $request->input('contact_number');
            $makeupBooking->makeup_client_email = $request->input('email');
            $makeupBooking->makeup_place = $request->input('place', $makeupBooking->makeup_place);
            $makeupBooking->makeup_date_booked = $request->input('date');
            $makeupBooking->makeup_time_slot = $convertedTime;
            $makeupBooking->makeup_time_slot_id = '111';
            $makeupBooking->booking_type = $type;
            $makeupBooking->makeup_clent_address = $request->input('address');
            $makeupBooking->beautician_name = $beauticians['names'];
            $makeupBooking->status = $request->input('status') ?: $makeupBooking->status;
            $makeupBooking->makeup_service_name = json_encode($selectedServices);
            $makeupBooking->save();

            voco_flash()->success(__('Booking has been updated successfully.'));

            return redirect()->route($this->routeForType($type));
        } catch (Exception $exception) {
            Log::error('Error updating booking: '.$exception->getMessage(), ['exception' => $exception]);
            voco_flash()->error(__('Failed to update Makeup Booking. :message', ['message' => $exception->getMessage()]));

            return redirect()->back()->withInput();
        }
    }

    public function storeService($request = null)
    {
        try {
            DB::beginTransaction();

            $makeupService = MakeupService::create([
                'service_name' => $request->service_name,
                'service_base_price' => $request->service_base_price,
                'tax' => $request->tax,
                'duration_minutes' => $request->duration_minutes,
                'status' => $request->status,
            ]);
            if (! $makeupService) {
                throw new Exception('Failed to save service information.');
            }

            DB::commit();

            voco_flash()->success(__('Service has been added successfully.'));

            return redirect()->route('makeup.booking.service');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error storing booking: '.$e->getMessage(), ['exception' => $e]);
            voco_flash()->error(__('Failed to add service. :message', ['message' => $e->getMessage()]));

            return redirect()->back()->withInput();
        }
    }

    public function updateService($request)
    {
        try {
            $service = MakeupService::findOrFail($request['id']);
            $service->service_name = $request->input('service_name');
            $service->service_base_price = $request->input('service_base_price');
            $service->tax = $request->input('tax');
            $service->duration_minutes = $request->input('duration_minutes');
            $service->status = $request->input('status');
            $service->save();

            voco_flash()->success(__('Service has been updated successfully.'));

            return redirect()->route('makeup.booking.service');
        } catch (Exception $exception) {
            Log::error('Error updating booking: '.$exception->getMessage(), ['exception' => $exception]);
            voco_flash()->error(__('Failed to update Service. :message', ['message' => $exception->getMessage()]));

            return redirect()->back()->withInput();
        }
    }
}
