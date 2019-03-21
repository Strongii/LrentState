<?php

namespace App\Http\Controllers\api;
use Auth;
use App\Http\Requests\api\modifyRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

class userInfoController extends Controller
{
use Helpers;
    //提交微信名称
    public function nickname(Request $request){
        $user = Auth::user()->id;//获取当前登录用户id号
        $niname= $request->nickname;
        $userObject = User::find($user);
        $userObject->nickname = $niname;
        $userObject->save();
        return $this->response->array([
            'optionStatus'=>'success'
            ]);

    }


    //查询用户信息
    public function user(){
       $user = Auth::user();//获取当前登录用户实例
       // $user = $this->user();
        $name = $user->name;
        $phone = $user->phone;
        return $this->response->array([
            'name' => $name,'phone'=>$phone
        ]);

    }

    //用户信息修改
    public function modify(modifyRequest $request){
        $user = Auth::user()->id;
        $name = $request->name;
        $phone = $request->phone;
        $modify = User::find($user);
        $modify->name = $name;
        $modify->phone =$phone;
        $modify->save();
        return $this->response->array([
            'optionStatus'=>'success'
        ]);
    }

}
