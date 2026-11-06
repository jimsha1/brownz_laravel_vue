<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 24px;
            color: #1d1d1d;
        }

        .container {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            padding: 32px 36px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 2px solid #e6cd7f;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #1d1d1d;
            margin-bottom: 6px;
            text-transform: capitalize;
        }

        .title {
            font-size: 18px;
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-info {
            color: #666;
            font-size: 13px;
            line-height: 1.7;
        }

        .company-info span {
            display: inline-block;
            margin: 0 8px;
            color: #ccc;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 24px;
            margin-bottom: 24px;
            padding: 16px 18px;
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #888;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #1d1d1d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 24px;
            background: #fffdf5;
            border: 1px solid #f0e6c8;
        }

        th {
            background-color: #E6CD7F;
            color: #1d1d1d;
            padding: 12px 14px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }

        td {
            padding: 10px 14px;
            border: 1px solid #f2ead4;
            font-size: 13px;
        }

        tr:nth-child(even) td {
            background-color: #fff9e6;
        }

        .amount-cell {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .total-row td {
            background-color: #f5ebc2 !important;
            font-weight: 700;
        }

        .net-salary td {
            background-color: #E6CD7F !important;
            font-weight: 700;
            font-size: 15px;
        }

        .net-label {
            text-align: right;
        }

        .bottom-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 24px 0 0;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }

        .bottom-info label {
            display: block;
            margin-bottom: 6px;
            color: #666;
            font-weight: 500;
            font-size: 13px;
        }

        .bottom-info .value-line {
            min-height: 28px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
            color: #1d1d1d;
            padding-bottom: 4px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 16px;
        }

        .signature-line {
            width: 200px;
            text-align: center;
        }

        .signature-line hr {
            border: none;
            border-top: 1px solid #666;
            margin-bottom: 8px;
        }

        .signature-line div {
            color: #555;
            font-size: 13px;
            font-weight: 500;
        }

        @media (max-width: 640px) {
            body {
                padding: 12px;
            }

            .container {
                padding: 20px 16px;
            }

            .meta-grid,
            .bottom-info {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
                padding: 16px;
            }
        }
    </style>
</head>
@php
    $salaryInfo = $dataToPage['salaryInfo'];
    $totalAddition = $dataToPage['totalAddition'];
    $totalDeduction = $dataToPage['totalDeduction'];
    $netSalary = $dataToPage['netSalary'];
    $company = $dataToPage['company'] ?? [];
    $companyName = $company['name'] ?? '';
    $companyPhone = $company['phone'] ?? '';
    $companyEmail = $company['email'] ?? '';
    $companyAddress = $company['address'] ?? '';
    $companyCity = $company['city'] ?? '';
    $formatAmount = function ($amount) {
        return '₹ ' . number_format((float) $amount, 2);
    };
@endphp

<body>
    <div class="container">
        <div class="header">
            @if ($companyName)
                <div class="company-name">{{ $companyName }}</div>
            @endif
            <div class="title">Salary Slip</div>
            <div class="company-info">
                @if ($companyAddress || $companyCity)
                    {{ trim($companyAddress . ($companyAddress && $companyCity ? ', ' : '') . $companyCity) }}<br>
                @endif
                @if ($companyPhone)
                    Phone: {{ $companyPhone }}
                @endif
                @if ($companyPhone && $companyEmail)
                    <span>|</span>
                @endif
                @if ($companyEmail)
                    Email: {{ $companyEmail }}
                @endif
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <span class="meta-label">Employee Name</span>
                <span class="meta-value">{{ $salaryInfo->employee->name ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Designation</span>
                <span class="meta-value">{{ $salaryInfo->employee->designation ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Month</span>
                <span class="meta-value">{{ $salaryInfo->month ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Year</span>
                <span class="meta-value">{{ $salaryInfo->year ?? '—' }}</span>
            </div>
        </div>

        <table class="downloads-table">
            <tr>
                <th width="25%">Earnings</th>
                <th width="25%" class="amount-cell">Amount</th>
                <th width="25%">Deductions</th>
                <th width="25%" class="amount-cell">Amount</th>
            </tr>
            <tr>
                <td>Basic Salary</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->basic_salary) }}</td>
                <td>TDS</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->TDS_amount) }}</td>
            </tr>
            <tr>
                <td>TA</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->TA) }}</td>
                <td>Professional Tax</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->professional_tax_amount) }}</td>
            </tr>
            <tr>
                <td>DA</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->DA) }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>HRA</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->HRA) }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Special Allowance (if any)</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->special_allowance) }}</td>
                <td>Leave Deduction</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->deduction) }}</td>
            </tr>
            <tr>
                <td>Overtime</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->overtime_amount) }}</td>
                <td>Advance</td>
                <td class="amount-cell">{{ $formatAmount($salaryInfo->advance) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Addition</td>
                <td class="amount-cell">{{ $formatAmount($totalAddition) }}</td>
                <td>Total Deduction</td>
                <td class="amount-cell">{{ $formatAmount($totalDeduction) }}</td>
            </tr>
            <tr class="net-salary">
                <td colspan="2" class="net-label">NET Payable</td>
                <td colspan="2" class="amount-cell">{{ $formatAmount($netSalary) }}</td>
            </tr>
        </table>

        <div class="bottom-info">
            <div>
                <label>Cheque #</label>
                <div class="value-line">&nbsp;</div>
            </div>
            <div>
                <label>Bank / Account</label>
                <div class="value-line">{{ $salaryInfo->emp_account_no ?: '—' }}</div>
            </div>
            <div>
                <label>Dated As</label>
                <div class="value-line">&nbsp;</div>
            </div>
        </div>

        <div class="signatures">
            <div class="signature-line">
                <hr>
                <div>Employee Signature</div>
            </div>
            <div class="signature-line">
                <hr>
                <div>Director</div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 350);
        });
    </script>
</body>

</html>
