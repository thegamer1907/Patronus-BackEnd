<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class block_status extends Model
{
    protected $fillable = [
        'account_no', 'status'
    ];
}
