<?php

namespace App\Http\Controllers;

use App\Models\Cta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CtaController extends Controller
{
    /* save cta */
    public function saveCta(Request $request)
    {
        if ($request->has('heading') && $request->has('description')) {
            $checkIFExist = Cta::where('user_id', Auth::id())->first();
            if ($checkIFExist) {
                $checkIFExist->heading = $request->heading;
                $checkIFExist->description = $request->description;
                $checkIFExist->save();
            } else {
                Cta::create([
                    "heading" => $request->heading,
                    "description" => $request->description,
                    "user_id" => Auth::id(),
                ]);
            }
            return response()->json([
                'status' => 1,
                'message' => 'Changes updated successfully',
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message' => 'Something went wrong',
            ]);
        }
    }

    /* get cta */
    public function getCta()
    {
        $checkIFExist = Cta::where('user_id', Auth::id())->first();
        return response()->json($checkIFExist ? $checkIFExist : "");
    }
}
