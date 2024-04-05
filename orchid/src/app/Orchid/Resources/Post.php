<?php

namespace App\Orchid\Resources;

use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\TD;
use Orchid\Screen\Sight;
use Orchid\Crud\Filters\DefaultSorted;

class Post extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Post::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('id')->title('ID'),
            Input::make('title')->title('Name')->placeholder('Enter title here'),
            Relation::make('category_id')->fromModel(\App\Models\Category::class, 'name')->title('Choose your category')->chunk(50)->empty('None'),
        ];
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id'),
            TD::make('title'),
            TD::make('user_id'),
            TD::make('category_id'),
            TD::make('created_at', 'Date of creation')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                }),
            TD::make('updated_at', 'Update date')
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                }),
        ];
    }

    /**
     * Get the sights displayed by the resource.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('id'),
            Sight::make('title'),
            Sight::make('body'),
            Sight::make('user_id'),
            Sight::make('category_id')
        ];
    }

    /**
     * Get the text for the list breadcrumbs.
     *
     * @return string
     */
    public static function listBreadcrumbsMessage(): string
    {
        return static::label();
    }

    /**
     * Get the text for the create breadcrumbs.
     *
     * @return string
     */
    public static function createBreadcrumbsMessage(): string
    {
        return __('New :resource', ['resource' => static::singularLabel()]);
    }

    /**
     * Get the text for the edit breadcrumbs.
     *
     * @return string
     */
    public static function editBreadcrumbsMessage(): string
    {
        return __('Edit :resource', ['resource' => static::singularLabel()]);
    }
}
