<?php

namespace App\Http\Controllers;

use App\Models\AboutCms;
use App\Models\Blog;
use App\Models\Cta;
use App\Models\Hero;
use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use App\Models\ServiceCms;
use App\Models\ServiceFeatures;
use App\Models\Skill;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FrontendController extends Controller
{
    public function getHomePageData()
    {
        $hero = $services = $cta = [];
        $getHero = Hero::where('user_id', 1)->first();
        $hero['name'] = $getHero->name ?? '';
        $hero['sub_title'] = $getHero->sub_title ?? '';
        $hero['hero_image'] = $getHero->hero_image_link ? env('APP_URL') . $getHero->hero_image_link : "";
        $hero['resume_link'] = $getHero->resume_link ? env('APP_URL') . $getHero->resume_link : "";

        $getServiceCms = ServiceCms::select('heading', 'description')->where('user_id', 1)->first();
        $services['cms'] = $getServiceCms ?? ['heading' => "", 'description' => ""];
        $services['services'] = ServiceFeatures::where('user_id', 1)->get();
        foreach ($services['services'] as &$service) {
            $service->icon_link = env('APP_URL') . 'service_icons/' . $service->icon_link;
        }
        $cta = Cta::where('user_id', 1)->first();

        return response()->json([
            'hero' => $hero,
            'services' => $services,
            'cta' => $cta,
        ]);
    }

    /* get about data */
    public function getAboutData()
    {
        $getAboutData = AboutCms::where('user_id', 1)->first();
        $getSkills = Skill::where('user_id', 1)->get();
        return response()->json(['status' => 1, 'data' => $getAboutData, 'skills' => $getSkills]);
    }

    /* get blogs */
    public function getblogs(Request $request)
    {
        $limit = 10;
        $offset = $request->offset;
        if ($offset != Blog::count()) {
            $getBlogs['data'] = Blog::orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            $getBlogs['count'] = Blog::count();
            foreach ($getBlogs['data'] as $key => &$value) {
                $value->thumbnel = env('APP_URL') . 'blog_thumbnel/' . $value->thumbnel;
                $timestamp = strtotime($value->published_at);
                $inputDate = new DateTime();
                $inputDate->setTimestamp($timestamp);
                $formattedDate = $inputDate->format('M d, Y');
                $value->published_at = $formattedDate;
            }
            return $getBlogs;
        }
    }

    /* get single blog */
    public function getSingleBlogs(Request $request)
    {
        $getBlog = [];
        if ($request->has('blog_slug')) {
            $getAuthor =  Hero::where('user_id', 1)->first();
            $getBlog = Blog::where('slug', '=', $request->blog_slug)->first();
            $timestamp = strtotime($getBlog->published_at);
            $inputDate = new DateTime();
            $inputDate->setTimestamp($timestamp);
            $formattedDate = $inputDate->format('M d, Y');
            $getBlog->authorImage = env('APP_URL') . $getAuthor->hero_image_link;
            $getBlog->authorName = $getAuthor->name;
            $getBlog->published_at = $formattedDate;
            $getBlog->thumbnel = env('APP_URL') . 'blog_thumbnel/' . $getBlog->thumbnel;
        }
        // dd($getBlog);
        return $getBlog;
    }

    /* get portfolios */
    public function getPortfolios()
    {
        $getPortfolios = Portfolio::from('portfolios as p')->join('portfolio_category as pc', 'p.category', '=', 'pc.id')->where('p.user_id', 1)->get();
        $getPortfolioCategory =  PortfolioCategory::has('portfolios')->get();
        foreach ($getPortfolios as $key => &$value) {
            $value->icon = env('APP_URL') . "portfolio_images/{$value->icon}";
            $value->category_value = ucfirst($value->category_value);
        }
        $data['getPortfolios'] = $getPortfolios;
        $data['getPortfolioCategory'] = $getPortfolioCategory;
        return $data;
    }

    /* get single portfolio */
    public function getSinglePortfolio(Request $request)
    {
        $getPortfolioData = Portfolio::where('slug', $request->slug)->first();
        $getPortfolioData->icon = env('APP_URL') . "portfolio_images/{$getPortfolioData->icon}";
        return $getPortfolioData;
    }

    /* contact */
    public function sendmessage(Request $request)
    {
        $data = array('name' => "Sam Manna");
        try {

            /* sending mail to user */
            Mail::send('mail', $data, function ($message) use ($request) {
                $message->to($request->email, $request->name)->subject("Thank you for contacting me!");
                $message->from('soumyamanna180898@gmail.com', 'Soumya Manna');
            });

            /* sending mail to admin */
            Mail::raw("New Contact Request recieved \n Name: {$request->name} \n Email: {$request->email} \n Message: {$request->message}", function ($message) {
                $message->to('soumyamanna180898@gmail.com', 'Soumya Manna')->subject("New Contact Request recieved");
                $message->from('soumyamanna180898@gmail.com', 'Soumya Manna');
            });

            return response()->json([
                "status" => 1,
                "message" => "Thank you for your engagement. I look forward to your prompt response. ",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => 0,
                "message" => $th->getMessage(),
            ]);
        }
    }
}
