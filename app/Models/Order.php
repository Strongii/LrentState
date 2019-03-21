<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=[
      'user_id','good_id','order_status','total_price','start_time','end_time','payment_no','paid_at'
    ];

    public function good(){
        return $this->belongsTo(Good::class);
    }
}
