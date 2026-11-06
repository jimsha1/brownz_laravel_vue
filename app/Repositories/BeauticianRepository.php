<?php

namespace App\Repositories;

use App\Models\Beautician;
use App\Repositories\Base\BaseRepository;

class BeauticianRepository extends BaseRepository
{
    public function __construct(Beautician $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\BeauticianHunter
     */
    public function hunter()
    {
        return new \App\Hunters\BeauticianHunter($this->model());
    }

    public function getBeautician($beautician_id, $return_default = false)
    {

        // $beautician = $this->model()
        // ->join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
        // ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
        // ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
        //     ->where('beauticians.beautician_id', $beautician_id)
        //     ->where('beauticians.status', 'A')
        //     ->select(
        //         'beauticians.*',
        //         'bt_user.*',
        //         ...user_relation_columns('c_user', 'u_user')
        //     )
        //     ->first();
        $beautician = $this->model()
            ->join('users', 'beauticians.beautician_id', '=', 'users.id')
            ->leftJoin('employees', 'employees.user_id', '=', 'beauticians.beautician_id')
            ->where('beauticians.beautician_id', $beautician_id)
            ->select(
                'beauticians.*',
                'users.name',
                'users.username',
                'users.email',
                'users.mobile',
                'users.telephone',
                'users.gender',
                'users.profile_photo_path',
                'users.user_status',
                'users.shift_id',
                'users.created_at',
                'users.updated_at',
                'users.created_by',
                'users.updated_by',
                'employees.status as employee_status'
            )
            ->first();

        if ($beautician !== null) {
            unset($beautician->password);

            return $beautician;
        }

        return $return_default ? new Beautician() : null;
    }

    // public function uploadProfilePhoto(Beautician $beautician, $file)
    // {
    //     $user = \App\Models\User::findOrFail($beautician->beautician_id);

    //     // Save existing profile photo
    //     $existing_photo = $user->profile_photo_path;

    //     if ($file->isValid()) {
    //         $path = $file->store('public/' . Beautician::BEAUTICIAN_UPLOAD_PATH);

    //         $user->profile_photo_path = basename($path);
    //         $user->save();

    //         // Delete disk image of existing profile photo
    //         $this->deleteDiskImage($existing_photo);

    //         return true;
    //     }

    //     return false;
    // }

    public function uploadProfilePhoto(Beautician $beautician, $file)
    {
        $user = \App\Models\User::findOrFail($beautician->beautician_id);

        // Save existing profile photo
        $existing_photo = $user->profile_photo_path;

        if ($file->isValid()) {

            $ext = $file->extension();

            $filename = 'image_' . preg_replace('/\s+/', '', $user->name) . '-' . time() . '.' . $ext;

            $path = $file->storeAs('uploads/' . Beautician::BEAUTICIAN_UPLOAD_PATH, $filename, ['disk' => 'public']);

            // Save the filename to the user's profile photo path
            $user->profile_photo_path = $path;
            $user->save();

            // Delete the existing profile photo from the disk
            $this->deleteDiskImage($existing_photo);

            return true;
        }

        return false;
    }


    public function deleteDiskImage($filename)
    {
        if (empty($filename)) {
            return;
        }

        $relative = str_contains($filename, '/')
            ? ltrim($filename, '/')
            : Beautician::BEAUTICIAN_UPLOAD_PATH . '/' . $filename;

        $path = storage_path('app/public/' . $relative);

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
