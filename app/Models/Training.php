<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'instrument_id',
        'link',
        'date_training',
        'end_training',
        'duration',
        'training_media_id',
        'user_id',
        'sheet_id'
    ];
    protected $casts = [
        'date_training' => 'datetime',
        'end_training' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sheet()
    {
        return $this->belongsTo(Sheet::class);
    }
    public function instrument()
    {
        return $this->belongsTo(GenericInstrument::class, 'instrument_id');
    }
}












