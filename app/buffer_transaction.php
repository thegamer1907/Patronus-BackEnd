<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class buffer_transaction extends Model
{
    protected $fillable = [
        'from_account_no','to_account_no','amount'
    ];
}
