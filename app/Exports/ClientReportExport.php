<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected string $clientName = '',
        protected string $mobile = '',
        protected string $status = 'all'
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Client::with('user')
            ->leftJoin('users', 'users.id', '=', 'clients.client_id')
            ->select('clients.*')
            ->orderBy('users.name', 'desc');

        if ($this->clientName !== '') {
            $query->whereHas('user', function ($subQuery) {
                $subQuery->where('name', 'LIKE', '%' . $this->clientName . '%');
            });
        }

        if ($this->mobile !== '') {
            $query->whereHas('user', function ($subQuery) {
                $subQuery->where('mobile', 'LIKE', '%' . $this->mobile . '%');
            });
        }

        if ($this->status !== '' && strtolower($this->status) !== 'all') {
            $query->whereHas('user', function ($subQuery) {
                $subQuery->where('user_status', $this->status);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Client Name',
            'Username',
            'Mobile',
            'Status',
        ];
    }

    public function map($client): array
    {
        return [
            optional($client->user)->name ?? '',
            optional($client->user)->username ?? '',
            (string) (optional($client->user)->mobile ?? ''),
            ucfirst((string) (optional($client->user)->user_status ?? '')),
        ];
    }
}
