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

    public function index()
    {
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), 'Users fetched.');
    }

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

    public function show($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return $this->sendError('User does not exist.');
        }
        return $this->sendResponse(new UserResource($user), 'User fetched.');
    }

    public function update(Request $request, User $user)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
        
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->save();
        return $this->sendResponse(new UserResource($user), 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->sendResponse([], 'User deleted.');
    }
}
