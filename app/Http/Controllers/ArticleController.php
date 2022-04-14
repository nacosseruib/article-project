<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CommentModel;
use App\Models\LikeModel;
use App\Models\TagModel;
use App\Models\ViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Article as ArticleResource;
use DB;



//Article Schema
/**
 * @OA\Schema(
 *   schema="ArticleSchema",
 *   title="Article Model", description="Article model",
 *   @OA\Property(
 *     property="id", description="ID of the article",
 *     @OA\Schema(type="number", example=1)
 *  ),
 *   @OA\Property(
 *     property="title", description="title of the article",
 *     @OA\Schema(type="string", example="article title")
 *  ),
 * @OA\Property(
 *     property="thumbnail", description="cover image of the article",
 *     @OA\Schema(type="string", example="article cover")
 *  ),
 * @OA\Property(
 *     property="description", description="details of the article",
 *     @OA\Schema(type="string", example="article details")
 *  )
 * )
 */

 // request parameter
/**
 * @OA\Parameter(
 *   parameter="articles",
 *   name="limit",
 *   description="Limit the number of results",
 *   in="query",
 *   @OA\Schema(
 *     type="number", default=10
 *   )
 * ),
 */

    /**
    * @OA\Get(
    *   path="/articles",
    *   summary="Return the list of articles",
    *   tags={"Hello"},
    *   @OA\Parameter(ref="articles"),
     *    @OA\Response(
    *      response=200,
    *      description="List of articles",
    *      @OA\JsonContent(
    *        @OA\Property(
    *          property="data",
    *          description="List of articles",
    *
    *        )
    *      )
    *    )
    * )
    */

class ArticleController extends ParentController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //Get article List
    public function allArticles()
    {
        $data = [];
        try{
            $articles = Article::orderBy('id', 'desc')->paginate(10);
            $data['articles'] = ArticleResource::collection($articles);
        }catch(\Throwable $e){}

        return $this->sendResponse($data, 'List of all articles');
    }

    //get single article
    public function singleArticle(Request $request, $article_id = null)
    {
        $data = [];
        try{
            $articles = Article::find($article_id);
            $data['articles'] = new ArticleResource($articles);
        }catch(\Throwable $e){}

        return $this->sendResponse($data, 'One article');
    }


    //add article comment
    public function comment(Request $request, $article_id = null)
    {
        $data = [];
        $this->validate($request, [
            'message' => 'required|string',
        ]);
        try{
            $is_save = CommentModel::create([
                'message' => $request['message'],
                'article_id' => $article_id,
            ]);
            $getComment = Article::where('articles.id', $article_id)
                          ->join('comment', 'comment.article_id', '=', "articles.id")
                          ->first(['title', 'comment.message as comment', 'description']);
            $data['comment'] = $getComment;
        }catch(\Throwable $e){
            return $this->sendError(['error' => 'Server Error'], 'Unable to add comment', 500);
        }

        return $this->sendResponse($data, 'One comment');
    }


     //add article like
     public function likeArticle(Request $request, $article_id = null)
     {
         $data = [];
         $this->validate($request, [
             'counter' => 'required|string',
         ]);
         try{
             $is_save = LikeModel::create([
                 'counter' => $request['counter'],
                 'article_id' => $article_id,
             ]);

             $data['liked'] = LikeModel::where('articles.id', $article_id)->count();
         }catch(\Throwable $e){
             return $this->sendError(['error' => 'Server Error'], 'Unable to like article', 500);
         }

         return $this->sendResponse($data, 'all likes');
     }

     //add article view
     public function viewArticle(Request $request, $article_id = null)
     {
         $data = [];
         $this->validate($request, [
             'count' => 'required|string',
         ]);
         try{
             $is_save = ViewModel::create([
                 'count' => $request['count'],
                 'article_id' => $article_id,
             ]);

             $data['views'] = ViewModel::where('articles.id', $article_id)->count();
         }catch(\Throwable $e){
             return $this->sendError(['error' => 'Server Error'], 'Unable to view article', 500);
         }

         return $this->sendResponse($data, 'all viewed');
     }

     //add article tag
     public function tagArticle(Request $request, $article_id = null)
     {
         $data = [];
         $this->validate($request, [
             'count' => 'required|string',
         ]);
         try{
             $is_save = TagModel::create([
                 'url' => $request['url'],
                 'label' => $request['label'],
                 'article_id' => $article_id,
             ]);
             $getTag = Article::where('articles.id', $article_id)
                          ->join('tag', 'tag.article_id', '=', "articles.id")
                          ->first(['url', 'lable', 'title', 'description']);
             $data['tags'] = $getTag;
         }catch(\Throwable $e){
             return $this->sendError(['error' => 'Server Error'], 'Unable to tag article', 500);
         }

         return $this->sendResponse($data, 'all tag');
     }


    //Create new Article
    public function create(Request $request)
    {
        $is_save = null;

        $uploadCompletePathName = $this->uploadPath() . 'profile_images/';
        $uploadCompletePathNameThumbnail300X300 = $uploadCompletePathName . '300x300/';
        $uploadCompletePathNameThumbnail500X500 = $uploadCompletePathName . '500x500/';

        $this->validate($request, [
            'title' => 'required|string|unique:articles',
            //'thumb' => 'image|mimes:png,jpg,jpe,jpeg,gif|max: 2100',
            'description' => 'required|string'
        ]);

        try{

                $is_save = Article::create($request->all());

                if($request->hasFile('thumb'))
                {
                    $getArrayResponse = $this->uploadAnyFile($request['thumb'], $uploadCompletePathName, $maxFileSize = 10, $newExtension = null, $newRadFileName = true);
                    if($getArrayResponse)
                    {
                        if($getArrayResponse['success'])
                        {
                            Article::where('id', $is_save->id)->update([
                                'thumb'         => $getArrayResponse['newFileName'],
                            ]);
                        }
                        //Resize Product Thumbnail - 300X300
                        $this->createThumbnail($uploadCompletePathName . $getArrayResponse['newFileName'], $uploadCompletePathNameThumbnail300X300 . $getArrayResponse['newFileName'], $width = 300, $height = 300);
                        //Resize Product Thumbnail - 500X500
                        $this->createThumbnail($uploadCompletePathName . $getArrayResponse['newFileName'], $uploadCompletePathNameThumbnail500X500 . $getArrayResponse['newFileName'], $width = 500, $height = 500, $is_resize_canvas = 1);
                    }
                }

        }catch(\Throwable $e){
            return $this->sendError(['error' => 'Server Error'], 'Unable to save record', 500);
        }
        $data['articles'] = new ArticleResource($is_save);
        return $this->sendResponse($data, 'List of all articles');
    }
    //
}
