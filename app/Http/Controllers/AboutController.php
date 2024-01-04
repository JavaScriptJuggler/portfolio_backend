<?php

namespace App\Http\Controllers;

use App\Models\AboutCms;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AboutController extends Controller
{
    public function saveAboutCms(Request $request)
    {
        $checkIfAboutCmsExist = AboutCms::where('user_id', Auth::id())->first();
        $is_success = 0;
        if ($checkIfAboutCmsExist) {
            /* updating */
            if ($request->has("description"))
                $checkIfAboutCmsExist->description = $request->description;
            if ($request->has("number_of_project"))
                $checkIfAboutCmsExist->number_of_project = $request->number_of_project;
            if ($request->has("programming_language_known"))
                $checkIfAboutCmsExist->programming_language_known = $request->programming_language_known;
            if ($request->has("framework_known"))
                $checkIfAboutCmsExist->framework_known = $request->framework_known;
            if ($request->has("client_handled"))
                $checkIfAboutCmsExist->client_handled = $request->client_handled;
            $is_success = $checkIfAboutCmsExist->save();
        } else {
            /* creating */
            $data = [];
            $data['user_id'] = Auth::id();
            if ($request->has("description"))
                $data['description'] = $request->description;
            if ($request->has("number_of_project"))
                $data['number_of_project'] = $request->number_of_project;
            if ($request->has("programming_language_known"))
                $data['programming_language_known'] = $request->programming_language_known;
            if ($request->has("framework_known"))
                $data['framework_known'] = $request->framework_known;
            if ($request->has("client_handled"))
                $data['client_handled'] = $request->client_handled;
            $is_success = AboutCms::create($data)->save();
        }

        return response()->json([
            'status' => $is_success,
            'message' => $is_success ? "Changes applied successfully" : "Something went wrong"
        ]);
    }

    /* get about cms */
    public function getAboutCms()
    {
        $getData = AboutCms::where('user_id', Auth::id())->first();
        return response()->json($getData ? $getData : "");
    }

    /* set skills */
    public function saveSkills(Request $request)
    {
        $inputs = $request->all();
        $isSuccess = '';
        if (count($inputs)) {
            foreach ($inputs as $key => $value) {
                if ($value['skillName'] && $value['skillDescription']) {
                    if ($value['itemId']) {
                        $check = Skill::where('id', $value['itemId'])->where('user_id', Auth::id())->first();
                        if ($check) {
                            $check->skill_name = $value['skillName'];
                            $check->Skill_description = $value['skillDescription'];
                            $isSuccess = $check->save();
                        }
                    } else {
                        $data = [];
                        $data['skill_name'] = $value['skillName'];
                        $data['skill_description'] = $value['skillDescription'];
                        $data['user_id'] = Auth::id();
                        $isSuccess = Skill::create($data);
                    }
                }
                if (!$isSuccess)
                    break;
            }
            return response()->json([
                'status' => $isSuccess,
                'message' => $isSuccess ? "changes applied successfully" : "something went wrong",
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => "something went wrong",
            ]);
        }
    }

    /* get skills */
    public function getSkills()
    {
        $getData = Skill::select('skill_name as skillName', 'skill_description as skillDescription', 'id as itemId')->where('user_id', Auth::id())->get();
        return response()->json($getData);
    }

    /* delete skills */
    public function deleteSkills(Request $request)
    {
        $isSuccess = Skill::find($request->itemId)->delete();
        return response()->json([
            'status'=>$isSuccess,
            'message'=>$isSuccess?'Deleted Successfully':"something went wrong",
        ]);
    }
}
