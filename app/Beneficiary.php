<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Beneficiary extends Model
{
    protected $fillable = [
        'email', 'ben_email','ben_account_no','name'
    ];

    public $incrementing = false;

    protected function setKeysForSaveQuery(Builder $query)
    {
        $query
            ->where('email', '=', $this->getAttribute('email'))
            ->where('ben_email', '=', $this->getAttribute('ben_email'));
        return $query;
    }
}
