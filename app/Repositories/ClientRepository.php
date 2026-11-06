<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class ClientRepository extends BaseRepository
{
    public function __construct(\App\Models\Client $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\ClientHunter
     */
    public function hunter()
    {
        return new \App\Hunters\ClientHunter($this->model());
    }

    public function getClient($client_id, $return_default = false)
    {
        $client = $this->model()
                ->join('users', 'clients.client_id', '=', 'users.id')
                ->where('clients.client_id', $client_id)
                ->first();

        if ($client === null && $return_default) {
            return new \App\Models\Client();
        }

        return $client;
    }

    public function saveClientAndUser(\App\Models\Client $client, \App\Models\User $user)
    {
        $is_new_user = empty($user->id);

        if ($is_new_user) {
            // Set a temporary `username` while inserting
            $user->username = mt_rand(1, 99999) . mt_rand(11111, 99999);

            // Do not assign any password for users, created from admin console
            $user->password = '';
        }

        // Save user record
        $user->save();

        if ($is_new_user) {
            // Set default `username` for new user
            $user->username = 'user' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $user->save();

            // Save client
            $client->client_id = $user->id;
            $client->save();
        }

        return $client;
    }
}
