<?php

namespace App\Http\Controllers\Salon\BeauticianDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\BeauticianRepository;
use App\Models\Beautician;
use Illuminate\Support\Facades\DB;

class UpdateProfilePhotoController extends Controller
{
    public function __invoke(Request $request, BeauticianRepository $beautician_repo, $beautician_id)
    {
        if ($request->beautician_id != $beautician_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Find beautician record
        $beautician = $beautician_repo->model()->findOrFail($beautician_id);

        // Start database transaction
        $response = DB::transaction(function () use ($request, $beautician, $beautician_repo) {
            return $this->saveFromRequest($request, $beautician, $beautician_repo);
        });

        return redirect()->route('tab.salon.beautician.details', [
            'beautician_id' => $response->beautician_id,
            'tab_name' => 'edit',
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function saveFromRequest(Request $request, Beautician $beautician, BeauticianRepository $beautician_repo)
    {
           
            $result = $beautician_repo->uploadProfilePhoto($beautician, $request->file('profile_photo_path'));

        if ($result) {
            voco_flash()->success(__('Profile photo updated!'));
        } else {
            voco_flash()->danger(__('Something went wrong while updating profile photo. Please try again later.'));
        }

        return $beautician;
    }
}
