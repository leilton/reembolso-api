<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refound extends Model
{
    public $timestamps = false;
    protected $fillable = ['date', 'type', 'description', 'value', 'receipt'];

    public function getStatusAttribute($status): bool
    {
        return $status;
    }

    public function getBlockAttribute($block): bool
    {
        return $block;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    use softDeletes;

    protected $dates = ['deleted_at'];
    protected $dateFormat = 'c';

}
