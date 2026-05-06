<?php

namespace App\Services\Attendance\FaceRecognition;

interface FaceComparator
{
    /**
     * يرجع نسبة التشابه (0..100) بين الصورتين.
     * يرمي FaceComparisonException عند الفشل التقني (network/خدمة) دون أن يكون رفضًا أمنيًا.
     */
    public function compare(string $referenceImageBytes, string $candidateImageBytes): float;
}
