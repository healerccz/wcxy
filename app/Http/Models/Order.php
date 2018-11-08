<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'time', 'dormitory', 'mobile',
        'note', 'status', 'user_id', 'room'
    ];

    public $timestamps = true;
}
