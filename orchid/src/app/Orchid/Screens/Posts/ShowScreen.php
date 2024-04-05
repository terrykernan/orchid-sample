<?php

namespace App\Orchid\Screens\Posts;

use App\Models\Post;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class ShowScreen extends Screen
{
    public $post;
    /**
     * Query data.
     * @param Post $post
     * @return array
     */
    public function query(Post $post): iterable
    {

        $post->load('attachment');
        return [
            "post" => $post
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Post page';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [

            Link::make('Edit')
                ->icon('note')
                ->route('platform.posts.edit', $this->post->id),


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
            Layout::legend('post', [
                Sight::make('id')->popover(''),

                Sight::make('title')->render(function ($post) {
                    return "<h2> {$post->title} </h2>";
                }),
                Sight::make('featured image')->render(function ($post) {
                    if($post->featuredImage) {
                        return '<img style="width:300px; border-radius:5px;" src="' . $post->featuredImage->url . '" />';
                    }
                }),
                Sight::make('body')->render(function (Post $post) {
                    return "<div> {$post->body} </div>";
                }),

                Sight::make('Created / Updated')->render(function (Post $post) {
                    return  $post->created_at->toDateTimeString() .
                        ' / ' .
                        '<span style="color:#00209b;" > ' .
                        $post->updated_at->toDateTimeString() .
                        '</span> ';
                }),
                Sight::make('attachment', 'images')->render(function ($post) {
                        $content = "<div  style='display:flex; flex-wrap:wrap;'>";
                        foreach ($post->attachment as $attachment) {
                            $content .= "<div>  <img  style='width:250px; margin: 5px; border-radius:5px;' src= '{$attachment->url}' /> </div>";
                        }
                        $content .= "</div>";

                        return $content;
                })
            ])->title(),
        ];
    }



}
