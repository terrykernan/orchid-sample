<?php

namespace App\Orchid\Layouts\Posts;

use App\Models\Post;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layout;

class ListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    public $target = 'posts';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('title', 'Title')
                ->render(function (Post $post) {
                    return Link::make($post->title)
                        ->route('platform.posts.show', $post);
                }),

            TD::make('body', 'Content')->render(function (Post $post) {
                return Str::limit($post->body, 20, '...');
            }),


            TD::make('Action')->render(
                function (Post $post) {
                    return    Link::make('Edit')->route('platform.posts.edit', $post->id)->icon("envolope");
                }
            ),


        ];
    }
}
