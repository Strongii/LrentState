<?php

namespace App\Http\Controllers\api;
use App\Models\Good;
use App\Models\Order;
use Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class orderController extends Controller
{
    public function searchOrder(Request $request){
        $user = Auth::user()->id;
        $orderStatus = $request->orderStatus;
        $requestNum = $request->requestNum;

        if ($orderStatus == 1){


            if($requestNum == 0) {
                $order = Order::select('good_id', 'order_status', 'start_time', 'end_time', 'total_price', 'payment_no')->where(['user_id' => $user])
                    ->orderBy('paid_at', 'DESC')->limit(10)->get();
                return $order;
            }else{
                $ordernumber = DB::select('select count(*) as number from orders where user_id = ?', [$user]);
                foreach ($ordernumber as $order) {
                    $number = $order->number;
                }
                $ys = $number % 10;
                $num = ($number - $ys) / 10;

                if($requestNum < $num){
                    $order = Order::select('good_id', 'order_status', 'start_time', 'end_time', 'total_price', 'payment_no')->where(['user_id' => $user])
                        ->orderBy('paid_at', 'DESC')->offset(10*$requestNum)->limit(10)->get();
                    return $order;
                }
                if($requestNum >= $num && $ys != 0){
                    if(($requestNum - $num) >= 1){
                        return $this->response->array([
                            'message'=>'已经没有订单啦asd'
                        ]);
                    }
                    $order = Order::select('good_id', 'order_status', 'start_time', 'end_time', 'total_price', 'payment_no')->where(['user_id' => $user])
                        ->orderBy('paid_at', 'DESC')->offset(10*$num)->limit($ys)->get();
                    return $order;
                }
                if($requestNum >= $num && $ys == 0){
                    if(($requestNum - $num) >=1){
                        return $this->response->array([
                            'message'=>'已经没有订单啦asd'
                        ]);
                    }
                    $order = Order::select('good_id', 'order_status', 'start_time', 'end_time', 'total_price', 'payment_no')->where(['user_id' => $user])
                        ->orderBy('paid_at', 'DESC')->offset(10*$num)->limit(10)->get();
                    return $order;
                }


            }


        }else{
            $order = Order::select('good_id','order_status','start_time','end_time','total_price','payment_no')->where([['user_id','=',$user],['order_status','=',$orderStatus],])
                ->orderBy('paid_at', 'DESC')-> limit(10)->get();
            return $order;
        }
    }

    public static function findAvailableNo(){
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 7 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 7, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!Order::where('payment_no', $no)->exists()) {
                return $no;
            }
        }
        return false;
    }

    public function postOrder(Request $request)
    {

        $user = Auth::user()->id;
        $good_id = $request->good_id;
        $startTime = $request->startTime;
        $endTime = $request->endTime;
        $good = Good::where('id',$good_id)->first();
        $userInfo = User::where('id',$user)->first();
        $name = $userInfo->name;
        $phone = $userInfo->phone;



        if ($good_id == 1) {
            $instartTime = intval($startTime);
            $inendTime = intval($endTime);
            $type = 1;
            $ordernumber = DB::select('select count(*) as number from orders where (start_time >= ? AND (start_time+1) < ? AND end_time >= ? AND good_id = ?) OR (start_time <= ? AND (end_time-1) > ? AND end_time <= ? AND good_id = ?) OR (start_time >= ? AND end_time <= ? AND good_id = ?) OR (start_time <= ? AND end_time >= ? AND good_id = ?)', [$instartTime,$inendTime,$inendTime,$type,$instartTime,$instartTime,$inendTime,$type,$instartTime,$inendTime,$type,$instartTime,$inendTime,$type]);
            foreach ($ordernumber as $order) {
                $number = $order->number;
            }
            $syNumber = $good->count - $number;
            if($syNumber <= 0){
                return $this->response->array([
                'error'=>'该时段无多余房间',
//                'synumber'=>$syNumber,
//                'ordernumber'=>$number,
//                'totalNumber'=>$good->count,
//                    'start'=>$instartTime
                ]);
            }


            $stY = $startTime[0].$startTime[1].$startTime[2].$startTime[3];
            $stM = $startTime[4].$startTime[5];
            $stD = $startTime[6].$startTime[7];
            $etY = $endTime[0].$endTime[1].$endTime[2].$endTime[3];
            $etM = $endTime[4].$endTime[5];
            $etD = $endTime[6].$endTime[7];
            $stDate = $stY.'-'.$stM.'-'.$stD;
            $etDate = $etY.'-'.$etM.'-'.$etD;
            $second1 = strtotime($stDate);
            $second2 = strtotime($etDate);
            if ($second1 < $second2) {
                $tmp = $second2;
                $second2 = $second1;
                $second1 = $tmp;
            }
            $dayNum = ($second1 - $second2) / 86400;
            $total_free = $dayNum*$good->price;

        }else{
            $instartTime = intval($startTime);
            $inendTime = intval($endTime);
            $ordernumber = DB::select('select count(*) as number from orders where (start_time >= ? AND (start_time+1) < ? AND end_time >= ? AND good_id = ? ) OR (start_time <= ? AND (end_time-1) > ? AND end_time <= ? AND good_id = ? ) OR (start_time >= ? AND end_time <= ? AND good_id = ?) OR (start_time <= ? AND end_time >= ? AND good_id = ?)', [$instartTime,$inendTime,$inendTime,$good_id,$instartTime,$instartTime,$inendTime,$good_id,$instartTime,$inendTime,$good_id,$instartTime,$inendTime,$good_id]);
            foreach ($ordernumber as $orderitem) {
                $number = $orderitem->number;
            }
            $syNumber = $good->count - $number;
            if($syNumber == 0){
                return $this->response->array([
                    'error'=>'该时段无多余车辆'
                ]);
            }
            $stHour = $startTime[8].$startTime[9];
            $etHour = $endTime[8].$endTime[9];
            $hourNum = $etHour - $stHour;
            $total_free = $hourNum*$good->price;
        }
        $payment_no = static::findAvailableNo();
        if(!$payment_no){
            return $this->response->array([
                'error'=>'订单生成错误'
            ]);
        }
        $paid_at = Carbon::now();
        $order = \DB::transaction(function () use ($user,$total_free,$good_id,$startTime,$endTime,$payment_no,$paid_at)  {
            $order = new Order([
                'user_id'=>$user,
                'good_id'=>$good_id,
                'order_status'=>1,
                'total_price'=>$total_free,
                'start_time'=>$startTime,
                'end_time'=>$endTime,
                'payment_no'=>$payment_no,
                'paid_at'=>$paid_at,
            ]);
            $order->save();
        });
        return $this->response->array([
            'payment_no'=>$payment_no,
            'name'=>$name,
            'phone'=>$phone,
            'price'=>$total_free,
        ]);


    }

}
