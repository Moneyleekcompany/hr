<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrAttendance extends Model
{
    use HasFactory;

    protected $table = 'qr_attendances';

    protected $fillable = ['title','identifier'];

    protected $appends = ['qr_code'];


    public function getQrCodeAttribute()
    {
        return QrCode::size(480)->generate($this->identifier);
    }

}
