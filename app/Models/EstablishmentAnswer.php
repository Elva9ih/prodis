<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstablishmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_id',
        'question_code',
        'answer_code',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_code', 'code');
    }
}
