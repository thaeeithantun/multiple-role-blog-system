<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Blog;
use App\Http\Resources\Blog as BlogResource;
use App\Http\Resources\BlogCollection;
use Illuminate\Support\Facades\Auth;

class BlogController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/blogs",
     *      tags={"Blogs"},
     *      operationId="get Blogs ",
     *      summary="Get list of blogs",
     *      security={ {"sanctum": {} }},
     *      description="Returns list of blogs",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="blogs",
     *                         type="array",
     *                         collectionFormat="multi",
     *                         @OA\Items(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="Blog Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="description",
     *                                      type="string",
     *                                      description="Blog Description"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="string",
     *                                      description="Blog creater's name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Created at"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated at"
     *                                  )
     *                          )
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Message"
     *                     )
     *                 )
     *             )
     *         }
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */

    public function index()
    {
        $auth_user = Auth::user('api');
        if ($auth_user->hasRole(['admin', 'manager'])) {
            $blogs = Blog::paginate(10);
        } else if ($auth_user->hasRole('user')) {
            $blogs = $auth_user->blogs()->paginate(10);
        }
        return new BlogCollection($blogs);
        // return $this->sendResponse(BlogResource::collection($blogs), 'Blogs fetched.');
    }

    /**
     * @OA\Post(
     *     path="/blogs",
     *     tags={"Blogs"},
     *     summary="Create blog with form data",
     *     operationId="createblogWithForm",
     *     security={
     *         {"sanctum": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     description="Enter Blog Title",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Enter Blog Description",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     * *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="Blog Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="description",
     *                                      type="string",
     *                                      description="Blog Description"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="string",
     *                                      description="Blog creater's name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Created at"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated at"
     *                                  )
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Message"
     *                     )
     * )
     *         }
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), null, 422);
        }
        $input['user_id'] = Auth::user('api')->id;
        $blog = Blog::create($input);
        return $this->sendResponse(new BlogResource($blog), 'Blog created.');
    }


    /**
     * @OA\Get(
     *      path="/blogs/{blogId}",
     *      operationId="getblogById",
     *      tags={"Blogs"},
     *      summary="Get blog information",
     *      description="Returns blog data",
     *      @OA\Parameter(
     *          name="blogId",
     *          description="Blog Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="Blog Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="title",
     *                                      type="string",
     *                                      description="Blog Title"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="description",
     *                                      type="string",
     *                                      description="Blog Description"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_by",
     *                                      type="string",
     *                                      description="Blog creater's name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="Blog Created at"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="Blog Updated at"
     *                                  )
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Message"
     *                     )
     * )
     *         }
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={
     *         {"sanctum": {}}
     *     },
     * )
     */
    public function show($id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return $this->sendError('Blog does not exist.');
        }

        $auth_user = Auth::user('api');
        if ($auth_user->hasRole('user')) {
            if ($blog->user_id !== $auth_user->id) {
                return $this->sendError("You are not authorized to access this Blog.", null, 401);
            }
        }

        return $this->sendResponse(new BlogResource($blog), 'Blog fetched.');
    }

    /**
     * @OA\Put(
     *     path="/blogs/{blogId}",
     *     tags={"Blogs"},
     *     summary="Updates blog with form data",
     *     operationId="updateblogWithForm",
     *     @OA\Parameter(
     *         name="blogId",
     *         in="path",
     *         description="ID of blog that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"sanctum": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     description="Updated title of the blog",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Updated description of the blog",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return $this->sendError('Blog does not exist.');
        }

        $auth_user = Auth::user('api');
        if ($auth_user->hasRole('user')) {
            if ($blog->user_id !== $auth_user->id) {
                return $this->sendError("You are not authorized to update this Blog.", null, 401);
            }
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), null, 422);
        }

        $blog->title = $input['title'];
        $blog->description = $input['description'];
        $blog->user_id = $auth_user->id;
        $blog->save();

        return $this->sendResponse(new BlogResource($blog), 'Blog updated.');
    }

    /**
     * @OA\Delete(
     *      path="/blogs/{blogId}",
     *      operationId="deleteblogById",
     *      tags={"Blogs"},
     *      summary="Delete blog",
     *      description="Delete blog",
     *      @OA\Parameter(
     *          name="blogId",
     *          description="Blog Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      security={
     *         {"sanctum": {}}
     *     },
     * )
     */
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return $this->sendError('Blog does not exist.');
        }

        $auth_user = Auth::user('api');
        if ($auth_user->hasRole('user')) {
            if ($blog->user_id !== $auth_user->id) {
                return $this->sendError("You are not authorized to delete this Blog.", null, 401);
            }
        }

        $blog->delete();
        return $this->sendResponse([], 'Blog deleted.');
    }
}
