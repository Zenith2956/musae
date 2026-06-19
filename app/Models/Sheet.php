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
        'composer',
        'user_id',
        'bpm',
        'gamme',
        'proficiency_level',
        'style'

    ];
    public $timestamps = false;
    
    public function instrument()
    {
        return $this->belongsTo(GenericInstrument::class);
    }
}
