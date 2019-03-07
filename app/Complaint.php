<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Complaint extends Model
{
    protected $fillable = [
        'email', 'type','message','resolved'
    ];

    public $incrementing = false;

    protected function setKeysForSaveQuery(Builder $query)
    {
        $query
            ->where('email', '=', $this->getAttribute('email'))
            ->where('type', '=', $this->getAttribute('type'))
            ->where('message', '=', $this->getAttribute('message'));
        return $query;
    }
}
