<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Consultation Form For Hair</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .page {
            width: 210mm;
            /* A4 width */
            min-height: 297mm;
            /* A4 height */
            margin: 12mm auto;
            padding: 12mm;
            background: #fff;
            box-shadow: 0 0 0.5rem rgba(0, 0, 0, .15);
        }

        .toolbar {
            position: sticky;
            top: 0;
            background: #fff;
            padding: 8px 0;
            z-index: 999;
        }

        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            body {
                margin: 0;
            }

            .toolbar {
                display: none !important;
            }

            .page {
                box-shadow: none;
                margin: 0;
                width: auto;
                min-height: auto;
                padding: 0;
            }
        }

        input,
        textarea,
        select {
            border: none;
            border-bottom: 1px solid #444;
            padding: 2px 0;
            outline: 0;
            background: transparent;
        }

        label {
            font-weight: 500;
        }

        .new-form-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .new-form-table th,
        .new-form-table td {
            border: 0px solid transparent;
            padding: 4px 6px;
            font-size: 14px;
            vertical-align: middle;
        }

        .new-form-table th {
            background: transparent;
            text-align: left;
            padding-left: 0;
            /* width: 180px; */
        }

        .brw-checkbox-browz-dds {
            display: inline-block;
            margin-right: 10px;
            cursor: pointer;
        }

        .brow-checkbox-dd {
            margin-right: 5px;
        }

        .cleint-info-container label {
            font-size: 12px;
            width: 90px;
            display: flex;
            justify-content: space-between;
        }

        .cleint-info-container h6 {
            margin: 0;
            padding-left: 10px;
            font-size: 12px;
        }

        .sub-head-p {
            font-size: 14px;
            color: #1d1d1d;
            margin-bottom: 0;
            font-weight: 600;
        }



        /* check box */
        /* The container */
        .check-le {
            display: block;
            position: relative;
            padding-left: 21px;
            margin-bottom: 0px;
            cursor: pointer;
            font-size: 12px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default checkbox */
        .check-le input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 16px;
            width: 16px;
            background-color: #eee;
            border-radius: 4px;
        }

        /* On mouse-over, add a grey background color */
        .check-le:hover input~.checkmark {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .check-le input:checked~.checkmark {
            background-color: #E6CD7F;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .check-le input:checked~.checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .check-le .checkmark:after {
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .new-form-table th {
            font-size: 12px;
            font-weight: 400;
        }

        .new-form-table td {
            color: #444;
        }

        .new-form-table tr {
            border-color: #e2e2e244;
        }

        @media print {
            .check-le .checkmark {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        hr {
            border-color: #939292;
        }


        .check-le {
            display: flex;
            align-items: center;
            gap: 3px;
            /* small space between box and text */
        }

        .new-form-table tr:last-child {
            border-color: #e7e7e7;
        }

        .iny-s {
            margin: 0;
            font-size: 12px;
            color: #1d1d1d;
        }

        .listing-ddd li {
            font-size: 12px;
        }

        .sign-container b {
            font-size: 12px;
        }

        .sign-container p {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .sign-container {
            object-fit: contain;
        }

        header {
            background-color: #000;
            padding: 12px;
            margin-bottom: 15px;
        }

        @media print {
            header {
                background-color: #1d1d1d !important;
                -webkit-print-color-adjust: exact;
                /* For Chrome, Safari */
                print-color-adjust: exact;
                /* For Firefox */
                color: #fff !important;
                /* make text white */
            }
        }

        header img {
            width: 80%;
            object-fit: contain;
        }

        header p {
            margin: 0;
            color: #fff;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 5px;
        }

        header p i {
            font-size: 12px;
            color: #1d1d1d;
            width: 18px;
            height: 18px;
            display: flex !important;
            justify-content: center;
            align-items: center;
            background-color: #E6CD7F;
            border-radius: 50%;

        }

        .listing-ddd {
            line-height: 1.9;
        }


        @media print {
            footer .yellow {
                background-color: #E6CD7F;
                padding: 10px;
                -webkit-print-color-adjust: exact;
                /* For Chrome, Safari */
                print-color-adjust: exact;
                /* For Firefox */
            }

            footer .bottom {
                background-color: #1d1d1d;
                padding: 20px;
                -webkit-print-color-adjust: exact;
                /* For Chrome, Safari */
                print-color-adjust: exact;
                /* For Firefox */
            }

            footer {
                position: relative;
                width: 100%;
                margin-top: 100%;
                /* spacing from content */
                /* forces footer to start on a new page */
            }

            footer .yellow {
                background: yellow;
                text-align: center;
                padding: 10px;
                font-size: 14px;
                font-weight: bold;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            footer .bottom {
                background: #000;
                height: 10px;
            }
        }

        @media print {

            body,
            html {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            .print-wrapper {
                min-height: 100%;
                display: flex;
                flex-direction: column;
            }

            .content {
                flex: 1;
                /* takes all remaining space */
            }

            footer {
                margin-top: 50%;
                /* pushes footer to bottom of last page */
                width: 100%;
            }

            footer .yellow {
                background: #E6CD7F;
                text-align: center;
                padding: 5px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            footer .bottom {
                background: #1d1d1d;
                height: 30px;
            }
        }

        .toolbar {
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
            padding: 10px 0;
        }

        .toolbar-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .print-btn,
        .close-btn {
            border: none;
            padding: 10px 16px;
            border-radius: 50px;
            font-family: "Roboto", sans-serif !important;
            min-width: max-content;
            font-size: 16px;
            transition: 0.2s;
            cursor: pointer;
        }

        .print-btn {
            background-color: #1D1D1D;
            color: #fff !important;
        }

        .print-btn:hover {
            text-decoration: none;
            background-color: #E6CD7F;
            color: #1D1D1D !important;
        }

        .close-btn {
            background-color: #F0F0F3;
            color: #1D1D1D !important;
        }

        .close-btn:hover {
            background-color: #E6CD7F;
        }
    </style>
</head>

<body>

    <!-- toolbar -->
    <div class="toolbar">
        <div class="container toolbar-actions">
            <button type="button" class="close-btn" onclick="window.close()">Close</button>
            <button type="button" class="print-btn" onclick="window.print()">Print</button>
        </div>
    </div>

    <!-- A4 page -->
    <div class="page">

        <header>
            <div class="row align-items-center">
                <div class="col-4">
                    <img src="{{ asset('admin/images/logo.png?id=4') }}" alt="logo">
                </div>
                <div class="col-8">
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <p><i class="fa fa-phone" aria-hidden="true"></i> +91 9645 333 949</p>
                            <p><i class="fa fa-phone" aria-hidden="true"></i> +91 9745 333 949</p>
                        </div>
                        <div class="col-6">
                            <p><i class="fa fa-map-marker" aria-hidden="true"></i> Manjeri | Perinthalmanna</p>
                            <p><i class="fa fa-envelope-o" aria-hidden="true"></i> brownzbeautysalon@gmail.com</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-0"><i class="fa fa-instagram" aria-hidden="true"></i> brownzbeautysalon</p>
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                </div>
            </div>

        </header>
        <h4 class="text-center mb-3" style="font-size: 16px;">CONSULTATION FORM FOR HAIR</h4>
        <form method="POST" action="#">

            <!-- Client Info -->
            <div class="row mb-2 g-2 cleint-info-container">
                <div class="col-6 d-flex align-items-center">
                    <label for="client_name">Client Name <span>:</span></label>
                    <h6>{{ $consultation->customer_name }}</h6>
                </div>
                <div class="col-3 d-flex align-items-center">
                    <label for="age">Age <span>:</span></label>
                    <h6>{{ $consultation->customer_age }}</h6>
                </div>
                <div class="col-3 d-flex align-items-center">
                    <label for="date">Date <span>:</span></label>
                    <h6>{{ \Carbon\Carbon::parse($consultation->created_at)->format('d/m/Y') }}</h6>
                </div>
                <div class="col-6 d-flex align-items-center">
                    <label for="phone1">Phone 1 <span>:</span></label>
                    <h6>{{ $consultation->customer_phone }}</h6>
                </div>
                <div class="col-6 d-flex align-items-center">
                    <label for="phone2">Phone 2 <span>:</span></label>
                    <h6>{{ $consultation->customer_mobile }}</h6>
                </div>
                <div class="col-6 d-flex align-items-center">
                    <label for="stylist_name">Stylist Name <span>:</span></label>
                    <h6>{{ $consultation->beautician_name }}</h6>
                </div>
                <div class="col-6 d-flex align-items-center">
                    <label for="designation">Designation <span>:</span></label>
                    <h6>{{ $consultation->beautician_designation ?? '-' }}</h6>
                </div>
            </div>
            <hr class="mt-0 mb-1">

            <!-- Primary Analysis -->
            <h5 class=" sub-head-p">Primary Analysis</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table mt-1 mb-2">
                    @php
                        $analyses = collect($consultation->hair_analyses)->mapWithKeys(function ($a) {
                            return [
                                $a->hair_analyses_title => json_decode($a->hair_analyses_values, true) ?? [],
                            ];
                        });
                    @endphp

                    {{-- Scalp Condition --}}
                    <tr>
                        <th>Scalp Condition</th>
                        @foreach (['Normal', 'Oily', 'Dry', 'Dandruff', 'Sensitive', 'Hair loss'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="scalp_condition[]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['scalp_condition'] ?? []) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Hair Texture --}}
                    <tr>
                        <th>Hair Texture</th>
                        @foreach (['Wavy', 'Curly', 'Straight', 'Coily'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="hair_texture[]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_texture'] ?? []) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Hair Density --}}
                    <tr>
                        <th>Hair Density</th>
                        @foreach (['Fine', 'Medium', 'Thick'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="hair_density[]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_density'] ?? []) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Hair Condition --}}
                    <tr>
                        <th>Hair Condition</th>
                        @foreach (['Healthy', 'Dry/damaged', 'Sensitized', 'Split ends'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="hair_condition[]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_condition'] ?? []) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Hair Elasticity --}}
                    <tr>
                        <th>Hair Elasticity</th>
                        @foreach (['Healthy', 'Weak'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="hair_elasticity[]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_elasticity'] ?? []) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Hair Porosity --}}
                    <tr>
                        <th>Hair Porosity</th>
                        @foreach (['Low', 'Medium', 'High'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="hair_porosity[]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['hair_porosity'] ?? []) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    {{-- % of Grey --}}
                    <tr>
                        <th>% of Grey</th>
                        @foreach (['30%', '50%', '70%', '100%'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="grey_percentage[]" value="{{ $option }}"
                                        {{ in_array($option, $analyses['grey_percentage'] ?? []) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>


            <!-- History -->
            {{-- <h5 class=" sub-head-p">History</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table mt-1 mb-2">
                    <!-- Hair Type -->
                    <tr>
                        <th rowspan="5">Hair Type</th>
                        <td>
                            <label class="check-le">Virgin
                                <input type="checkbox" name="hair_type[]" value="Virgin">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Henna
                                <input type="checkbox" name="hair_type[]" value="Henna">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Chemically Treated
                                <input type="checkbox" checked name="hair_type[]" value="Chemically Treated">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;">Lorem ipsum dolor, sit amet consectetur adipisicing
                                elit.</p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Coloured
                                <input type="checkbox" name="hair_type[]" value="Coloured">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Keratin
                                <input type="checkbox" name="hair_type[]" value="Keratin">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>

                    <!-- Allergies -->
                    <tr>
                        <th rowspan="4">Allergies / sensitivities</th>
                        <td>
                            <label class="check-le">Hair Dye
                                <input type="checkbox" name="allergies[]" checked value="Hair Dye">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;">Lorem ipsum dolor, sit amet consectetur adipisicing
                                elit.</p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Chemical solution
                                <input type="checkbox" name="allergies[]" value="Chemical solution">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Blonder / lightener
                                <input type="checkbox" name="allergies[]" value="Blonder/lightener">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Other
                                <input type="checkbox" name="allergies[]" value="Other">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>

                    <!-- Medical Condition -->
                    <tr>
                        <th rowspan="2">Medical condition</th>
                        <td>
                            <label class="check-le">Pregnant
                                <input type="checkbox" name="medical_condition[]" value="Pregnant">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>
                    <tr>
                        <td>
                            <label class="check-le">Medicine conception
                                <input type="checkbox" name="medical_condition[]" checked
                                    value="Medicine conception">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;">Lorem ipsum dolor, sit amet consectetur adipisicing
                                elit.</p>
                    </tr>

                    <!-- Home Care -->
                    <tr>
                        <th>Home care</th>
                        <td>
                            <label class="check-le">Shampoo / conditioner / serum
                                <input type="checkbox" checked name="home_care[]" value="Shampoo/conditioner/serum">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;">Lorem ipsum dolor, sit amet consectetur adipisicing
                                elit.</p>
                    </tr>

                    <!-- Oiling Habit -->
                    <tr>
                        <th>Oiling Habit</th>
                        <td>
                            <label class="check-le">Frequency / Type of oil
                                <input type="checkbox" name="oiling_habit[]" value="Frequency/Type of oil">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;"></p>
                    </tr>

                    <!-- Styling Product -->
                    <tr>
                        <th>Use of styling product</th>
                        <td>
                            <label class="check-le">Heating tools / styling creams
                                <input type="checkbox" checked name="styling_product[]"
                                    value="Heating tools/styling creams">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin: 0; font-size: 12px;">Heating tools</p>
                    </tr>
                </table>
            </div> --}}

            <h5 class="sub-head-p">History</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table mt-1 mb-2">
                    @php
                        $histories = collect($consultation->hair_histories)->mapWithKeys(function ($h) {
                            return [
                                $h->hair_history_title => [
                                    'values' => json_decode($h->hair_history_values, true) ?? [],
                                    'desc' => json_decode($h->hair_history_description, true) ?? [],
                                ],
                            ];
                        });
                    @endphp

                    {{-- Hair Type --}}
                    <tr>
                        <th rowspan="5">Hair Type</th>
                        @foreach (['Virgin', 'Henna', 'Chemically Treated', 'Coloured', 'Keratin'] as $i => $option)
                    <tr>
                        <td>
                            <label class="check-le">{{ $option }}
                                <input type="checkbox" name="hair_type[]" value="{{ $option }}"
                                    {{ in_array($option, $histories['hair_type']['values'] ?? []) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin:0; font-size:12px;">
                                {{ $histories['hair_type']['desc'][$i] ?? '' }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    </tr>

                    {{-- Allergies --}}
                    <tr>
                        <th rowspan="4">Allergies / sensitivities</th>
                        @foreach (['Hair Dye', 'Chemical solution', 'Blonder/lightener', 'Other'] as $i => $option)
                    <tr>
                        <td>
                            <label class="check-le">{{ $option }}
                                <input type="checkbox" name="allergies[]" value="{{ $option }}"
                                    {{ in_array($option, $histories['allergies']['values'] ?? []) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin:0; font-size:12px;">
                                {{ $histories['allergies']['desc'][$i] ?? '' }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    </tr>

                    {{-- Medical Condition --}}
                    <tr>
                        <th rowspan="2">Medical condition</th>
                        @foreach (['Pregnant', 'Medicine conception'] as $i => $option)
                    <tr>
                        <td>
                            <label class="check-le">{{ $option }}
                                <input type="checkbox" name="medical_condition[]" value="{{ $option }}"
                                    {{ in_array($option, $histories['medical_condition']['values'] ?? []) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin:0; font-size:12px;">
                                {{ $histories['medical_condition']['desc'][$i] ?? '' }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    </tr>

                    {{-- Home Care --}}
                    <tr>
                        <th>Home care</th>
                        <td>
                            <label class="check-le">Shampoo / conditioner / serum
                                <input type="checkbox" name="home_care[]" value="Shampoo/conditioner/serum"
                                    {{ in_array('Shampoo/conditioner/serum', $histories['home_care']['values'] ?? []) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin:0; font-size:12px;">
                                {{ $histories['home_care']['desc'][0] ?? '' }}
                            </p>
                        </td>
                    </tr>

                    {{-- Oiling Habit --}}
                    <tr>
                        <th>Oiling Habit</th>
                        <td>
                            <label class="check-le">Frequency / Type of oil
                                <input type="checkbox" name="oiling_habit[]" value="Frequency/Type of oil"
                                    {{ in_array('Frequency/Type of oil', $histories['oiling_habit']['values'] ?? []) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin:0; font-size:12px;">
                                {{ $histories['oiling_habit']['desc'][0] ?? '' }}
                            </p>
                        </td>
                    </tr>

                    {{-- Styling Product --}}
                    <tr>
                        <th>Use of styling product</th>
                        <td>
                            <label class="check-le">Heating tools / styling creams
                                <input type="checkbox" name="styling_product[]" value="Heating tools/styling creams"
                                    {{ in_array('Heating tools/styling creams', $histories['styling_product']['values'] ?? []) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <p style="margin:0; font-size:12px;">
                                {{ $histories['styling_product']['desc'][0] ?? '' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </div>


            <!-- Client Desire -->
            <h5 class="sub-head-p mb-3">Client Desire</h5>
            <table class="table table-bordered new-form-table mt-1 mb-2">
                @foreach ($consultation->hair_client_desires as $desire)
                    <tr>
                        <th style="width: 100px;">
                            {{ ucfirst(str_replace('_', ' ', $desire->hair_client_desires_title)) }}</th>
                        <td>
                            <p class="iny-s">{{ $desire->hair_client_desires_description1 ?? '' }}</p>
                        </td>
                        <td>
                            <p class="iny-s">{{ $desire->hair_client_desires_description2 ?? '' }}</p>
                        </td>
                    </tr>
                @endforeach
            </table>


            <!-- Declaration -->
            <h5 class="mb-1 sub-head-p">Declaration</h5>
            <ul class="listing-ddd mb-2">
                <li>I <b>{{ $consultation->customer_name }}</b> confirm the accuracy of the information provided above.
                </li>
                <li>I <b>{{ $consultation->customer_name }}</b> understand that chemical services involve potential
                    risks including allergic
                    reactions, irritation, hair breakage, or unsatisfactory results.</li>
                <li>I <b>{{ $consultation->customer_name }}</b> agree to have a patch/strand test performed if
                    recommended by the stylist.
                </li>
                <li>I <b>{{ $consultation->customer_name }}</b> understand that results vary based on hair condition,
                    previous services, and
                    aftercare.</li>
                <li>I <b>{{ $consultation->customer_name }}</b> will follow the recommended post-service care provided
                    by the stylist. I
                    understand the failure to follow the instructions may be affect the results or lead to damage</li>
                <li>I <b>{{ $consultation->customer_name }}</b> release <b style="color: #E6CD7F;">BROWNZ</b> , its
                    employees, and
                    affiliates from liability in the case of an undesired result, reaction, or damage due to disclosed
                    or undisclosed conditions.</li>
                <li>I have been informed about the cost of service before availing it.</li>
            </ul>

            <!-- Contraindications -->
            <h4 class="mb-1 " style="font-size: 16px; color:#444;">Contraindications :<b>please read carefully</b>
            </h4>
            <ol class="listing-ddd mb-2">
                <li>The purpose , procedure , and expected results of the service have been clearly explained to me by
                    the stylist </li>
                <li>Post colour cannot do straightening /smoothening for at least 8 - 10 days</li>
                <li>In case of pre-lightening to achieve desired result (colour change ) may take 2-3 sittings depending
                    on colour coating</li>
                <li>Any colour after pre-lightening will last up to 10 -12 washes</li>
                <li>Certain services can not be done on person having Asthma , High Blood pressure , undergoing Chemo
                    therapy , experiencing exceptional hair loss , Infection on scalp , Alopecia , Nits/lice , any
                    specific allergies or pregnant women and feeding mothers</li>
            </ol>

            <!-- Photo Permission -->
            <h5 class="mb-1 sub-head-p">Permission to Use Photos/Videos</h5>
            <div class="d-flex align-items-center mb-2">
                <label class="check-le me-2">Yes
                    <input type="radio" name="use_photo" value="1"
                        {{ $consultation->use_photo == 1 ? 'checked' : '' }} disabled>
                    <span class="checkmark"></span>
                </label>

                <label class="check-le">No
                    <input type="radio" name="use_photo" value="0"
                        {{ $consultation->use_photo == 0 ? 'checked' : '' }} disabled>
                    <span class="checkmark"></span>
                </label>
            </div>
            <ul class="listing-ddd">
                <li>Salon will use the photos and videos Respectfully and Professionally</li>
                <li>These images and videos may be used publicly or digitally to showcase the work and services of
                    <b>BROWNZ</b> salon
                </li>
            </ul>

            <!-- Signatures -->
            <div class="row g-2 sign-container">
                <div class="col-4">
                    <p><b>Client name:</b> {{ $consultation->customer_name }}</p>
                    @if ($consultation->customer_sign)
                        <img src="{{ asset('storage/' . $consultation->customer_sign) }}" alt="Customer Signature"
                            height="80">
                    @endif
                </div>
                <div class="col-4">
                    <p><b>Stylist name:</b> {{ $consultation->beautician_name }}</p>
                    @if ($consultation->beautician_sign)
                        <img src="{{ asset('storage/' . $consultation->beautician_sign) }}"
                            alt="Beautician Signature" height="80">
                    @endif
                </div>
                <div class="col-4">
                    <p><b>Manager name:</b> {{ $consultation->manager_name }}</p>
                    @if ($consultation->manager_sign)
                        <img src="{{ asset('storage/' . $consultation->manager_sign) }}" alt="Manager Signature"
                            height="80">
                    @endif
                </div>
            </div>
        </form>

        <div class="print-wrapper">
            <div class="content">
                <!-- all your form and consultation details here -->
            </div>

            <footer>
                <div class="yellow"></div>
                <div class="bottom"></div>
            </footer>
        </div>


    </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            var params = new URLSearchParams(window.location.search);
            var shouldAutoPrint = params.get('autoprint') === '1';

            function triggerPrint() {
                window.print();
            }

            if (shouldAutoPrint) {
                window.addEventListener('load', function () {
                    setTimeout(triggerPrint, 350);
                });

                window.addEventListener('afterprint', function () {
                    setTimeout(function () {
                        window.close();
                    }, 150);
                });
            }
        })();
    </script>
</body>

</html>
