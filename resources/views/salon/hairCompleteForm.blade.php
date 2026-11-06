<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hair Consultation Form</title>
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
        <h3>Hair Consultation Form</h3>

        <form method="POST" action="{{ route('hair.update', $consultation->id) }}">
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
            <h5 class="mb-3 mt-3">Primary Analysis</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table">
                    <!-- Scalp Condition -->
                    <tr>
                        <th>Scalp Condition</th>
                        @foreach (['Normal', 'Oily', 'Dry', 'Dandruff', 'Sensitive', 'Hair loss'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="primary_analysis[scalp_condition][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['scalp_condition'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }}
                                    <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                    <!-- Hair Texture -->
                    <tr>
                        <th>Hair Texture</th>
                        @foreach (['Wavy', 'Curly', 'Straight', 'Coily'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="primary_analysis[hair_texture][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_texture'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }}
                                    <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                        <td colspan="2"></td>
                    </tr>
                    <!-- Hair Density -->
                    <tr>
                        <th>Hair Density</th>
                        @foreach (['Fine', 'Medium', 'Thick'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="primary_analysis[hair_density][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_density'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }}
                                    <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                        <td colspan="3"></td>
                    </tr>
                    <!-- Hair Condition -->
                    <tr>
                        <th>Hair Condition</th>
                        @foreach (['Healthy', 'Dry/damaged', 'Sensitized', 'Split ends'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="primary_analysis[hair_condition][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_condition'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }}
                                    <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                        <td colspan="2"></td>
                    </tr>
                    <!-- Hair Elasticity -->
                    <tr>
                        <th>Hair Elasticity</th>
                        @foreach (['Healthy', 'Weak'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="primary_analysis[hair_elasticity][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_elasticity'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }}
                                    <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                        <td colspan="4"></td>
                    </tr>
                    <!-- Hair Porosity -->
                    <tr>
                        <th>Hair Porosity</th>
                        @foreach (['Low', 'Medium', 'High'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="primary_analysis[hair_porosity][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_porosity'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }}
                                    <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                        <td colspan="3"></td>
                    </tr>
                    <!-- % of Grey -->
                    <tr>
                        <th>% of Grey</th>
                        @foreach (['30%', '50%', '70%', '100%'] as $option)
                            <td>
                                <label class="brw-checkbox-browz-dds">
                                    <input class="brow-checkbox-dd" type="checkbox"
                                        name="primary_analysis[grey_percentage][]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['grey_percentage'] ?? []) ? 'checked' : '' }}>
                                    {{ $option }}
                                    <span class="brow-checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                        <td colspan="2"></td>
                    </tr>
                </table>
            </div>

            <hr>

            <!-- History -->
            <h5 class="mb-3 mt-3">History</h5>


            <div class="table-responsive">
                <table class="table table-bordered new-form-table" cellspacing="0" cellpadding="6" width="100%">

                    <!-- Hair Type -->
                    @foreach (['Virgin', 'Henna', 'Chemically Treated', 'Coloured', 'Keratin'] as $index => $option)
                    <tr>
                        @if ($index === 0)
                        <th rowspan="5">Hair Type</th>
                        @endif
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input type="hidden" name="history[hair_type][{{ $option }}][type]"
                                    value="{{ $option }}">
                                <input type="hidden" name="history[hair_type][{{ $option }}][checked]"
                                    value="false">

                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="history[hair_type][{{ $option }}][checked]" value="true"
                                    {{ !empty($histories['hair_type'][$option]) ? 'checked' : '' }}>
                                {{ $option }}
                                <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                name="history[hair_type][{{ $option }}][description]">{{ $histories['hair_type'][$option] ?? '' }}</textarea>
                        </td>
                    </tr>
                    @endforeach

                    <!-- Allergies -->
                    @foreach (['Hair Dye', 'Chemical Solution', 'Blonder/Lightener', 'Other'] as $index => $option)
                    <tr>
                        @if ($index === 0)
                        <th rowspan="4">Allergies/sensitivities</th>
                        @endif
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input type="hidden" name="history[allergies][{{ $option }}][type]"
                                    value="{{ $option }}">
                                <input type="hidden" name="history[allergies][{{ $option }}][checked]"
                                    value="false">

                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="history[allergies][{{ $option }}][checked]" value="true"
                                    {{ !empty($histories['allergies'][$option]) ? 'checked' : '' }}>
                                {{ $option }}
                                <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                name="history[allergies][{{ $option }}][description]">{{ $histories['allergies'][$option] ?? '' }}</textarea>
                        </td>
                    </tr>
                    @endforeach

                    <!-- Medical Condition -->
                    @foreach (['Pregnant', 'Medicine Conception'] as $index => $option)
                    <tr>
                        @if ($index === 0)
                        <th rowspan="2">Medical condition</th>
                        @endif
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input type="hidden" name="history[medical_condition][{{ $option }}][type]"
                                    value="{{ $option }}">
                                <input type="hidden" name="history[medical_condition][{{ $option }}][checked]"
                                    value="false">

                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="history[medical_condition][{{ $option }}][checked]" value="true"
                                    {{ !empty($histories['medical_condition'][$option]) ? 'checked' : '' }}>
                                {{ $option }}
                                <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                name="history[medical_condition][{{ $option }}][description]">{{ $histories['medical_condition'][$option] ?? '' }}</textarea>
                        </td>
                    </tr>
                    @endforeach

                    <!-- Home Care -->
                    <tr>
                        <th>Home care</th>
                        @php $option = 'Shampoo/Conditioner/Serum'; @endphp
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input type="hidden" name="history[home_care][{{ $option }}][type]"
                                    value="{{ $option }}">
                                <input type="hidden" name="history[home_care][{{ $option }}][checked]"
                                    value="false">

                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="history[home_care][{{ $option }}][checked]" value="true"
                                    {{ !empty($histories['home_care'][$option]) ? 'checked' : '' }}>
                                {{ $option }}
                                <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                name="history[home_care][{{ $option }}][description]">{{ $histories['home_care'][$option] ?? '' }}</textarea>
                        </td>
                    </tr>

                    <!-- Oiling Habit -->
                    <tr>
                        <th>Oiling Habit</th>
                        @php $option = 'Frequency/Type of Oil'; @endphp
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input type="hidden" name="history[oiling_habit][{{ $option }}][type]"
                                    value="{{ $option }}">
                                <input type="hidden" name="history[oiling_habit][{{ $option }}][checked]"
                                    value="false">

                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="history[oiling_habit][{{ $option }}][checked]" value="true"
                                    {{ !empty($histories['oiling_habit'][$option]) ? 'checked' : '' }}>
                                {{ $option }}
                                <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                name="history[oiling_habit][{{ $option }}][description]">{{ $histories['oiling_habit'][$option] ?? '' }}</textarea>
                        </td>
                    </tr>

                    <!-- Styling -->
                    <tr>
                        <th>Use of styling product</th>
                        @php $option = 'Heating Tools/Styling Creams'; @endphp
                        <td>
                            <label class="brw-checkbox-browz-dds">
                                <input type="hidden" name="history[styling_product][{{ $option }}][type]"
                                    value="{{ $option }}">
                                <input type="hidden" name="history[styling_product][{{ $option }}][checked]"
                                    value="false">

                                <input class="brow-checkbox-dd" type="checkbox"
                                    name="history[styling_product][{{ $option }}][checked]" value="true"
                                    {{ !empty($histories['styling_product'][$option]) ? 'checked' : '' }}>
                                {{ $option }}
                                <span class="brow-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                name="history[styling_product][{{ $option }}][description]">{{ $histories['styling_product'][$option] ?? '' }}</textarea>
                        </td>
                    </tr>

                </table>
            </div>


            <!-- Client Desire -->
            <h5 class="mb-3 mt-3">Client Desire</h5>
            <table class="table table-bordered new-form-table border-0" cellspacing="0" cellpadding="6"
                width="100%">
                @foreach (['service_needed' => 'Service Needed', 'desired_result' => 'Desired Result', 'technique' => 'Technique', 'patch_test' => 'Patch Test', 'product_used' => 'Product Used', 'after_care' => 'After Care'] as $field => $label)
                    <tr>
                        <th>{{ $label }}</th>
                        <td>
                            <input type="text" class="browz-gray-input-custom" style="width:100%;"
                                name="client_desire[{{ $field }}][description1]"
                                value="{{ $desires[$field]['desc1'] ?? '' }}">
                        </td>
                        <td>
                            <input type="text" class="browz-gray-input-custom" style="width:100%;"
                                name="client_desire[{{ $field }}][description2]"
                                value="{{ $desires[$field]['desc2'] ?? '' }}">
                        </td>
                    </tr>
                @endforeach
            </table>

            <hr>

            <!-- Declaration -->
            <h5 class="my-3">Declaration</h5>
            <ul class="listing-ddd">
                @foreach (['confirm the accuracy of the information provided above.', 'understand that chemical services involve potential risks including but not limited to allergic reactions, irritation, hair breakage, or unsatisfactory results.', 'agree to have a patch/strand test performed if recommended by the stylist.', 'understand that results vary based on hair condition, previous services, and aftercare.', 'will follow the recommended post-service care provided by the stylist. I understand the failure to follow the instructions may affect the results or lead to damage.', 'release <b>BROWNZ</b>, its employees, and affiliates from liability in the case of an undesired result, reaction, or damage due to disclosed or undisclosed conditions.', 'have been informed about the cost of service before availing it.'] as $declaration)
                    <li>I <input
                            style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff;"
                            value="{{ $consultation->customer_name }}"> {{ $declaration }}</li>
                @endforeach
            </ul>

            <!-- Contraindications -->
            <h4 class="my-3" style="font-size: 16px; color:#444;">Contraindications: please read carefully</h4>
            <ol class="listing-ddd">
                <li>The purpose, procedure, and expected results of the service have been clearly explained to me by the
                    stylist.</li>
                <li>Post colour cannot do straightening/smoothening for at least 8-10 days.</li>
                <li>In case of pre-lightening to achieve desired result (colour change) may take 2-3 sittings depending
                    on colour coating.</li>
                <li>Any colour after pre-lightening will last up to 10-12 washes.</li>
                <li>Certain services cannot be done on person having Asthma, High Blood pressure, undergoing
                    Chemotherapy, experiencing exceptional hair loss, Infection on scalp, Alopecia, Nits/lice, any
                    specific allergies or pregnant women and feeding mothers.</li>
            </ol>

            <!-- Permission to use photos/videos -->
            <h4 class="my-3" style="font-size: 16px">Permission to use photos/videos</h4>
            <div class="d-flex align-items-center">
                <label class="brw-checkbox-browz-dds mr-4">
                    <input class="brow-checkbox-dd" type="radio" name="photo_permission" value="1"
                        {{ $consultation->use_photo == '1' ? 'checked' : '' }}>
                    Yes
                    <span class="brow-checkmark"></span>
                </label>
                <label class="brw-checkbox-browz-dds">
                    <input class="brow-checkbox-dd" type="radio" name="photo_permission" value="0"
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
