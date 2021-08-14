<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class AuthController extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="L5 OpenApi",
     *      description="L5 Swagger OpenApi description",
     *      @OA\Contact(
     *          email="thaeeithantun.dev@gmail.com"
     *      ),
     *     @OA\License(
     *         name="Apache 2.0",
     *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *     )
     * )
     */

    /**
     
     *
     *  @OA\Server(
     *      url="http://localhost:8000/api",
     *      description="L5 Swagger OpenApi Server"
     * )
     */


    /**
     * @OA\OpenApi(
     *   security={
     *     {
     *       "sanctum": {},
     *     }
     *   }
     * )
     */

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Logs user into system",
     *     operationId="loginUser",
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="User Email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Enter your password",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     
     *     @OA\Response(
     *          response=200,
     *          description="LoggedIn Successfully!",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="token",
     *                                      type="string",
     *                                      description="Bearer Token"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
     *     @OA\Response(
     *         response=404,
     *         description="Invalid username/password supplied"
     *     )
     * )
     */

    public function signin(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] = $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['name'] =  $authUser->name;

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized']);
        }
    }


    /**
     * @OA\Get(
     *      path="/logout",
     *      tags={"Auth"},
     *      operationId="logout ",
     *      summary="User Logout",
     *      security={ {"sanctum": {} }},
     *      description="Delete User's current access token",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
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
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'User Logged out');
    }
}
