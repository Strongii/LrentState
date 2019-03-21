<?php

namespace App\Http\Controllers\api;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\JWTAuth;
use PhpParser\Node\Scalar\String_;

class AuthController extends Controller
{
    public function socialStore(Request $request){


        $code = $request->Code;
        $app = \EasyWeChat::miniProgram();
        $wxInfo = $app->auth->session($code);
        $user = User::where('weixin_openid',$wxInfo['openid'])->first();
        if (!$user){
            $user = User::create([
                'weixin_openid'=>$wxInfo['openid'],
            ]);
        }
        $token = Auth::guard('api')->fromUser($user);
//        $cookie = \Cookie('token', '$token', 5);
        return $this->respondWithToken($token)->setStatusCode(201);
       // return $this->response->cookie($cookie);

    }



    public function refresh(){
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token){

        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

}
