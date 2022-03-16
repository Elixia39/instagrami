<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Photo;
use App\Comment;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_正しい構造のJSONを返却する()
    {
        $this->withoutExceptionHandling();

        factory(Photo::class)->create()->each(function ($photo) {
            $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        });
        $photo = Photo::first();

        $response = $this->json('GET', route('photo.show', [
            'id' => $photo->id,
        ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    "id" => $photo->owner->id,
                    "name" => $photo->owner->name,
                    "profile_image" => "default.png",
                    "url" => "/storage/profiles/default.png",
                ],
                'liked_by_user' => false,
                'likes_count' => 0,
                'comments' => $photo->comments
                    ->sortByDesc('id')
                    ->map(function ($comment) {
                        return [
                            'author' => [
                                "id" => $comment->author->id,
                                "name" => $comment->author->name,
                                "profile_image" => "default.png",
                                "url" => "/storage/profiles/default.png",
                            ],
                            'content' => $comment->content,
                        ];
                    })
                    ->all(),
            ]);
    }

}
