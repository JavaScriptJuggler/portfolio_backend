<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

use function PHPUnit\Framework\fileExists;

class BlogController extends Controller
{
    public function getBlog()
    {
        $getBlogList = Blog::all();
        return response()->json([
            'blogData' => $getBlogList,
            "status" => 1,
        ]);
    }

    public function saveBlog(Request $request)
    {
        if ($request->blog_name && $request->blog_content && $request->short_description) {
            if (!$request->has('blog_id')) {
                $isSuccess = Blog::create([
                    'blog_name' => $request->blog_name ?? "",
                    'blog_content' => $request->blog_content ?? "",
                    'short_description' => $request->short_description ?? "",
                    'slug' => $this->createSlug($request->blog_name),
                    'published_at' => date('d-m-Y'),
                    'thumbnel' => $this->linkGenerator($request->thumbnel),
                ])->save();
                return response()->json([
                    'status' => $isSuccess,
                    'message' => $isSuccess ? "blog added successfully" : "Something went wrong",
                ]);
            } else {
                $getData = Blog::find($request->blog_id);
                $getData->blog_name = $request->blog_name ?? "";
                $getData->blog_content = $request->blog_content ?? "";
                $getData->short_description = $request->short_description ?? "";
                $getData->slug = $this->createSlug($request->blog_name);
                if ($request->hasFile('thumbnel')) {
                    if ($getData->thumbnel && fileExists(base_path('public') . '/blog_thumbnel/' . $getData->thumbnel)) {
                        unlink(base_path('public') . '/blog_thumbnel/' . $getData->thumbnel);
                    }
                    $getData->thumbnel = $this->linkGenerator($request->thumbnel);
                }
                $isSuccess = $getData->save();
                return response()->json([
                    'status' => $isSuccess,
                    'message' => $isSuccess ? "Blog updated successfully" : "Something went wrong",
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => "something went wrong",
            ]);
        }
    }

    public function searchBlog(Request $request)
    {
        if ($request->has('blog_id')) {
            $getBlogData = Blog::find($request->blog_id);
            if ($getBlogData) {
                return response()->json([
                    "status" => 1,
                    "message" => "blog fetched successfully",
                    "data" => $getBlogData,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    "message" => "something went wrong",
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                "message" => "something went wrong",
            ]);
        }
    }

    /* delete blog */
    public function deleteBlog(Request $request)
    {
        if ($request->blog_id) {
            Blog::find($request->blog_id)->delete();
            return response()->json([
                'status' => 1,
                "message" => 'Blog Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                "message" => 'Something went wrong.',
            ]);
        }
    }

    /* create slug */
    public function createSlug($blogName)
    {
        $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $blogName);
        $slug = str_replace(' ', '-', $slug);
        $slug = strtolower($slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    /* image link provier */
    public function linkGenerator($file)
    {
        $serviceIcon = $file;
        $filename = time() . '_' . $serviceIcon->getClientOriginalName();
        $serviceIcon->move(base_path('public') . '/blog_thumbnel/', $filename);
        return $filename;
    }
}
