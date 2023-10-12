<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function from_user()
    {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }

    public function to_user()
    {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }
}
