<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Photo;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Comment;
use App\Http\Requests\StoreComment;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証が必要
        $this->middleware('auth')->except(['index','download','show']);
    }

    public function index()
    {
        $photos = Photo::with(['owner','likes'])->orderBy(Photo::CREATED_AT,'desc')->paginate();

        return $photos;
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        // 投稿写真の拡張子を取得する
        $extension = $request->photo->extension();

        $photo = new Photo();

        // インスタンス生成時に割り振られたランダムなID値と
        // 本来の拡張子を組み合わせてファイル名とする
        $photo->filename = $photo->id . '.' . $extension;

        // storage/app/public配下に保存する
        //Storage::putFileAs('photos', $photo->filename, 'public');
        $request->photo->storeAs('photos', $photo->filename, 'public');

        // データベースエラー時にファイル削除を行うため
        // トランザクションを利用する
        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            Storage::disk()->delete($photo->filename);
            throw $exception;
        }

        // リソースの新規作成なので
        // レスポンスコードは201(CREATED)を返却する
        return response($photo, 201);
    }

    public function download(Photo $photo)
    {
        if (! Storage::exists('public/photos/'.$photo->filename)) {
            abort(404);
        }

        $filePath = 'public/photos/'.$photo->filename;
        $mimeType = Storage::mimeType($filePath);
        $disposition = 'attachment; filename="'.$photo->filename.'"';
        $headers = [
            //'Content-Type' => 'application/octet-stream',
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition,
        ];


        //dd(storage_path($mimeType));
        // Storage::path('/public/photos/'),
        // Storage::disk('local')->download($filePath),
        // Storage::download($filePath,$photo->filename,$headers),
        // Storage::mimeType('public/photos/'.$photo->filename)
        // );

        //return Storage::download($filePath,$photo->filename,$headers);
        return response(Storage::get('public/photos/'.$photo->filename),200,$headers);
    }

    public function show(string $id)
    {
        $photo = Photo::where('id',$id)->with(['owner','comments.author','likes'])->first();

        return $photo ?? abort(404);
    }

    /**
     * コメント投稿
     * @param Photo $photo
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        // authorリレーションをロードするためにコメントを取得しなおす
        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }

    /**
     * いいね
     * @param string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
        abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ["photo_id" => $id];
    }

    public function unlike(string $id)
    {
        $photo = Photo::where('id',$id)->with('likes')->first();

        if (! $photo) {
        abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id" => $id];
    }
}
