<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_training',
        'duration',
        'training_media_id',
        'user_id',
        'sheet_id'
    ];
}