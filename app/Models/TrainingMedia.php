<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
    ];

    public $timestamps = false;
    protected $table = 'training_medias';

}
