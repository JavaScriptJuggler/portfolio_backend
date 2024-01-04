<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\fileExists;

class PortfolioController extends Controller
{
    /* save portfolio */
    public function savePortfolio(Request $request)
    {
        if (!$request->has('id')) {
            if ($request->has('portfolio_name') && $request->has('portfolio_description') && $request->has('portfolio_short_description') && $request->hasFile('portfolio_image') && $request->has('portfolio_category')) {
                $imageLink =  $this->linkGenerator($request->portfolio_image);
                $data = [
                    'user_id' => Auth::id(),
                    'portfolio_name' => $request->portfolio_name,
                    'portfolio_description' => $request->portfolio_description,
                    'portfolio_short_description' => $request->portfolio_short_description,
                    'slug' => $this->slugGenerator($request->portfolio_name),
                    'icon' => $imageLink,
                ];
                $category = json_decode($request->portfolio_category, true);
                if (array_key_exists('__isNew__', $category) && $category['__isNew__']) {
                    /* inserting category */
                    unset($category['__isNew__']);
                    $categoryId = PortfolioCategory::create([
                        'user_id' => Auth::id(),
                        'category_value' => $category['value'],
                        'category_data' => json_encode($category),
                    ])->id;
                } else {
                    /* find category id */
                    $categoryId = PortfolioCategory::where('user_id', Auth::id())->where('category_value', $category['value'])->first()->id;
                }
                $data['category'] = $categoryId;
                $isPortfolioSave = Portfolio::create($data)->save();
                return response()->json([
                    'status' => $isPortfolioSave,
                    'message' => $isPortfolioSave ? 'Portfolio saved sucessfully' : 'Something went wrong',
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => "Some mandatory fields are missing",
                ]);
            }
        } else {
            if ($request->has('portfolio_name') && $request->has('portfolio_description') && $request->has('portfolio_short_description') && $request->has('portfolio_category')) {
                $findData = Portfolio::find($request->id);
                if ($request->hasFile('portfolio_image')) {
                    $oldFileData = base_path('public') . '/portfolio_images/' . $findData->icon;
                    if (fileExists($oldFileData))
                        @unlink($oldFileData);
                    $imageLink =  $this->linkGenerator($request->portfolio_image);
                    $findData->icon = $imageLink;
                }
                $findData->portfolio_name = $request->portfolio_name;
                $findData->portfolio_description = $request->portfolio_description;
                $findData->portfolio_short_description = $request->portfolio_short_description;
                $category = json_decode($request->portfolio_category, true);
                if (array_key_exists('__isNew__', $category) && $category['__isNew__']) {
                    /* inserting category */
                    unset($category['__isNew__']);
                    $categoryId = PortfolioCategory::create([
                        'user_id' => Auth::id(),
                        'category_value' => $category['value'],
                        'category_data' => json_encode($category),
                    ])->id;
                } else {
                    /* find category id */
                    $categoryId = PortfolioCategory::where('user_id', Auth::id())->where('category_value', $category['value'])->first()->id;
                }
                $findData->category = $categoryId;
                $findData->slug = $this->slugGenerator($request->portfolio_name);
                $isPortfolioSave = $findData->save();
                return response()->json([
                    'status' => $isPortfolioSave,
                    'message' => $isPortfolioSave ? 'Portfolio saved sucessfully' : 'Something went wrong',
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => "Some mandatory fields are missing",
                ]);
            }
        }
    }

    /* get portfolio */
    public function getPortfolio()
    {
        return Portfolio::select('portfolio_category.category_value as category', 'portfolios.id', 'portfolios.icon', 'portfolios.portfolio_name', 'portfolios.portfolio_short_description', 'portfolios.slug')
            ->join('portfolio_category', 'portfolios.category', '=', 'portfolio_category.id')
            ->where('portfolios.user_id', Auth::id())
            ->get();
    }
    /* image link provier */
    public function linkGenerator($file)
    {
        $serviceIcon = $file;
        $filename = time() . '_' . $serviceIcon->getClientOriginalName();
        $serviceIcon->move(base_path('public') . '/portfolio_images/', $filename);
        return $filename;
    }

    /* slug generator */
    public function slugGenerator($heading)
    {
        $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $heading);
        $slug = str_replace(' ', '-', $slug);
        $slug = strtolower($slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    /* search portfolio */
    public function searchPortfolio(Request $request)
    {
        if ($request->has('id') && $request->id) {
            $data = [];
            $getselectedPortfolio = Portfolio::join('portfolio_category', 'portfolios.category', '=', 'portfolio_category.id')->where('portfolios.id', $request->id)->first();

            $getPortfolioCategory = PortfolioCategory::all();

            foreach ($getPortfolioCategory as $key => $value) {
                $data[] = json_decode($value['category_data']);
            }
            $getCategoryOptions = $data;
            return response()->json([
                'status' => 1,
                'data' => [
                    'portfolio_data' => $getselectedPortfolio,
                    'portfolio_categories' => $getCategoryOptions,
                ]
            ]);
        }
    }

    /* get portfolio category */
    public function getPortfolioCategory()
    {
        $getPortfolioCategory = PortfolioCategory::all();
        $data = [];
        foreach ($getPortfolioCategory as $key => $value) {
            $data[] = json_decode($value['category_data']);
        }
        return response()->json([
            'status' => 1,
            'data' => $data,
        ]);
    }

    /* delete portfolio */
    public function deletePortfolio(Request $request)
    {
        if ($request->has('itemId')) {
            $getData = Portfolio::find($request->itemId);
            $fileLink = base_path('public') . '/portfolio_images/' . $getData->icon;
            if (fileExists($fileLink))
                unlink($fileLink);
            $getData->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Portfolio Deleted Successfully',
            ]);
        }
    }
}
