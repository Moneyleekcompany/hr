<?php

namespace App\Services\Attendance\FaceRecognition;

use Aws\Rekognition\RekognitionClient;
use Throwable;

class AwsRekognitionFaceComparator implements FaceComparator
{
    public function __construct(private RekognitionClient $client) {}

    public function compare(string $referenceImageBytes, string $candidateImageBytes): float
    {
        try {
            $result = $this->client->compareFaces([
                'SimilarityThreshold' => 0.0, // نسترجع كل المطابقات ونحكم نحن بناءً على الحد المعدّ
                'SourceImage' => ['Bytes' => $referenceImageBytes],
                'TargetImage' => ['Bytes' => $candidateImageBytes],
            ]);
        } catch (Throwable $e) {
            throw new FaceComparisonException(
                'AWS Rekognition compareFaces failed: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $matches = $result->get('FaceMatches') ?? [];
        if (empty($matches)) {
            return 0.0;
        }

        $best = 0.0;
        foreach ($matches as $match) {
            $sim = (float) ($match['Similarity'] ?? 0.0);
            if ($sim > $best) {
                $best = $sim;
            }
        }

        return $best;
    }
}
