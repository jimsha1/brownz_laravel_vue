<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $primaryKey = 'client_id';
    public $incrementing = false;
    public $timestamps = false;

    /* Category */
    const CATEGORY_VIP = 'vip';
    const CATEGORY_REGULAR = 'regular';
    const CATEGORY_UNSPECIFIED = 'unspecified';

    public function clientsDropdown($default = false)
    {
        $db_clients = self::join('users', 'clients.client_id', '=', 'users.id')
            ->select('clients.client_id', 'users.name', 'username', 'mobile')
            ->get();

        $clients_array = [];
        foreach ($db_clients as $row) {
            $clients_array[$row->client_id] = $row->name . ' (' . $row->mobile . ')';
        }

        return voco_nvp_dropdown($clients_array, $default);
    }

    public function clientsDropdownWithData($default = false)
    {
        $db_clients = self::join('users', 'clients.client_id', '=', 'users.id')
            ->select(
                'clients.client_id',
                'users.name',
                'username',
                'mobile',
                'email',
                'telephone',
                'address',
                'city',
                'state',
                'zip'
            )
            ->get();

        $html = '';
        foreach ($db_clients as $row) {
            $data_attributes = [
                'data-name="' . e($row->name) . '"',
                'data-username="' . e($row->username) . '"',
                'data-mobile="' . e($row->mobile) . '"',
                'data-email="' . e($row->email) . '"',
                'data-telephone="' . e($row->telephone) . '"',
                'data-address="' . e($row->address) . '"',
                'data-city="' . e($row->city) . '"',
                'data-state="' . e($row->state) . '"',
                'data-zip="' . e($row->zip) . '"',
            ];

            $html .= '<option value="' . $row->client_id . '"'
                    . ($default === $row->client_id ? ' selected' : '')
                    . ' ' . implode(' ', $data_attributes)
                    . '>'
                    . $row->name . ' (' . $row->mobile . ')'
                    . '</option>';
        }

        return new \Illuminate\Support\HtmlString($html);
    }

    public function categoryDropdown($default = false)
    {
        return voco_scalar_dropdown($this->getCategory(), $default);
    }

    public function getCategory()
    {
        return [
            self::CATEGORY_VIP,
            self::CATEGORY_REGULAR,
            self::CATEGORY_UNSPECIFIED,
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'client_id');
    }
}
