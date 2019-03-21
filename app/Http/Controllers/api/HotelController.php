<?php

namespace App\Http\Controllers\api;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\api\hotelRequest;
use Illuminate\Http\Request;
use App\Models\Good;
use App\Http\Controllers\Controller;

class HotelController extends Controller
{
    public function hotelnumber(hotelRequest $request){
        $instartTime = intval($request->startTime);
        $inendTime = intval($request->endTime);
        $type = 1;
        $total = Good::select('count')->where('id',$type)->first();
        $ordernumber = DB::select('select count(*) as number from orders where (start_time >= ? AND (start_time+1) < ? AND end_time >= ? AND good_id = ?) OR (start_time <= ? AND (end_time-1) > ? AND end_time <= ? AND good_id = ?) OR (start_time >= ? AND end_time <= ? AND good_id = ?) OR (start_time <= ? AND end_time >= ? AND good_id = ?)', [$instartTime,$inendTime,$inendTime,$type,$instartTime,$instartTime,$inendTime,$type,$instartTime,$inendTime,$type,$instartTime,$inendTime,$type]);
        foreach ($ordernumber as $orderitem) {
            $number = $orderitem->number;
        }
        // $forbid  禁止表单
        $syNumber = $total->count - $number;
        if ($syNumber <= 0){
            $syNumber = 0;
        }

        return $this->response->array(['number'=>$syNumber]);

    }
}
