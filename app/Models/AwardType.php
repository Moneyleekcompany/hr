<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AwardType extends Model
{
    use HasFactory;

    protected $table = 'award_types';

    protected $fillable = [
        'title','status',
    ];

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class, 'award_type_id', 'id');
    }
}
