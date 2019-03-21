<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\Api\carRequest;
use App\Models\Good;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CarController extends Controller
{
    public function carnumber(carRequest $request){
        $type = $request->type;
        $carType = $request->carType;
        $instartTime = intval($request->startTime);
        $inendTime = intval($request->endTime);
        $total = Good::select('count')->where('id',$type)->first();
        $ordernumber = DB::select('select count(*) as number from orders where (start_time >= ? AND (start_time+1) < ? AND end_time >= ? AND good_id = ? ) OR (start_time <= ? AND (end_time-1) > ? AND end_time <= ? AND good_id = ? ) OR (start_time >= ? AND end_time <= ? AND good_id = ?) OR (start_time <= ? AND end_time >= ? AND good_id = ?)', [$instartTime,$inendTime,$inendTime,$type,$instartTime,$instartTime,$inendTime,$type,$instartTime,$inendTime,$type,$instartTime,$inendTime,$type]);
        foreach ($ordernumber as $orderitem) {
            $number = $orderitem->number;
        }
        $syNumber = $total->count - $number;
        return $this->response->array(['number'=>$syNumber]);
    }
}
