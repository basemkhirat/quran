<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Section;
use App\Models\Tag;

class PostsController extends Controller
{

    /**
     * Get all posts
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {

        $limit = request()->filled("limit") ? request()->get("limit") : 20;
        $sort_field = request()->filled("sort_field") ? request()->get("sort_field") : "created_at";
        $sort_direction = request()->filled("sort_direction") ? request()->get("sort_direction") : "DESC";

        $posts = Post::where("status", 1)
            ->where("lang", app()->getLocale())
            ->orderBy($sort_field, $sort_direction);

        if ($request->filled("q")) {
            $posts->where('title', "like", "%" . $request->get("q") . "%");
        }

        if ($request->filled("category_id")) {
            $posts->whereHas('sections', function ($query) {
                $query->where("sections.id", Request()->get("category_id"));
            });
        }

        if ($request->filled("tag_id")) {
            $posts->whereHas('tags', function ($query) {
                $query->where("tags.id", Request()->get("tag_id"));
            });
        }

        if ($request->filled("last_update")) {
            $date = $request->get("last_update");

            if (is_numeric($date)) {
                $date = date("Y-m-d H:i:s", $date);
            }

            $posts->where('updated_at', ">=", $date);
        }

        $posts = $posts->paginate($limit);

        return response()->success($posts);
    }

    /**
     * Get all sections
     * @param Request $request
     * @return mixed
     */
    public function sections()
    {

        $sort_field = request()->filled("sort_field") ? request()->get("sort_field") : "id";
        $sort_direction = request()->filled("sort_direction") ? request()->get("sort_direction") : "asc";

        $sections = Section::join("sections_translations", "sections_translations.section_id", "=", "sections.id")
            ->where("sections_translations.language_id", config("main.locales." . app()->getLocale() . ".id"))
            ->orderBy($sort_field, $sort_direction);

        $sections->select(
            "id",
            "name",
            "image_url"
        );

        $rows = $sections->get()->map(function ($row) {
            $posts_count = Post::whereHas("sections", function ($query) use ($row) {
                return $query->where("sections.id", $row->id);
            })->count();

            $row->posts_count = $posts_count;
            return $row;
        });

        return response()->success($rows);
    }

    /**
     * Get section details
     * @param Request $request
     * @return mixed
     */
    public function section($id)
    {

        $section = Section::join("sections_translations", "sections_translations.section_id", "=", "sections.id")
            ->where("sections_translations.language_id", config("main.locales." . app()->getLocale() . ".id"))
            ->where("sections.id", $id);

        $section->select(
            "id",
            "name",
            "image_url"
        );

        $section = $section->first();

        if (!$section) {
            return abort(404, "Section not found");
        }

        $section->posts_count = Post::whereHas("sections", function ($query) use ($section) {
            return $query->where("sections.id", $section->id);
        })->count();

        return response()->success($section);
    }

    /**
     * Get tag details
     * @param Request $request
     * @return mixed
     */
    public function tag($id)
    {

        $tag = Tag::where("tags.id", $id);

        $tag->select(
            "id",
            "name"
        );

        $tag = $tag->first();

        if (!$tag) {
            return abort(404, "Tag not found");
        }

        $tag->posts_count = Post::whereHas("tags", function ($query) use ($tag) {
            return $query->where("tags.id", $tag->id);
        })->count();

        return response()->success($tag);
    }

    /**
     * Get post details
     * @param Request $request
     * @return mixed
     */
    public function details($id)
    {
        $post = Post::where("id", $id)->where("lang", app()->getLocale())->first();

        if (!$post) {
            return abort(404, "Post not found");
        }

        return response()->success($post);
    }

    /**
     * View increment
     * @param Request $request
     * @return mixed
     */
    public function view($id)
    {
        $post = Post::where("id", $id)->first();

        $post->views = $post->views + 1;
        $post->timestamps = false;

        $post->save();

        return response()->success([
            "status" => true
        ]);
    }
}
