<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\HairAnalysis;
use App\Models\HairClientDesires;
use App\Models\HairHistory;
use App\Models\SkinAnalysis;
use App\Models\SkinConclusion;
use App\Models\SkinHistory;
use App\Models\SkinLifestyle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS2D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HairConsultationController extends Controller
{
    public function findForBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'type' => 'required|in:hair,skin',
        ]);

        $consultation = Consultation::where('booking_id', $request->booking_id)
            ->where('type', $request->type)
            ->latest('id')
            ->first();

        if (!$consultation) {
            return response()->json(['exists' => false]);
        }

        $payload = $request->type === 'hair'
            ? $this->buildHairFormPayload($consultation)
            : $this->buildSkinFormPayload($consultation);

        $qr = $this->buildConsultationQr($consultation);

        return response()->json(array_merge([
            'exists' => true,
            'form_id' => $consultation->id,
            'status' => $consultation->status,
            'form_url' => $qr['form_url'],
            'qr_code' => $qr['qr_code'],
        ], $payload));
    }

    public function hairPartialSubmit(Request $request)
    {
        if (!$request->booking_id || !$request->client_id) {
            return response()->json(['error' => 'Booking and client are required.'], 422);
        }

        DB::beginTransaction();

        try {
            $consultation = $this->findOrCreateConsultation($request, 'hair');

            $consultation->update([
                'customer_id' => $request->client_id,
                'customer_name' => $request->client_name,
                'customer_age' => $request->client_age,
                'customer_phone' => $request->client_phone,
                'customer_mobile' => $request->client_mobile,
                'date' => $request->date,
                'beautician_name' => $request->stylist_name,
                'beautician_designation' => $request->designation,
                'use_photo' => $request->photo_permission,
                'status' => $consultation->status === 'completed' ? 'completed' : 'partial',
            ]);

            DB::table('hair_analyses')->where('consultation_id', $consultation->id)->delete();
            if ($request->primary_analysis) {
                foreach ($request->primary_analysis as $title => $values) {
                    HairAnalysis::create([
                        'consultation_id' => $consultation->id,
                        'hair_analyses_title' => $title,
                        'hair_analyses_values' => json_encode($values),
                    ]);
                }
            }

            DB::table('hair_histories')->where('consultation_id', $consultation->id)->delete();
            if ($request->filled('history')) {
                foreach ($request->history as $title => $items) {
                    if (!is_array($items)) {
                        continue;
                    }
                    $values = [];
                    $descriptions = [];
                    foreach ($items as $item) {
                        if ($this->isHistoryChecked($item['checked'] ?? false)) {
                            $values[] = $item['type'] ?? null;
                            $descriptions[] = $item['description'] ?? null;
                        }
                    }

                    HairHistory::create([
                        'consultation_id' => $consultation->id,
                        'hair_history_title' => $title,
                        'hair_history_values' => json_encode($values),
                        'hair_history_description' => json_encode($descriptions),
                    ]);
                }
            }

            DB::table('hair_client_desires')->where('consultation_id', $consultation->id)->delete();
            if ($request->client_desire) {
                foreach ($request->client_desire as $title => $desc) {
                    HairClientDesires::create([
                        'consultation_id' => $consultation->id,
                        'hair_client_desires_title' => $title,
                        'hair_client_desires_description1' => $desc['description1'] ?? null,
                        'hair_client_desires_description2' => $desc['description2'] ?? null,
                    ]);
                }
            }

            DB::commit();

            $qr = $this->buildConsultationQr($consultation);

            return response()->json([
                'form_id' => $consultation->id,
                'form_url' => $qr['form_url'],
                'qr_code' => $qr['qr_code'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showCompleteForm($id)
    {
        $consultation = Consultation::with(['hair_analyses', 'hair_histories', 'hair_client_desires'])
            ->findOrFail($id);

        // Map hair_analyses → ['scalp_condition'=>['Normal','Dry'], ...]
        $analyses = [];
        foreach ($consultation->hair_analyses as $a) {
            $analyses[$a->hair_analyses_title] = json_decode($a->hair_analyses_values, true);
        }

        // Map histories → keep both values and descriptions
        $histories = [];
        foreach ($consultation->hair_histories as $h) {
            $values = json_decode($h->hair_history_values, true) ?? [];
            $descs  = json_decode($h->hair_history_description, true) ?? [];

            foreach ($values as $i => $val) {
                $histories[$h->hair_history_title][$val] = $descs[$i] ?? null;
            }
        }


        // Map client desires → ['service_needed'=>['desc1'=>'...','desc2'=>'...']]
        $desires = [];
        foreach ($consultation->hair_client_desires as $d) {
            $desires[$d->hair_client_desires_title] = [
                'desc1' => $d->hair_client_desires_description1,
                'desc2' => $d->hair_client_desires_description2,
            ];
        }
        // dd($histories);
        return view('salon.hairCompleteForm', compact('consultation', 'analyses', 'histories', 'desires'));
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();

        try {
            $consultation = Consultation::findOrFail($id);

            // --- Handle Signatures ---
            $customerSign   = $this->saveSignature($request->client_sign, 'customer');
            $beauticianSign = $this->saveSignature($request->beautician_sign, 'beautician');
            $managerSign    = $this->saveSignature($request->manager_sign, 'manager');

            // --- Update main consultation ---
            // phone1 = mobile, phone2 = alternate phone (matches complete form labels)
            $consultation->update([
                'customer_name'   => $request->client_name,
                'customer_age'    => $request->age,
                'customer_mobile' => $request->phone1,
                'customer_phone'  => $request->phone2,
                'beautician_name' => $request->stylist_name,
                'beautician_designation' => $request->designation,
                'date'            => $request->date,
                'use_photo'       => $request->photo_permission ?? 0,
                'customer_sign'   => $customerSign ?? $consultation->customer_sign,
                'beautician_sign' => $beauticianSign ?? $consultation->beautician_sign,
                'manager_name' => $request->manager_name,
                'manager_sign'    => $managerSign ?? $consultation->manager_sign,
                'type'          => 'hair',
                'status'  => 'completed',
            ]);



            if ($consultation->booking_id) {
                DB::table('bookings')
                    ->where('booking_id', $consultation->booking_id)
                    ->update([
                        'cons_code' => 'CONS-' . $consultation->id,
                        'updated_at'   => now(),
                    ]);
            }

            // --- Hair Analyses ---
            if ($request->has('primary_analysis')) {
                DB::table('hair_analyses')->where('consultation_id', $id)->delete();
                foreach ($request->primary_analysis as $title => $values) {
                    HairAnalysis::create([
                        'consultation_id'      => $id,
                        'hair_analyses_title'  => $title,
                        'hair_analyses_values' => json_encode($values),
                        'updated_at'           => now(),
                    ]);
                }
            }

            // --- Hair History ---
            if ($request->has('history')) {
                DB::table('hair_histories')->where('consultation_id', $id)->delete();
                foreach ($request->history as $title => $items) {
                    if (is_array($items)) {
                        // Prepare values and descriptions separately
                        $values = [];
                        $descriptions = [];
                        foreach ($items as $item) {
                            if ($this->isHistoryChecked($item['checked'] ?? false)) {
                                $values[] = $item['type'] ?? null;
                                $descriptions[] = $item['description'] ?? null;
                            }
                        }
                        HairHistory::create([
                            'consultation_id'      => $id,
                            'hair_history_title'   => $title,
                            'hair_history_values'  => json_encode($values),
                            'hair_history_description' => json_encode($descriptions),
                        ]);
                    }
                }
            }
            // --- Client Desires ---
            if ($request->has('client_desire')) {
                DB::table('hair_client_desires')->where('consultation_id', $id)->delete();
                foreach ($request->client_desire as $title => $desc) {
                    DB::table('hair_client_desires')->insert([
                        'consultation_id'                 => $id,
                        'hair_client_desires_title'       => $title,
                        'hair_client_desires_description1' => $desc['description1'] ?? null,
                        'hair_client_desires_description2' => $desc['description2'] ?? null,
                        'updated_at'                      => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Consultation updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Save signature image to storage and return path.
     * Returns existing storage path unchanged when the pad was not re-signed.
     */
    private function saveSignature($value, $prefix)
    {
        if (!$value) {
            return null;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
            $data = substr($value, strpos($value, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            $data = base64_decode($data);
            if ($data === false) {
                throw new \Exception('Base64 decode failed');
            }

            $fileName = $prefix . '_sign_' . time() . '.' . $type;
            $filePath = 'uploads/signatures/' . $fileName;

            Storage::disk('public')->put($filePath, $data);

            return $filePath;
        }

        // Existing stored path from hidden input (user did not redraw)
        $path = ltrim((string) $value, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'uploads/')) {
            return $path;
        }

        return null;
    }

    private function isHistoryChecked($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    // skin
    public function skinPartialSubmit(Request $request)
    {
        if (!$request->booking_id || !$request->client_id) {
            return response()->json(['error' => 'Booking and client are required.'], 422);
        }

        DB::beginTransaction();

        try {
            $consultation = $this->findOrCreateConsultation($request, 'skin');

            $consultation->update([
                'customer_id' => $request->client_id,
                'customer_name' => $request->client_name,
                'customer_age' => $request->client_age,
                'customer_phone' => $request->client_phone,
                'customer_mobile' => $request->client_mobile,
                'date' => $request->date,
                'beautician_name' => $request->stylist_name,
                'beautician_designation' => $request->designation,
                'use_photo' => $request->input('skin_photo_permission', $request->photo_permission),
                'status' => $consultation->status === 'completed' ? 'completed' : 'partial',
            ]);

            DB::table('skin_analyses')->where('consultation_id', $consultation->id)->delete();
            if ($request->skin_analysis) {
                foreach ($request->skin_analysis as $title => $values) {
                    SkinAnalysis::create([
                        'consultation_id' => $consultation->id,
                        'skin_analyses_title' => $title,
                        'skin_analyses_values' => json_encode($values),
                    ]);
                }
            }

            DB::table('skin_lifestyles')->where('consultation_id', $consultation->id)->delete();
            if ($request->life_style) {
                foreach ($request->life_style as $title => $values) {
                    SkinLifestyle::create([
                        'consultation_id' => $consultation->id,
                        'skin_lifestyle_title' => $title,
                        'skin_lifestyle_values' => json_encode($values),
                    ]);
                }
            }

            DB::table('skin_histories')->where('consultation_id', $consultation->id)->delete();
            if ($request->history) {
                foreach ($request->history as $title => $values) {
                    SkinHistory::create([
                        'consultation_id' => $consultation->id,
                        'skin_history_title' => $title,
                        'skin_history_values' => json_encode($values),
                    ]);
                }
            }

            DB::table('skin_conclusions')->where('consultation_id', $consultation->id)->delete();
            if ($request->conclusion) {
                foreach ($request->conclusion as $title => $desc) {
                    SkinConclusion::create([
                        'consultation_id' => $consultation->id,
                        'skin_conclusion_title' => $title,
                        'skin_conclusion_description' => $desc['description'] ?? $desc['desc'] ?? null,
                    ]);
                }
            }

            DB::commit();

            $qr = $this->buildConsultationQr($consultation);

            return response()->json([
                'form_id' => $consultation->id,
                'form_url' => $qr['form_url'],
                'qr_code' => $qr['qr_code'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function showCompleteSkinForm($id)
    {
        $consultation = Consultation::with(['skin_analyses', 'skin_lifestyles', 'skin_histories', 'skin_conclusions'])
            ->findOrFail($id);

        $analyses = [];
        foreach ($consultation->skin_analyses as $a) {
            $analyses[$a->skin_analyses_title] = json_decode($a->skin_analyses_values, true);
        }

        $lifeStyles = [];
        foreach ($consultation->skin_lifestyles as $l) {
            $lifeStyles[$l->skin_lifestyle_title] = json_decode($l->skin_lifestyle_values, true);
        }

        $histories = [];
        foreach ($consultation->skin_histories as $h) {
            $histories[$h->skin_history_title] = json_decode($h->skin_history_values, true);
        }

        $conclusions = [];
        foreach ($consultation->skin_conclusions as $c) {
            $conclusions[$c->skin_conclusion_title] = [
                'desc' => $c->skin_conclusion_description,
            ];
        }

        return view('salon.skinCompleteForm', compact('consultation', 'analyses', 'lifeStyles', 'histories', 'conclusions'));
    }

    public function skinUpdate(Request $request, $id)
    {
        // dd($request);
        DB::beginTransaction();

        try {
            $consultation = Consultation::findOrFail($id);

            // --- Handle Signatures ---
            $customerSign   = $this->saveSignature($request->client_sign, 'customer');
            $beauticianSign = $this->saveSignature($request->beautician_sign, 'beautician');
            $managerSign    = $this->saveSignature($request->manager_sign, 'manager');

            // --- Update main consultation ---
            // phone1 = mobile, phone2 = alternate phone (matches complete form labels)
            $consultation->update([
                'customer_name'   => $request->client_name,
                'customer_age'    => $request->age,
                'customer_mobile' => $request->phone1,
                'customer_phone'  => $request->phone2,
                'beautician_name' => $request->stylist_name,
                'beautician_designation' => $request->designation,
                'date'            => $request->date,
                'use_photo'       => $request->input('skin_photo_permission', $request->photo_permission) ?? 0,
                'customer_sign'   => $customerSign ?? $consultation->customer_sign,
                'beautician_sign' => $beauticianSign ?? $consultation->beautician_sign,
                'manager_name' => $request->manager_name,
                'manager_sign'    => $managerSign ?? $consultation->manager_sign,
                'type'            => 'skin',
                'status'  => 'completed',
            ]);

             if ($consultation->booking_id) {
                DB::table('bookings')
                    ->where('booking_id', $consultation->booking_id)
                    ->update([
                        'cons_code' => 'CONS-' . $consultation->id,
                        'updated_at'   => now(),
                    ]);
            }

            // --- Skin Analyses ---
            if ($request->has('skin_analysis')) {
                DB::table('skin_analyses')->where('consultation_id', $id)->delete();
                foreach ($request->skin_analysis as $title => $values) {
                    SkinAnalysis::create([
                        'consultation_id'      => $id,
                        'skin_analyses_title'  => $title,
                        'skin_analyses_values' => json_encode($values),
                    ]);
                }
            }

            // --- Lifestyle ---
            if ($request->has('life_style')) {
                DB::table('skin_lifestyles')->where('consultation_id', $id)->delete();
                foreach ($request->life_style as $title => $values) {
                    SkinLifestyle::create([
                        'consultation_id'      => $id,
                        'skin_lifestyle_title' => $title,
                        'skin_lifestyle_values' => json_encode($values),
                    ]);
                }
            }

            // --- History ---
            if ($request->has('history')) {
                DB::table('skin_histories')->where('consultation_id', $id)->delete();
                foreach ($request->history as $title => $values) {
                    SkinHistory::create([
                        'consultation_id'      => $id,
                        'skin_history_title'   => $title,
                        'skin_history_values'  => json_encode($values),
                    ]);
                }
            }

            // --- Conclusion ---
            if ($request->has('conclusion')) {
                DB::table('skin_conclusions')->where('consultation_id', $id)->delete();
                foreach ($request->conclusion as $title => $desc) {
                    SkinConclusion::create([
                        'consultation_id'             => $id,
                        'skin_conclusion_title'       => $title,
                        'skin_conclusion_description' => $desc['desc'] ?? $desc['description'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Consultation updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    private function findOrCreateConsultation(Request $request, string $type): Consultation
    {
        $consultation = null;

        if ($request->filled('consultation_id')) {
            $consultation = Consultation::where('id', $request->consultation_id)
                ->where('type', $type)
                ->where('booking_id', $request->booking_id)
                ->first();
        }

        if (!$consultation) {
            $consultation = Consultation::where('booking_id', $request->booking_id)
                ->where('type', $type)
                ->latest('id')
                ->first();
        }

        if ($consultation) {
            return $consultation;
        }

        return Consultation::create([
            'booking_id' => $request->booking_id,
            'customer_id' => $request->client_id,
            'type' => $type,
            'status' => 'partial',
        ]);
    }

    private function buildConsultationQr(Consultation $consultation): array
    {
        $formUrl = $consultation->type === 'skin'
            ? route('skin.form.complete', ['id' => $consultation->id], true)
            : route('hair.form.complete', ['id' => $consultation->id], true);

        $dns = new DNS2D();
        $pngData = $dns->getBarcodePNG($formUrl, 'QRCODE');

        return [
            'form_url' => $formUrl,
            'qr_code' => 'data:image/png;base64,' . $pngData,
        ];
    }

    private function buildHairFormPayload(Consultation $consultation): array
    {
        $consultation->load(['hair_analyses', 'hair_histories', 'hair_client_desires']);

        $primaryAnalysis = [
            'scalp_condition' => [],
            'hair_texture' => [],
            'hair_density' => [],
            'hair_condition' => [],
            'hair_elasticity' => [],
            'hair_porosity' => [],
            'grey_percentage' => [],
        ];
        foreach ($consultation->hair_analyses as $row) {
            $primaryAnalysis[$row->hair_analyses_title] = json_decode($row->hair_analyses_values, true) ?: [];
        }

        $hairHistoryTemplate = [
            'hair_type' => ['Virgin', 'Henna', 'Chemically Treated', 'Coloured', 'Keratin'],
            'allergies' => ['Hair Dye', 'Chemical Solution', 'Blonder/Lightener', 'Other'],
            'medical_condition' => ['Pregnant', 'Medicine Conception'],
            'home_care' => ['Shampoo/Conditioner/Serum'],
            'oiling_habit' => ['Frequency/Type of Oil'],
            'styling_product' => ['Heating Tools/Styling Creams'],
        ];

        $savedHistory = [];
        foreach ($consultation->hair_histories as $row) {
            $values = json_decode($row->hair_history_values, true) ?: [];
            $descs = json_decode($row->hair_history_description, true) ?: [];
            $map = [];
            foreach ($values as $i => $val) {
                $map[$val] = $descs[$i] ?? '';
            }
            $savedHistory[$row->hair_history_title] = $map;
        }

        $hairHistory = [];
        foreach ($hairHistoryTemplate as $title => $types) {
            $hairHistory[$title] = [];
            foreach ($types as $type) {
                $hairHistory[$title][] = [
                    'type' => $type,
                    'description' => $savedHistory[$title][$type] ?? '',
                    'checked' => array_key_exists($type, $savedHistory[$title] ?? []),
                ];
            }
        }

        $clientDesire = [
            'service_needed' => ['description1' => '', 'description2' => ''],
            'desired_result' => ['description1' => '', 'description2' => ''],
            'technique' => ['description1' => '', 'description2' => ''],
            'patch_test' => ['description1' => '', 'description2' => ''],
            'product_used' => ['description1' => '', 'description2' => ''],
            'after_care' => ['description1' => '', 'description2' => ''],
        ];
        foreach ($consultation->hair_client_desires as $row) {
            $clientDesire[$row->hair_client_desires_title] = [
                'description1' => $row->hair_client_desires_description1 ?? '',
                'description2' => $row->hair_client_desires_description2 ?? '',
            ];
        }

        return [
            'client_name' => $consultation->customer_name,
            'client_age' => $consultation->customer_age,
            'date' => $consultation->date ? \Carbon\Carbon::parse($consultation->date)->format('Y-m-d') : null,
            'client_mobile' => $consultation->customer_mobile,
            'client_phone' => $consultation->customer_phone,
            'stylist_name' => $consultation->beautician_name,
            'designation' => $consultation->beautician_designation,
            'photo_permission' => $consultation->use_photo,
            'primary_analysis' => $primaryAnalysis,
            'hair_history' => $hairHistory,
            'client_desire' => $clientDesire,
        ];
    }

    private function buildSkinFormPayload(Consultation $consultation): array
    {
        $consultation->load(['skin_analyses', 'skin_lifestyles', 'skin_histories', 'skin_conclusions']);

        $skinAnalysis = [
            'skin_type' => [],
            'skin_concern' => [],
            'skin_allergy' => [],
            'skin_sensitivity' => [],
            'skin_routine' => [],
        ];
        foreach ($consultation->skin_analyses as $row) {
            $skinAnalysis[$row->skin_analyses_title] = json_decode($row->skin_analyses_values, true) ?: [];
        }

        $lifeStyle = [
            'water_intakes' => [],
            'sleep' => [],
            'stress' => [],
            'sun_exposure' => [],
            'smoke' => [],
            'drinks' => [],
        ];
        foreach ($consultation->skin_lifestyles as $row) {
            $lifeStyle[$row->skin_lifestyle_title] = json_decode($row->skin_lifestyle_values, true) ?: [];
        }

        $history = [
            'medication' => [],
            'hormonal_issues' => [],
            'past_treatments' => [],
            'chronic_illness' => [],
            'pregnant' => [],
        ];
        foreach ($consultation->skin_histories as $row) {
            $history[$row->skin_history_title] = json_decode($row->skin_history_values, true) ?: [];
        }

        $conclusion = [
            'recommended_service' => ['description' => ''],
            'after_care' => ['description' => ''],
            'follow_up_plan' => ['description' => ''],
        ];
        foreach ($consultation->skin_conclusions as $row) {
            $conclusion[$row->skin_conclusion_title] = [
                'description' => $row->skin_conclusion_description ?? '',
            ];
        }

        return [
            'client_name' => $consultation->customer_name,
            'client_age' => $consultation->customer_age,
            'date' => $consultation->date ? \Carbon\Carbon::parse($consultation->date)->format('Y-m-d') : null,
            'client_mobile' => $consultation->customer_mobile,
            'client_phone' => $consultation->customer_phone,
            'stylist_name' => $consultation->beautician_name,
            'designation' => $consultation->beautician_designation,
            'skin_photo_permission' => $consultation->use_photo,
            'skin_analysis' => $skinAnalysis,
            'life_style' => $lifeStyle,
            'history' => $history,
            'conclusion' => $conclusion,
        ];
    }
}
