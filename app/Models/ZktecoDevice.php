<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZktecoDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'branch_id',
        'is_active',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
