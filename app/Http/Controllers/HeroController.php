<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeroController extends Controller
{
    /* get heros */
    public function index()
    {
        $dataArr = [];
        $dataArr['hero_settings'] = Hero::where('user_id', Auth::id())->get();

        return count($dataArr) ? $dataArr : [];
    }

    /* store heros */
    public function storeHero(Request $request)
    {
        $dataArr = [];
        $request->has('name') ? $dataArr['name'] = $request->name : "";
        $request->has('subtitle') ? $dataArr['sub_title'] = $request->subtitle : "";
        if ($request->hasFile('heroImage')) {
            $heroImageFile = $request->file('heroImage');
            $filename = time() . '_' . $heroImageFile->getClientOriginalName();
            $heroImageFile->move(base_path('public'), $filename);
            $dataArr['hero_image_link'] = $filename;
        }

        if ($request->hasFile('resumeFile')) {
            $resumeFile = $request->file('resumeFile');
            $filename = time() . '_' . $resumeFile->getClientOriginalName();
            $resumeFile->move(base_path('public'), $filename);
            $dataArr['resume_link'] = $filename;
        }
        $dataArr['user_id'] = Auth::id();
        $isDataExist = Hero::where('user_id', Auth::id())->first();
        if (!$isDataExist)
            $isSuccess = Hero::create($dataArr)->save();
        else {
            if (isset($dataArr['resume_link']) && $isDataExist->resume_link)
                unlink(base_path('public') . "/{$isDataExist->resume_link}");
            if (isset($dataArr['hero_image_link']) && $isDataExist->hero_image_link)
                unlink(base_path('public') . "/{$isDataExist->hero_image_link}");
            $isSuccess = Hero::where('user_id', Auth::id())->update($dataArr);
        }

        return response()->json(['success' => $isSuccess, 'message' => "action done successfully"]);
    }
}
