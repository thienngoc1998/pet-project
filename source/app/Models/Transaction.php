<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Transaction extends Model
{
    protected $guarded = [''];

    protected $status = [
        '1' => [
            'class' => 'default',
            'name'  => 'Tiếp nhận'
        ],
        '2' => [
            'class' => 'info',
            'name'  => 'Đang vận chuyển'
        ],
        '3' => [
            'class' => 'success',
            'name'  => 'Hoàn thành'
        ],
        '-1' => [
            'class' => 'danger',
            'name'  => 'Đã Huỷ'
        ],
    ];

    public const PENDING = 1;
    public const PROCESSING = 2;
    public const SUCCESS = 3;
    public const ERROR = 4;
    public const CANCELED = 5;

    public function getStatus()
    {
        return Arr::get($this->status, $this->tst_status,"[N\A]");
    }

    public function user()
    {
        return $this->belongsTo(User::class,'tst_user_id','id');
    }
}
