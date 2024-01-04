<?php

namespace App\Http\Controllers;

use App\Models\ServiceCms;
use App\Models\ServiceFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /* store service sectionCms */
    public function storeService(Request $request)
    {
        $jsonString = $request->serviceCMS;
        // Decode the JSON string into an associative array
        $serviceCMSData = json_decode($jsonString, true);
        $isDataAvailble =  ServiceCms::where('user_id', Auth::id())->first();
        if ($isDataAvailble) {
            $serviceCMSData['heading'] ? $isDataAvailble->heading = $serviceCMSData['heading'] : "";
            $serviceCMSData['description'] ? $isDataAvailble->description = $serviceCMSData['description'] : "";
            $isDataAvailble->save();
        } else {
            $data = [];
            $serviceCMSData['heading'] ? $data['heading'] = $serviceCMSData['heading'] : "";
            $serviceCMSData['description'] ? $data['description'] = $serviceCMSData['description'] : "";
            $data['user_id'] = Auth::id();
            ServiceCms::create($data);
        }

        /* handling repeaters */
        $repeaterItems = $request->repeaters;
        $filename = "";
        foreach ($repeaterItems as $key => $value) {
            if ($value['heading'] && $value['description']) {
                if (!$value['itemId']) {
                    /* creating */
                    $serviceIcon = $value['image'];
                    $filename = time() . '_' . $serviceIcon->getClientOriginalName();
                    $serviceIcon->move(base_path('public') . '/service_icons/', $filename);
                    ServiceFeatures::create([
                        'heading' => $value['heading'],
                        'description' => $value['description'],
                        'user_id' => Auth::id(),
                        'icon_link' => $filename,
                    ]);
                } else {
                    /* updating */
                    $getFeatureServices = ServiceFeatures::where('user_id', Auth::id())->where("id", $value['itemId'])->first();
                    if ($getFeatureServices) {
                        if ($value['image'] && file_exists(base_path('public') . "/service_icons/{$getFeatureServices->icon_link}")) {
                            unlink(base_path('public') . "/service_icons/{$getFeatureServices->icon_link}");
                            $serviceIcon = $value['image'];
                            $filename = time() . '_' . $serviceIcon->getClientOriginalName();
                            $serviceIcon->move(base_path('public') . '/service_icons/', $filename);
                        }

                        $getFeatureServices->heading = $value['heading'];
                        $getFeatureServices->description = $value['description'];
                        if ($filename)
                            $getFeatureServices->icon_link = $filename;
                        $getFeatureServices->save();
                    }
                }
            }
        }
        return response()->json(['status' => 1, "message" => "changes accepeted successfully"]);
    }

    /* fetch services */
    public function fetchServices()
    {
        $servicesCms = ServiceCms::where('user_id', Auth::id())->first();
        $featureServices = ServiceFeatures::where('user_id', Auth::id())->get();
        return response()->json([
            'servicesCms' => $servicesCms ?? "",
            'featureServices' => count($featureServices) > 0 ? $featureServices : [],
        ]);
    }

    /* delete service items */
    public function deleteServiceItems(Request $request)
    {
        if ($request->has('item_id')) {
            $checkDataIfExist = ServiceFeatures::find($request->item_id);
            if ($checkDataIfExist) {
                /* unlinking file first */
                if (file_exists(base_path('public') . "/service_icons/{$checkDataIfExist->icon_link}")) {
                    unlink(base_path('public') . "/service_icons/{$checkDataIfExist->icon_link}");
                    $checkDataIfExist->delete();
                    return response()->json([
                        "status" => 0,
                        "message" => 'operation completed successfully',
                    ]);
                }
            } else {
                return response()->json(['status' => 0, "message" => 'Nothing to delete']);
            }
        }
    }
}
