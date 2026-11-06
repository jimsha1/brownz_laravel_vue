<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skin Consultation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Existing CSS styles remain unchanged */
        .new-form-table {
            /* border-radius: 24px; overflow: hidden; */
        }

        .new-form-table.table-bordered th,
        .new-form-table.table-bordered td {
            border: none;
            border-top: 1px solid #e2e2e244;
            vertical-align: middle;
        }

        .new-form-table .brw-checkbox-browz-dds {
            font-size: 14px;
            margin-bottom: 0;
            color: #7b7b7b;
        }

        .new-form-table.table-bordered th {
            padding-left: 0;
            font-weight: 400;
        }

        .browz-gray-input-custom {
            background-color: #F0F0F3;
            color: #1D1D1D;
            font-size: 14px;
            font-weight: 400;
            padding: 10px 15px;
            border-radius: 26px;
            max-width: 100%;
            width: 100%;
            border: none;
            outline: none;
            height: auto;
        }

        .brw-checkbox-browz-dds {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 18px;
            user-select: none;
            color: #1D1D1D;
        }

        .brw-checkbox-browz-dds .brow-checkbox-dd {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .brw-checkbox-browz-dds .brow-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #F0F0F3 !important;
            border: 1px solid #F0F0F3;
            border-radius: 5px;
        }

        .brw-checkbox-browz-dds .brow-checkbox-dd:checked~.brow-checkmark {
            background-color: #E6CD7F !important;
            border-color: #E6CD7F;
            border-radius: 5px;
        }

        .brw-checkbox-browz-dds .brow-checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid #fff;
            border-width: 0 2.99px 2.99px 0;
            transform: rotate(45deg);
        }

        .brw-checkbox-browz-dds .brow-checkbox-dd:checked~.brow-checkmark:after {
            display: block;
        }

        .sig-canvas {
            min-height: 120px;
            height: 120px;
            background: #fff;
            touch-action: none;
        }
    </style>
</head>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<body>
    <div class="container mt-4">
        <h3>Skin Consultation Form</h3>

        <form method="POST" action="{{ route('skin.update', $consultation->id) }}">
            @csrf
            @method('POST')

            <!-- Client Info -->
            <div class="row mb-4" style="row-gap: 16px">
                <div class="col-md-6">
                    <label for="client_name">Client Name</label>
                    <input type="text" class="browz-gray-input-custom" id="client_name" name="client_name"
                        value="{{ old('client_name', $consultation->customer_name) }}">
                </div>
                <div class="col-md-3">
                    <label for="age">Age</label>
                    <input type="number" class="browz-gray-input-custom" id="age" name="age"
                        value="{{ old('age', $consultation->customer_age) }}">
                </div>
                <div class="col-md-3">
                    <label for="date">Date</label>
                    <input type="date" class="browz-gray-input-custom" id="date" name="date"
                        value="{{ old('date', $consultation->date ? \Illuminate\Support\Carbon::parse($consultation->date)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6">
                    <label for="phone1">Phone 1</label>
                    <input type="text" class="browz-gray-input-custom" id="phone1" name="phone1"
                        value="{{ old('phone1', $consultation->customer_mobile) }}">
                </div>
                <div class="col-md-6">
                    <label for="phone2">Phone 2</label>
                    <input type="text" class="browz-gray-input-custom" id="phone2" name="phone2"
                        value="{{ old('phone2', $consultation->customer_phone) }}">
                </div>
                <div class="col-md-6">
                    <label for="stylist_name">Stylist Name</label>
                    <input type="text" class="browz-gray-input-custom" id="stylist_name" name="stylist_name"
                        value="{{ old('stylist_name', $consultation->beautician_name) }}">
                </div>
                <div class="col-md-6">
                    <label for="designation">Designation</label>
                    <input type="text" class="browz-gray-input-custom" id="designation" name="designation"
                        value="{{ old('designation', $consultation->beautician_designation) }}">
                </div>
            </div>

            <hr class="pb-3">

            <!-- Primary Analysis -->
            <h5 class="mb-3 mt-3">Skin Analysis</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table">

                    <tr>
                        <th>Skin type</th>
                        @foreach (['Normal', 'Dry', 'Oily', 'Combination', 'Pore', 'Other'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox" name="skin_analysis[skin_type][]"
                                        value="{{ $option }}"
                                        {{ in_array($option, $analyses['skin_type'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th>Skin concern</th>
                        @foreach (['Acne', 'Pigmentation', 'Dullness', 'Wrinkles', 'Pore', 'Other'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox" name="skin_analysis[skin_concern][]"
                                        value="{{ $option }}"
                                        {{ in_array($option, $analyses['skin_concern'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th>Skin allergy</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="skin_analysis[skin_allergy][]"
                                    value="Yes"
                                    {{ in_array('Yes', $analyses['skin_allergy'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="skin_analysis[skin_allergy][]"
                                    value="No"
                                    {{ in_array('No', $analyses['skin_allergy'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td colspan="4"></td>
                    </tr>

                    <tr>
                        <th>Skin sensitivity</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="skin_analysis[skin_sensitivity][]" value="Yes"
                                    {{ in_array('Yes', $analyses['skin_sensitivity'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="skin_analysis[skin_sensitivity][]" value="No"
                                    {{ in_array('No', $analyses['skin_sensitivity'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td colspan="4"></td>
                    </tr>

                    <tr>
                        <th>Skin routine</th>
                        @foreach (['Cleanser', 'Moisturiser', 'Sunscreen', 'Serum', 'Toner', 'Night Care'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="skin_analysis[skin_routine][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['skin_routine'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                </table>


            </div>

            <!-- Life style -->
            <h5 class="mb-3 mt-3">Life style</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table">
                    <tr>
                        <th>Water intakes</th>
                        @foreach (['Low', 'Medium', 'High'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="life_style[water_intakes][]" value="{{ $option }}"
                                        {{ in_array($option, $lifeStyles['water_intakes'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Sleep</th>
                        @foreach (['Good', 'Moderate', 'Poor'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox" name="life_style[sleep][]"
                                        value="{{ $option }}"
                                        {{ in_array($option, $lifeStyles['sleep'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Stress</th>
                        @foreach (['Low', 'Moderate', 'High'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox" name="life_style[stress][]"
                                        value="{{ $option }}"
                                        {{ in_array($option, $lifeStyles['stress'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Sun exposure</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="life_style[sun_exposure][]"
                                    value="Yes"
                                    {{ in_array('Yes', $lifeStyles['sun_exposure'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="life_style[sun_exposure][]"
                                    value="No"
                                    {{ in_array('No', $lifeStyles['sun_exposure'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Smoke</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="life_style[smoke][]"
                                    value="Yes" {{ in_array('Yes', $lifeStyles['smoke'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="life_style[smoke][]"
                                    value="No" {{ in_array('No', $lifeStyles['smoke'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Drinks</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="life_style[drinks][]"
                                    value="Yes"
                                    {{ in_array('Yes', $lifeStyles['drinks'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="life_style[drinks][]"
                                    value="No" {{ in_array('No', $lifeStyles['drinks'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <h5 class="mb-3 mt-3">History</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table border-0">
                    <tr>
                        <th>Medication</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="history[medication][]"
                                    value="yes"
                                    {{ in_array('yes', $histories['medication'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="history[medication][]"
                                    value="no"
                                    {{ in_array('no', $histories['medication'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Hormonal Issues</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="history[hormonal_issues][]"
                                    value="yes"
                                    {{ in_array('yes', $histories['hormonal_issues'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="history[hormonal_issues][]"
                                    value="no"
                                    {{ in_array('no', $histories['hormonal_issues'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Past Treatments</th>
                        @foreach (['peel', 'botox', 'laser', 'other'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox" name="history[past_treatments][]"
                                        value="{{ $option }}"
                                        {{ in_array($option, $histories['past_treatments'] ?? []) ? 'checked' : '' }}>
                                    {{ ucfirst($option) }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Chronic Illness</th>
                        @foreach (['diabetes', 'thyroid', 'other'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox" name="history[chronic_illness][]"
                                        value="{{ $option }}"
                                        {{ in_array($option, $histories['chronic_illness'] ?? []) ? 'checked' : '' }}>
                                    {{ ucfirst($option) }} <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                        <td></td>
                    </tr>
                    <tr>
                        <th>Pregnant</th>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="history[pregnant][]"
                                    value="yes"
                                    {{ in_array('yes', $histories['pregnant'] ?? []) ? 'checked' : '' }}>
                                Yes <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input class="brow-checkbox-dd" type="checkbox" name="history[pregnant][]"
                                    value="no"
                                    {{ in_array('no', $histories['pregnant'] ?? []) ? 'checked' : '' }}>
                                No <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>


            <h5 class="mb-3 mt-3">Conclusion</h5>
            <table class=" table table-bordered new-form-table border-0" cellspacing="0" cellpadding="6"
                width="100%">
                <tr>
                    <th style="width: 200px">Recommended service</th>
                    <td><input type="text" class="browz-gray-input-custom"
                            name="conclusion[recommended_service][desc]"
                            value="{{ $conclusions['recommended_service']['desc'] ?? '' }}" style="width:100%;"></td>
                </tr>
                <tr>
                    <th style="width: 200px">After care</th>
                    <td><input type="text" class="browz-gray-input-custom" name="conclusion[after_care][desc]"
                            value="{{ $conclusions['after_care']['desc'] ?? '' }}" style="width:100%;"></td>
                </tr>
                <tr>
                    <th style="width: 200px">Follow up plan</th>
                    <td><input type="text" class="browz-gray-input-custom" name="conclusion[follow_up_plan][desc]"
                            value="{{ $conclusions['follow_up_plan']['desc'] ?? '' }}" style="width:100%;"></td>
                </tr>
            </table>

            <h5 class="my-3">After care guidelines</h5>
            <ul class="listing-ddd">
                <li>Avoid sun exposure and always apply sunscreen.</li>
                <li>Skip make-up ,hot water , or harsh products for 24-48 hrs.</li>
                <li>Keep skin and body hydrated with water plus moisturizer.</li>
                <li>Use soothing gel /cream[aloe vera , calming lotion] if redness or irritation occurs.</li>
                <li>Follow gentle home-care routine as advised by the therapist.</li>
                <li>Maintain regular salon visits for best results.</li>
            </ul>

            <h5 class="my-3">Declaration</h5>
            <p style="font-size: 16px">I<input
                    style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff; "
                    value="{{ $consultation->customer_name }}">
                her by declare that ;</p>
            <ol class="listing-ddd">
                <li>The information I have provided regarding my skin is true to the best of my knowledge.</li>
                <li>I understand that results of skin services may vary depending upon my skin condition and
                    after care practice.</li>
                <li>I have disclosed any allergies, sensitivities , or health issues that may affect the
                    treatment.</li>
                <li>I consent to undergo the recommended skin services and understand the procedure has been
                    explained to me by the therapist.</li>
                <li>I agree to follow the suggested <b>after care instruction</b> for the best results and to
                    avoid any adverse reactions.</li>
                <li>I release <b>the salon / therapist</b> from responsibility in case of any reaction arising
                    due to undisclosed medical history, allergies, or home-care negligence</li>
            </ol>

            <!-- Permission to use photos/videos -->
            <h4 class="my-3" style="font-size: 16px">Permission to use photos/videos</h4>
            <div class="d-flex align-items-center">
                <label class="brw-checkbox-browz-dds mr-4">
                    <input class="brow-checkbox-dd" type="radio" name="skin_photo_permission" value="1"
                        {{ $consultation->use_photo == '1' ? 'checked' : '' }}>
                    Yes
                    <span class="brow-checkmark"></span>
                </label>
                <label class="brw-checkbox-browz-dds">
                    <input class="brow-checkbox-dd" type="radio" name="skin_photo_permission" value="0"
                        {{ $consultation->use_photo == '0' ? 'checked' : '' }}>
                    No
                    <span class="brow-checkmark"></span>
                </label>
            </div>
            <ul class="listing-ddd">
                <li>Salon will use the photos and videos Respectfully and Professionally.</li>
                <li>These images and videos may be used publicly or digitally to showcase the work and services of
                    <b>BROWNZ</b> salon.
                </li>
            </ul>

            <!-- Signatures -->
            <div class="row row-gap-4">
                <div class="col-12 col-md-8">

                    {{-- Client --}}
                    <div class="row mb-3 align-items-start">
                        <div class="col-sm-6 col-12">
                            <p class="d-flex gap-2">
                                <b>Client name:</b>
                                <input class="border-0 border-bottom bg-white flex-grow-1"
                                    value="{{ $consultation->customer_name }}" readonly>
                            </p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <p class="mb-0">Signature</p>
                                <button type="button" class="btn btn-sm btn-secondary"
                                    onclick="clearSignature('client')">Clear</button>
                            </div>
                            <canvas id="clientCanvas" class="border rounded w-100 sig-canvas"></canvas>
                            <input type="hidden" name="client_sign" id="client_sign"
                                value="{{ $consultation->customer_sign }}">
                        </div>
                    </div>

                    {{-- Stylist --}}
                    <div class="row mb-3 align-items-start">
                        <div class="col-sm-6 col-12">
                            <p class="d-flex gap-2">
                                <b>Stylist name:</b>
                                <input class="border-0 border-bottom bg-white flex-grow-1"
                                    value="{{ $consultation->beautician_name }}" readonly>
                            </p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <p class="mb-0">Signature</p>
                                <button type="button" class="btn btn-sm btn-secondary"
                                    onclick="clearSignature('stylist')">Clear</button>
                            </div>
                            <canvas id="stylistCanvas" class="border rounded w-100 sig-canvas"></canvas>
                            <input type="hidden" name="beautician_sign" id="beautician_sign"
                                value="{{ $consultation->beautician_sign }}">
                        </div>
                    </div>

                    {{-- Manager --}}
                    <div class="row align-items-start">
                        <div class="col-sm-6 col-12">
                            <p class="d-flex gap-2">
                                <b>Manager name:</b>
                                <input class="border-0 border-bottom bg-white flex-grow-1"
                                    value="{{ $consultation->manager_name ?? '' }}" name="manager_name">
                            </p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <p class="mb-0">Signature</p>
                                <button type="button" class="btn btn-sm btn-secondary"
                                    onclick="clearSignature('manager')">Clear</button>
                            </div>
                            <canvas id="managerCanvas" class="border rounded w-100 sig-canvas"></canvas>
                            <input type="hidden" name="manager_sign" id="manager_sign"
                                value="{{ $consultation->manager_sign }}">
                        </div>
                    </div>

                </div>
            </div>

            <button type="submit" class="btn btn-success mt-3">Update Form</button>
        </form>
    </div>

    <script src="{{ asset('admin/js/signature.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function resizeCanvas(canvas) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = canvas.getBoundingClientRect();
                canvas.width = Math.max(rect.width, 1) * ratio;
                canvas.height = Math.max(rect.height, 1) * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
            }

            const clientCanvas = document.getElementById('clientCanvas');
            const stylistCanvas = document.getElementById('stylistCanvas');
            const managerCanvas = document.getElementById('managerCanvas');
            const pads = {
                client: { pad: null, input: document.getElementById('client_sign') },
                stylist: { pad: null, input: document.getElementById('beautician_sign') },
                manager: { pad: null, input: document.getElementById('manager_sign') },
            };

            [clientCanvas, stylistCanvas, managerCanvas].forEach(resizeCanvas);

            pads.client.pad = new SignaturePad(clientCanvas);
            pads.stylist.pad = new SignaturePad(stylistCanvas);
            pads.manager.pad = new SignaturePad(managerCanvas);

            window.clearSignature = function(type) {
                if (!pads[type]) return;
                pads[type].pad.clear();
                pads[type].input.value = '';
            };

            function loadSignature(pad, path) {
                if (!path) return;
                const src = path.indexOf('data:') === 0
                    ? path
                    : "{{ asset('storage') }}/" + path.replace(/^\/+/, '');
                pad.fromDataURL(src);
            }

            loadSignature(pads.client.pad, pads.client.input.value);
            loadSignature(pads.stylist.pad, pads.stylist.input.value);
            loadSignature(pads.manager.pad, pads.manager.input.value);

            document.querySelector('form').addEventListener('submit', () => {
                if (!pads.client.pad.isEmpty()) {
                    pads.client.input.value = pads.client.pad.toDataURL();
                }
                if (!pads.stylist.pad.isEmpty()) {
                    pads.stylist.input.value = pads.stylist.pad.toDataURL();
                }
                if (!pads.manager.pad.isEmpty()) {
                    pads.manager.input.value = pads.manager.pad.toDataURL();
                }
            });

            ['clientCanvas', 'stylistCanvas', 'managerCanvas'].forEach(id => {
                document.getElementById(id).addEventListener('touchmove', e => e.preventDefault(), {
                    passive: false
                });
            });
        });
    </script>
</body>

</html>
