<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository
{
    // SHOULD pass "\App\User" here for permission package
    public function __construct(\App\Models\User $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\UserHunter
     */
    public function hunter()
    {
        return new \App\Hunters\UserHunter($this->model());
    }

    public function getOutlets($user)
    {
        $user_id = ($user instanceof \App\Models\User) ? $user->id : $user;

        return \App\Models\Outlet::join('user_has_outlets', 'user_has_outlets.outlet_id', '=', 'outlets.outlet_id')
            ->where('user_has_outlets.user_id', $user_id)
            ->get();
    }

    public function syncOutlets($user, $outlets)
    {
        $user_id = ($user instanceof \App\Models\User) ? $user->id : $user;

        $assigned_outlet_ids = $this->getOutlets($user)->pluck('outlet_id')->toArray();

        $request_outlet_ids = [];
        foreach ((array) $outlets as $row) {
            $request_outlet_ids[] = ($row instanceof \App\Models\Outlet) ? $row->outlet_id : $row;
        }

        $removables = array_diff($assigned_outlet_ids, $request_outlet_ids);
        $attachables = array_diff($request_outlet_ids, $assigned_outlet_ids);

        if (count($removables) > 0) {
            \App\Models\UserHasOutlet::where('user_id', $user_id)
                ->whereIn('outlet_id', $removables)
                ->delete();
        }

        foreach ($attachables as $outlet_id) {
            $user_outlet = new \App\Models\UserHasOutlet();
            $user_outlet->outlet_id = $outlet_id;
            $user_outlet->user_id = $user_id;
            $user_outlet->created_at = new \DateTime();
            $user_outlet->created_by = Auth::id();

            // save model
            $user_outlet->save();
        }
    }
}
