<?php

namespace App\Services\Attendance;

use App\Models\User;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FacialRecognitionService
{
    /**
     * مطابقة الصورة المرفوعة مع الصورة الشخصية للموظف بالذكاء الاصطناعي
     *
     * @param User $user
     * @param string $uploadedImagePath
     * @return bool
     */
    public static function verifyFace(User $user, string $uploadedImagePath): bool
    {
        // التحقق مما إذا كانت الخاصية مفعلة مع حفظ النتيجة في الكاش لتخفيف الضغط على قاعدة البيانات
        $isFacialRecognitionEnabled = Cache::rememberForever('facial_recognition_status', function () {
            return AppSetting::where('slug', 'facial-recognition')->value('status');
        });

        if (!$isFacialRecognitionEnabled) {
            return true; // تمرير العملية بنجاح وتجاهل مطابقة الوجه إذا كانت الخاصية معطلة
        }

        // إذا لم يكن للموظف صورة شخصية مسجلة، نمرر العملية (أو يمكنك إجبارهم على رفع صورة أولاً)
        if (!$user->avatar) {
            return true; 
        }

        $profileImagePath = public_path(User::AVATAR_UPLOAD_PATH . $user->avatar);

        if (!file_exists($profileImagePath) || !file_exists($uploadedImagePath)) {
            return false;
        }

        try {
            // هنا يمكنك دمج AWS Rekognition SDK أو إرسال الصورتين إلى خدمة Python (FastAPI/Flask)
            // $rekognition = new \Aws\Rekognition\RekognitionClient([...]);
            // $result = $rekognition->compareFaces([...]);
            // return $result['FaceMatches'][0]['Similarity'] > 90.0;

            return true; // حالياً يتم إرجاع True حتى تقوم بدمج مكتبة الـ AI فعلياً
        } catch (\Exception $e) {
            Log::error('AI Facial Recognition Error: ' . $e->getMessage());
            return false;
        }
    }
}