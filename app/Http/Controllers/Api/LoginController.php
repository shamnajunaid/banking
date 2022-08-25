<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{



  //Login
   public function login(Request $request){
   	 $request->validate([
        'email' => 'email|required',
        'password' => 'required'
    ]);
   $user = User::where('email', $request->email)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
    
    $token =  $user->createToken(env('SANCTUM_SECRET'))->plainTextToken;
    $response  = [
      'user'=>$user,
      'token'=>$token
   ];
    return response($response);
    
   }


}
