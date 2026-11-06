<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Consultation Form For Skin</title>

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
        <h4 class="text-center mb-4 mt-4" style="font-size: 18px;">CONSULTATION FORM FOR SKIN</h4>
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

            <h5 class="sub-head-p mt-5">
                Skin Analysis
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table mt-2">
                    <!-- Skin Type -->
                    @php
                        $skinType = collect($consultation->skin_analyses)->firstWhere(
                            'skin_analyses_title',
                            'skin_type',
                        );
                        $skinTypeValues = $skinType ? json_decode($skinType->skin_analyses_values, true) : [];
                    @endphp
                    <tr>
                        <th>Skin Type</th>
                        @foreach (['Normal', 'Dry', 'Oily', 'Combination'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="skin_type[]" value="{{ $option }}"
                                        {{ in_array($option, $skinTypeValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Skin Concern -->
                    @php
                        $skinConcern = collect($consultation->skin_analyses)->firstWhere(
                            'skin_analyses_title',
                            'skin_concern',
                        );
                        $skinConcernValues = $skinConcern ? json_decode($skinConcern->skin_analyses_values, true) : [];
                    @endphp
                    <tr>
                        <th>Skin Concern</th>
                        @foreach (['Acne', 'Pigmentation', 'Dullness', 'Wrinkles', 'Pore', 'Other'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="skin_concern[]" value="{{ $option }}"
                                        {{ in_array($option, $skinConcernValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Skin Allergy -->
                    @php
                        $skinAllergy = collect($consultation->skin_analyses)->firstWhere(
                            'skin_analyses_title',
                            'skin_allergy',
                        );
                        $skinAllergyValues = $skinAllergy ? json_decode($skinAllergy->skin_analyses_values, true) : [];
                    @endphp
                    <tr>
                        <th>Skin Allergy</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="skin_allergy[]" value="{{ $option }}"
                                        {{ in_array($option, $skinAllergyValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Skin Sensitivity -->
                    @php
                        $skinSensitivity = collect($consultation->skin_analyses)->firstWhere(
                            'skin_analyses_title',
                            'skin_sensitivity',
                        );
                        $skinSensitivityValues = $skinSensitivity
                            ? json_decode($skinSensitivity->skin_analyses_values, true)
                            : [];
                    @endphp
                    <tr>
                        <th>Skin Sensitivity</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="skin_sensitivity[]" value="{{ $option }}"
                                        {{ in_array($option, $skinSensitivityValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Skin Routine -->
                    @php
                        $skinRoutine = collect($consultation->skin_analyses)->firstWhere(
                            'skin_analyses_title',
                            'skin_routine',
                        );
                        $skinRoutineValues = $skinRoutine ? json_decode($skinRoutine->skin_analyses_values, true) : [];
                    @endphp
                    <tr>
                        <th>Skin Routine</th>
                        @foreach (['Cleanser', 'Moisturiser', 'Sunscreen', 'Serum', 'Toner', 'Night Care'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="skin_routine[]" value="{{ $option }}"
                                        {{ in_array($option, $skinRoutineValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                </table>
            </div>


            <h5 class="sub-head-p mt-3">Life style</h5>
            <div class="table-responsive">
                <table class="table table-bordered new-form-table mt-2">
                    <!-- Water intakes -->
                    @php
                        $waterIntake = collect($consultation->skin_lifestyles)->firstWhere(
                            'skin_lifestyle_title',
                            'water_intakes',
                        );
                        $waterIntakeValues = $waterIntake ? json_decode($waterIntake->skin_lifestyle_values, true) : [];
                    @endphp
                    <tr>
                        <th>Water Intakes</th>
                        @foreach (['Low', 'Medium', 'High'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="water_intakes[]" value="{{ $option }}"
                                        {{ in_array($option, $waterIntakeValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Sleep -->
                    @php
                        $sleep = collect($consultation->skin_lifestyles)->firstWhere('skin_lifestyle_title', 'sleep');
                        $sleepValues = $sleep ? json_decode($sleep->skin_lifestyle_values, true) : [];
                    @endphp
                    <tr>
                        <th>Sleep</th>
                        @foreach (['Good', 'Moderate', 'Poor'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="sleep[]" value="{{ $option }}"
                                        {{ in_array($option, $sleepValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Stress -->
                    @php
                        $stress = collect($consultation->skin_lifestyles)->firstWhere('skin_lifestyle_title', 'stress');
                        $stressValues = $stress ? json_decode($stress->skin_lifestyle_values, true) : [];
                    @endphp
                    <tr>
                        <th>Stress</th>
                        @foreach (['Low', 'Moderate', 'High'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="stress[]" value="{{ $option }}"
                                        {{ in_array($option, $stressValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Sun exposure -->
                    @php
                        $sunExposure = collect($consultation->skin_lifestyles)->firstWhere(
                            'skin_lifestyle_title',
                            'sun_exposure',
                        );
                        $sunExposureValues = $sunExposure ? json_decode($sunExposure->skin_lifestyle_values, true) : [];
                    @endphp
                    <tr>
                        <th>Sun Exposure</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="sun_exposure[]" value="{{ $option }}"
                                        {{ in_array($option, $sunExposureValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Smoke -->
                    @php
                        $smoke = collect($consultation->skin_lifestyles)->firstWhere('skin_lifestyle_title', 'smoke');
                        $smokeValues = $smoke ? json_decode($smoke->skin_lifestyle_values, true) : [];
                    @endphp
                    <tr>
                        <th>Smoke</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="smoke[]" value="{{ $option }}"
                                        {{ in_array($option, $smokeValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Drinks -->
                    @php
                        $drinks = collect($consultation->skin_lifestyles)->firstWhere('skin_lifestyle_title', 'drinks');
                        $drinksValues = $drinks ? json_decode($drinks->skin_lifestyle_values, true) : [];
                    @endphp
                    <tr>
                        <th>Drinks</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="drinks[]" value="{{ $option }}"
                                        {{ in_array($option, $drinksValues) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                </table>

            </div>

            <h5 class="sub-head-p mt-3">History</h5>
            <div class="div table-responsive">
                <table class="table table-bordered new-form-table mt-2">
                    <!-- Medication -->
                    @php
                        $medication = collect($consultation->skin_histories)->firstWhere(
                            'skin_history_title',
                            'medication',
                        );
                        $medicationValues = $medication ? json_decode($medication->skin_history_values, true) : [];
                    @endphp
                    <tr>
                        <th>Medication</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="medication[]" value="{{ $option }}"
                                        {{ in_array(strtolower($option), array_map('strtolower', $medicationValues)) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Hormonal Issues -->
                    @php
                        $hormonalIssues = collect($consultation->skin_histories)->firstWhere(
                            'skin_history_title',
                            'hormonal_issues',
                        );
                        $hormonalIssuesValues = $hormonalIssues
                            ? json_decode($hormonalIssues->skin_history_values, true)
                            : [];
                    @endphp
                    <tr>
                        <th>Hormonal Issues</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="hormonal_issues[]" value="{{ $option }}"
                                        {{ in_array(strtolower($option), array_map('strtolower', $hormonalIssuesValues)) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>


                    <!-- Past Treatments -->
                    @php
                        $pastTreatments = collect($consultation->skin_histories)->firstWhere(
                            'skin_history_title',
                            'past_treatments',
                        );
                        $pastTreatmentsValues = $pastTreatments
                            ? array_map('strtolower', json_decode($pastTreatments->skin_history_values, true))
                            : [];
                    @endphp
                    <tr>
                        <th>Past Treatments</th>
                        @foreach (['Peel', 'Botox', 'Laser', 'Other'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="past_treatments[]" value="{{ $option }}"
                                        {{ in_array(strtolower($option), $pastTreatmentsValues) ? 'checked' : '' }}
                                        disabled>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Chronic Illness -->
                    @php
                        $chronicIllness = collect($consultation->skin_histories)->firstWhere(
                            'skin_history_title',
                            'chronic_illness',
                        );
                        $chronicIllnessValues = $chronicIllness
                            ? array_map('strtolower', json_decode($chronicIllness->skin_history_values, true))
                            : [];
                    @endphp
                    <tr>
                        <th>Chronic Illness</th>
                        @foreach (['Diabetes', 'Thyroid', 'Other'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="chronic_illness[]" value="{{ $option }}"
                                        {{ in_array(strtolower($option), $chronicIllnessValues) ? 'checked' : '' }}
                                        disabled>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Pregnant -->
                    @php
                        $pregnant = collect($consultation->skin_histories)->firstWhere(
                            'skin_history_title',
                            'pregnant',
                        );
                        $pregnantValues = $pregnant ? json_decode($pregnant->skin_history_values, true) : [];
                    @endphp
                    <tr>
                        <th>Pregnant</th>
                        @foreach (['Yes', 'No'] as $option)
                            <td>
                                <label class="check-le">{{ $option }}
                                    <input type="checkbox" name="pregnant[]" value="{{ $option }}"
                                        {{ in_array(strtolower($option), array_map('strtolower', $pregnantValues)) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>

                </table>

            </div>


            <!-- Conclusion -->
            <h5 class="sub-head-p mt-3">Conclusion</h5>
            <table class="table table-bordered new-form-table mt-1 mb-5">
                @foreach ($consultation->skin_conclusions as $conclusion)
                    <tr>
                        <th style="width: 200px;">
                            {{ ucwords(str_replace('_', ' ', $conclusion->skin_conclusion_title)) }}</th>
                        <td>
                            <p class="iny-s">{{ $conclusion->skin_conclusion_description }}</p>
                        </td>
                    </tr>
                @endforeach
            </table>


            <div class="py-2"></div>

            <h5 class="mb-1 sub-head-p">
                After care guidelines
            </h5>
            <ul class="listing-ddd mb-2">
                <li>Avoid sun exposure and always apply sunscreen.</li>
                <li>Skip make-up ,hot water , or harsh products for 24-48 hrs.</li>
                <li>Keep skin and body hydrated with water plus moisturizer.</li>
                <li>Use soothing gel /cream[aloe vera , calming lotion] if redness or irritation occurs.</li>
                <li>Follow gentle home-care routine as advised by the therapist.</li>
                <li>Maintain regular salon visits for best results.</li>
            </ul>

            <h5 class="mb-1 sub-head-p">Declaration</h5>
            <p style="font-size: 13px; margin: 0; margin-top: 8px;">I <b>{{ $consultation->customer_name }}</b> her by
                declare that ;</p>
            <ol class="listing-ddd mb-2">
                <li>The information I have provided regarding my skin is true to the best of my knowledge.</li>
                <li>I understand that results of skin services may vary depending upon my skin condition and after care
                    practice.</li>
                <li>I have disclosed any allergies, sensitivities , or health issues that may affect the treatment.</li>
                <li>I consent to undergo the recommended skin services and understand the procedure has been explained
                    to me by the therapist.</li>
                <li>I agree to follow the suggested <b>after care instruction</b> for the best results and to avoid any
                    adverse reactions.</li>
                <li>I release the <b>salon / therapist</b> from responsibility in case of any reaction arising due to
                    undisclosed medical history, allergies, or home-care negligence</li>
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
