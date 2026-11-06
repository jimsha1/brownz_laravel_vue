<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __invoke(Request $request)
    {
        $db_beauticians = Beautician::join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
            ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
            ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
            ->activeListed()
            ->orderBy('beauticians.beautician_id')
            ->select(
                'beauticians.*',
                'bt_user.*',
                ...user_relation_columns('c_user', 'u_user')
            )
            ->get();

        foreach ($db_beauticians as $row) {
            $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
        }
        $title  = "team";
        return view('website.team', compact('db_beauticians','title'));
    }
}
