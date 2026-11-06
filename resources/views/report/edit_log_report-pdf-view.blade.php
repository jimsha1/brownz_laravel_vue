<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Edit Log Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 16px;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin: 0 0 12px;
        }

        .filters p {
            margin: 0;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #96D4D4;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            text-align: center;
        }

        td.reason {
            text-align: left;
        }
    </style>
</head>

<body>
    <h2><u>Edit Log Report</u></h2>
    <div class="filters">
        <p>From: {{ !empty($start_date) ? $start_date : 'All' }}</p>
        <p>To: {{ !empty($end_date) ? $end_date : 'All' }}</p>
        <p>Type: {{ $source_label ?? 'All' }}</p>
        <p>Edited By: {{ !empty($edited_by_name) ? $edited_by_name : 'All' }}</p>
        <p>Search: {{ !empty($search) ? $search : 'All' }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Edited At</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Edit Reason</th>
                <th>Edited By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $key => $record)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $record['edited_at'] ?: '—' }}</td>
                    <td>{{ $record['source_label'] ?: '—' }}</td>
                    <td>{{ $record['reference_no'] !== '' ? $record['reference_no'] : '—' }}</td>
                    <td class="reason">{{ $record['edit_reason'] ?: '—' }}</td>
                    <td>{{ $record['edited_by_name'] !== '' ? $record['edited_by_name'] : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No edit logs found for the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
