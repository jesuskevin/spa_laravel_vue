<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleObserver
{
    public function saving($article)
    {
        $slug = Str::slug($article->title, '-');

        if (Article::where('slug', $slug)->exists()) {
            $slug = $slug . uniqid();
        }

        $article->slug = $slug;
    }
}
