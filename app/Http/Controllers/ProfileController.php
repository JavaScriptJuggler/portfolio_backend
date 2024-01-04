<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    /* save social links */
    public function saveSocialLinks(Request $request)
    {
        $user = User::find(Auth::id());
        $user->facebook = $request->facebook ?? '';
        $user->xhandle = $request->xhandle ?? '';
        $user->linkdin = $request->linkedin ?? '';
        $user->whatsapp = $request->whatsapp ?? '';
        $isSuccess = $user->save();

        return response()->json([
            'status' => $isSuccess,
            'message' => $isSuccess ? 'Social links saved successfully' : 'Something went wrong',
        ]);
    }

    /* get social links */
    public function getSocialLinks()
    {
        $user = User::find(1);
        return collect($user)->only(['facebook', 'xhandle', 'linkdin', 'whatsapp'])->toArray();
    }

    /* change password */
    public function changePassword(Request $request)
    {
        if ($request->has('old_password') && $request->old_password && $request->has('new_password') && $request->new_password) {
            if (Hash::check($request->old_password, Auth::user()->password)) {
                $validator = app('validator')->make($request->all(), [
                    'new_password' => [
                        'required',
                        'string',
                        'min:8',
                        'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*[a-zA-Z])/',
                    ],
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->get('new_password')[0]]);
                }
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->new_password);
                $isSuccess = $user->save();
                return response()->json([
                    'status' => $isSuccess,
                    'message' => $isSuccess ? 'password changed successful' : 'something went wrong',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Old Password is not matched',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Old & current password are required',
            ]);
        }
    }
}
