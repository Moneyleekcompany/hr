<?php

namespace App\Services\Attendance\FaceRecognition;

class NullFaceComparator implements FaceComparator
{
    public function compare(string $referenceImageBytes, string $candidateImageBytes): float
    {
        return 100.0;
    }
}
