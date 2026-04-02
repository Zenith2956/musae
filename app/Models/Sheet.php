<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sheet extends Model
{
    protected $table = 'sheets';
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'instrument_id',
        'users_id'

    ];
    public $timestamps = false;

}
