<?php

namespace App\Orchid\Screens\Posts;

use App\Models\Post;
use App\Orchid\Layouts\Posts\ListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Illuminate\Support\Str;

class ListScreen extends Screen
{


    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "posts" => Post::paginate(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'posts';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create new')
                ->icon('pencil')
                ->route('platform.posts.edit')

        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ListLayout::class

        ];
    }
}
