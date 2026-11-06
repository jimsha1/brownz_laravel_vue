<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;
use Hash;

class AdminPasswordController extends Controller
{
    public function changePassword() {
        return view('master.change-password');
    }

    public function savePassword(Request $request) {
        $validator = Validator::make($request->all(),[
            'current_password' => 'required',
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'response_html' => [
                    '.flash-data' => '<div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                    . implode('<br>', $validator->errors()->all())
                    . '</div>',
                ],
            ]);
        }

        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {

            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                'response_html' => [
                    '.flash-data' => '<div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Current password and new password must be different</div>',
                ],
            ]);
            }
            $user_model = User::find($user->id);
            $user_model->password = bcrypt($request->password);
            $user_model->last_password_change = new \DateTime();
            $user_model->save();
            return response()->json([
                'response_html' => [
                    '.flash-data' => '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Password changed successfully</div>',
                ],
            ]);
        }else{
            return response()->json([
                'response_html' => [
                    '.flash-data' => '<div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Current password is wrong</div>',
                ],
            ]);
        }

        
    }
}
