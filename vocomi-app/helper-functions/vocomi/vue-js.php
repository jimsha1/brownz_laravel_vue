<?php

function paginator_vue_data($db_records, $key_by = null)
{
    $array_data = ($key_by === null) ? $db_records->toArray()['data'] : $db_records->keyBy($key_by)->toArray();

    return [
        'db_rows' => $array_data,
        'links' => $db_records->links()->toHtml(),
        'sequence_start' => (($db_records->currentPage() - 1) * $db_records->perPage() + 1),
        'count' => $db_records->count(),
        'total' => $db_records->total(),
    ];
}
