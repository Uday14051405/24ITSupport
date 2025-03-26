<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_category_id', 'question', 'input_type', 'options', 'is_required', 'status'
    ];

    protected $casts = [
        'options' => 'array', // Cast options as an array
    ];

    public function category()
    {
        return $this->belongsTo(QuestionCategory::class, 'question_category_id');
    }
}
