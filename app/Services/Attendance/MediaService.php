<?php

namespace App\Services\Attendance;

use App\Models\User;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Storage;
use Exception;

class MediaService
{
    /**
     * معالجة صورة الـ Base64، حفظها عبر Storage، والتحقق منها بالذكاء الاصطناعي
     */
    public function processAndVerifyImage(string $base64Image, User $user, string $type): string
    {
        $imageName = $type . '_' . $user->id . '_' . time() . '.png';
        $imagePath = 'uploads/attendance/' . $imageName;
        $imageParts = explode(";base64,", $base64Image);

        if (count($imageParts) !== 2) {
            throw new Exception("تنسيق الصورة غير صالح.", 400);
        }

        // تحديد الديسك الافتراضي (سيكون s3 إذا قمت بتغييره في الـ .env)
        $disk = config('filesystems.default');
        Storage::disk($disk)->put($imagePath, base64_decode($imageParts[1]));

        // التحقق من الوجه بالذكاء الاصطناعي (AI Facial Recognition)
        if (!FacialRecognitionService::verifyFace($user, Storage::disk($disk)->url($imagePath))) {
            Storage::disk($disk)->delete($imagePath); // حذف الصورة المزيفة
            SecurityLog::create([
                'user_id' => $user->id,
                'type' => 'تزييف الوجه (Fake Face)',
                'message' => 'فشل التحقق من الوجه بالذكاء الاصطناعي أثناء ' . ($type == 'checkin' ? 'تسجيل الحضور.' : 'تسجيل الانصراف.'),
                'ip_address' => request()->ip()
            ]);
            throw new Exception("فشل التحقق من الوجه بالذكاء الاصطناعي. الصورة لا تتطابق مع صورتك الشخصية المسجلة.", 403);
        }

        return $imageName;
    }
}