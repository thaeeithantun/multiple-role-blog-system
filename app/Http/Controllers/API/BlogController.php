<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Blog;
use App\Http\Resources\Blog as BlogResource;
use Illuminate\Support\Facades\Auth;

class BlogController extends BaseController
{
    public function index()
    {
        $auth_user = Auth::user('api');
        if($auth_user->hasRole(['admin', 'manager'])) {
            $blogs = Blog::all();
        } else if($auth_user->hasRole('user')) {
            $blogs = $auth_user->blogs;
        }
        return $this->sendResponse(BlogResource::collection($blogs), 'Blogs fetched.');
    }

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


    public function show($id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return $this->sendError('Blog does not exist.');
        }

        $auth_user = Auth::user('api');
        if($auth_user->hasRole('user')) {
            if($blog->user_id !== $auth_user->id) {
                return $this->sendError("You are not authorized to access this Blog.", null, 401);
            }
        }
        
        return $this->sendResponse(new BlogResource($blog), 'Blog fetched.');
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return $this->sendError('Blog does not exist.');
        }

        $auth_user = Auth::user('api');
        if($auth_user->hasRole('user')) {
            if($blog->user_id !== $auth_user->id) {
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

    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return $this->sendError('Blog does not exist.');
        }

        $auth_user = Auth::user('api');
        if($auth_user->hasRole('user')) {
            if($blog->user_id !== $auth_user->id) {
                return $this->sendError("You are not authorized to delete this Blog.", null, 401);
            }
        }

        $blog->delete();
        return $this->sendResponse([], 'Blog deleted.');
    }
}
