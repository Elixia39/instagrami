<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use GuzzleHttp\Psr7\UploadedFile as Psr7UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Inline\Element\Strong;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ファイルをアップロードできる()
    {
        //$this->withoutExceptionHandling();
        // S3ではなくテスト用のストレージを使用する
        // → storage/framework/testing
        Storage::fake('profiles');
        $up = UploadedFile::fake()->image('photo.jpg');


        $response = $this->actingAs($this->user)
            ->json('POST', route('profile.create'), [
                // ダミーファイルを作成して送信している
                $up
            ]);

        // レスポンスが201(CREATED)であること
        $response->assertStatus(201);

        $photo = Photo::first();

        // 写真のIDが12桁のランダムな文字列であること
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z-_]{12}$/', $photo->id);
        $up->move("storage/framework/testing/disks/profiles");

        // DBに挿入されたファイル名のファイルがストレージに保存されていること
        //Storage::cloud()->assertExists($photo->filename);
        Storage::disk('profiles')->assertExists($photo->filename);

    }

    /**
     * @test
     */
    public function should_データベースエラーの場合はファイルを保存しない()
    {
        //$this->withoutExceptionHandling();

        // 乱暴だがこれでDBエラーを起こす
        Schema::drop('photos');

        Storage::fake('photos');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                UploadedFile::fake()->image('photo.jpg'),
            ]);

        // レスポンスが500(INTERNAL SERVER ERROR)であること
        $response->assertStatus(500);

        // ストレージにファイルが保存されていないこと
        $this->assertEquals(0, count(Storage::disk()->files()));
    }

    /**
     * @test
     */
    public function should_ファイル保存エラーの場合はDBへの挿入はしない()
    {
        //$this->withoutExceptionHandling();

        // ストレージをモックして保存時にエラーを起こさせる
        Storage::shouldReceive('photo')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                UploadedFile::fake()->image('photo.jpg'),
            ]);

        // レスポンスが500(INTERNAL SERVER ERROR)であること
        $response->assertStatus(500);

        // データベースに何も挿入されていないこと
        $this->assertEmpty(Photo::all());
    }
}
