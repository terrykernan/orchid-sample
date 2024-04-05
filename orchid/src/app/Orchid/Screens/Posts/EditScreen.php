<?php

namespace App\Orchid\Screens\Posts;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use App\Models\Category;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Intervention\Image\Facades\Image;
use Orchid\Screen\Fields\Relation;

class EditScreen extends Screen
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
            'post' => $post
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->post->exists ? "Edit Post" : "Create Post";
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Create')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->post->exists),

            Button::make('Save')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->post->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('delete')
                ->canSee($this->post->exists),
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
            Layout::rows([
                Input::make("post.title")
                    ->title('Title')
                    ->placeholder('enter title here'),

                Relation::make("post.category_id")
                    ->fromModel(Category::class, 'name')
                    ->title('Category')
                    ->placeholder('Choose a category here'),

                Quill::make("post.body")
                    ->title('Content')
                    ->placeholder('enter content here'),

                Cropper::make("post.featured_image")
                    ->title('Featured Image')
                    ->targetId()
                    ->width('400px'),

                Upload::make("post.attachment")
                    ->title("attachment")
                    ->media()
                    ->value(true)
            ])
        ];
    }

    /**
     * Create Or update
     */

    public function createOrUpdate(Request $request)
    {
        $this->post->fill($request->get('post'));

        // add current user id
        $this->post->user_id = $request->user()->id;

        $this->post->save();

        if (!empty($this->post->featured_image)) {
            $attachments_path = "posts/{$this->post->id}/";
            $featuredImage = Attachment::find($this->post->featured_image);
            $this->moveAttachment($featuredImage,  $attachments_path . ($featuredImage->isImage() ? 'images/original/' : ''));
        }

        if ($request->has('post.attachment') && $request->input('post.attachment') != $this->post->attachment->pluck('id')->all()) {
            $this->post->attachment()->sync(
                $request->input('post.attachment')
            );
            // renew model instance
            $post = Post::find($this->post->id);
            $this->moveAttachments($post, $attachments_path);
        }

        Alert::info("post " . ($this->post->exists ?  'updated' : 'created') . " successfully");

        return redirect()->route('platform.posts.list');
    }


    /**
     * delete
     */

    public function delete(Post $post)
    {
        $this->post->delete();

        Alert::info('post deleted successfully');

        return redirect()->route('platform.posts.list');
    }


    /**
     * move attachments to specified path
     */
    private function moveAttachments(Post $post, $path = null)
    {
        $path =  $path ?? "posts/{$post->id}/";

        foreach ($post->attachment as $attachment) {
            $this->moveAttachment($attachment, $path . ($attachment->isImage() ? 'images/original/' : ''));
        }
    }

    /**
     * move attachment to specified path
     */
    private function moveAttachment(Attachment $attachment, string $path)
    {
        $file = $attachment->name . '.' . $attachment->extension;

        Storage::disk($attachment->disk)
            ->move(
                $attachment->path . $file,
                $path . $file
            );

        $attachment->update([
            'path' => $path
        ]);

        if ($attachment->isImage()) {
            $this->generateThumbnail($attachment);
        }
    }

    private function generateThumbnail($attachment)
    {
        $name = $attachment->name . '.' . $attachment->extension;
        $image = Storage::disk($attachment->disk)->get($attachment->path  . $name);
        $image = Image::make($image)->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        });

        $thumbnail_path = str_replace('/original/', '/thumbnail/', $attachment->path);

        if (!Storage::disk($attachment->disk)->exists($thumbnail_path)) {
            Storage::disk('public')->makeDirectory($thumbnail_path);
        }

        $image->save(storage_path('app/public/' . $thumbnail_path  . $name));

        $attachment->update([
            'path' => $thumbnail_path
        ]);
    }
}
