<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{

    /**
     * @OA\Get(
     *      path="/users",
     *      tags={"Users"},
     *      operationId="get users ",
     *      summary="Get list of users",
     *      security={ {"sanctum": {} }},
     *      description="Returns list of users",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="users",
     *                         type="array",
     *                         collectionFormat="multi",
     *                         @OA\Items(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="User Name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="User Email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="role",
     *                                      type="string",
     *                                      description="User Role"
     *                                  ),
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
        $users = User::with('roles')->paginate(10);
        return UserResource::collection($users);
        // return $this->sendResponse(UserResource::collection($users), 'Users fetched.');
    }


    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Create user with form data",
     *     operationId="createuserWithForm",
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
     *                     property="name",
     *                     description="Enter name",
     *                     type="string",
     *                 ),
     * @OA\Property(
     *                     property="email",
     *                     description="Enter email",
     *                     type="string",
     *                 ),
     * @OA\Property(
     *                     property="password",
     *                     description="Enter password",
     *                     type="string",
     *                 ),
     * @OA\Property(
     *                     property="confirm_password",
     *                     description="Confirm Password",
     *                     type="string",
     *                 ),
     * @OA\Property(
     *                     property="role",
     *                     description="Enter Role",
     *                     type="string",
     *                 ),
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
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="User Name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="User Email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="role",
     *                                      type="string",
     *                                      description="User Role"
     *                                  ),
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        $input = $request->all();


        $role = Role::where('name', $input['role'])->first();
        if ($role) {
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $user->assignRole($role);
            return $this->sendResponse(new UserResource($user), 'User created.');
        } else {
            return $this->sendError('Role does not exist');
        }
    }

    /**
     * @OA\Get(
     *      path="/users/{userId}",
     *      operationId="getuserById",
     *      tags={"Users"},
     *      summary="Get user information",
     *      description="Returns user data",
     *      @OA\Parameter(
     *          name="userId",
     *          description="user Id",
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
     *                                      description="User Id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="User Name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="User Email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="role",
     *                                      type="string",
     *                                      description="User Role"
     *                                  ),
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
        $user = User::find($id);
        if (is_null($user)) {
            return $this->sendError('User does not exist.');
        }
        return $this->sendResponse(new UserResource($user), 'User fetched.');
    }

    /**
     * @OA\Put(
     *     path="/users/{userId}",
     *     tags={"Users"},
     *     summary="Updates user with form data",
     *     operationId="updateuserWithForm",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of user that needs to be updated",
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
     *                     property="name",
     *                     description="Updated name of the user",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="Updated email of the user",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     description="Updated role of the user",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $role = Role::where('name', $input['role'])->first();
        if ($role) {
            $user->name = $input['name'];
            $user->email = $input['email'];
            $user->save();
            $user->roles()->detach();
            $user->assignRole($role);
            return $this->sendResponse(new UserResource($user), 'User updated.');
        } else {
            return $this->sendError('Role does not exist');
        }
    }

    /**
     * @OA\Delete(
     *      path="/users/{userId}",
     *      operationId="deleteuserById",
     *      tags={"Users"},
     *      summary="Delete user",
     *      description="Delete user",
     *      @OA\Parameter(
     *          name="userId",
     *          description="user Id",
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
    public function destroy(User $user)
    {
        $user->delete();
        return $this->sendResponse([], 'User deleted.');
    }
}
