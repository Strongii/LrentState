<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1',['namespace' =>'App\Http\Controllers\api'],function ($api) {

//
//    'middleware' => 'api.throttle',
//        'limit' => config('api.rate_limits.sign.limit'),
//        'expires' => config('api.rate_limits.sign.expires'),
    $api->group([],
        function ($api){
        //微信登录
        $api->get('social/Auth','AuthController@socialStore');
        //刷新token
        $api->PUT('refresh','AuthController@refresh');
        $api->get('test','UserInfoController@test');
      //      $api->post('postOrder','OrderController@postOrder');

        //需要token验证
        $api->group(['middleware'=>'api.auth'],function ($api){
            //微信支付
            $api->any('payReq','WeChatController@orderPay');
            //提交微信名称
            $api->post('storeNickName','UserInfoController@nickname');
            //查询用户信息
            $api->post('user','UserInfoController@user');
            //用户信息修改
            $api->post('modifyInfo','UserInfoController@modify');
            //订单查询
            $api->post('order','OrderController@searchOrder');
            //提交订单
            $api->post('postOrder','OrderController@postOrder');
            //汽车数量查询
            $api->post('carNumber','CarController@carnumber');
            //酒店数量查询
            $api->post('hotelNumber','HotelController@hotelnumber');
        });



    });

});

