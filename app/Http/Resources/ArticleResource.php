<?php

namespace App\Http\Resources;

use App\Models\Article;
use App\Models\Comment;
use App\Models\favouriteBlogs;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this[0]->collection);
        $user = User::find($this[0]->user_id);
        $menu = Menu::find($this[0]->menu_id);
        $comments = Comment::where('article_id', $this[0]->id)->count();
        $favourites = DB::table('favourites')->where('reference_id', $this[0]->id)->where('reference_type', 'articles')->count();
        return [
            "id" => $this[0]->id,
            "user" => $user->username,
            "menu" => $menu->name,
            "title" => $this[0]->title,
            "content" => $this[0]->content,
            "avatar" => $this[0]->avatar,
            "comments_total" => $comments,
            "favourites_total" => $favourites,
        ];
    }
}
