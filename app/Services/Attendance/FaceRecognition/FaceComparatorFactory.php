<?php

namespace App\Services\Attendance\FaceRecognition;

use Aws\Rekognition\RekognitionClient;
use InvalidArgumentException;

class FaceComparatorFactory
{
    public static function make(?string $driver = null): FaceComparator
    {
        $driver = $driver ?? config('attendance.face_recognition.driver', 'null');

        return match ($driver) {
            'aws' => self::makeAws(),
            'null' => new NullFaceComparator(),
            default => throw new InvalidArgumentException("Unknown face recognition driver: {$driver}"),
        };
    }

    private static function makeAws(): AwsRekognitionFaceComparator
    {
        $cfg = config('attendance.face_recognition.aws', []);
        $timeout = (int) config('attendance.face_recognition.timeout', 8);

        $args = [
            'version' => 'latest',
            'region' => $cfg['region'] ?? 'us-east-1',
            'http' => [
                'timeout' => $timeout,
                'connect_timeout' => max(2, (int) ceil($timeout / 2)),
            ],
        ];

        if (!empty($cfg['key']) && !empty($cfg['secret'])) {
            $args['credentials'] = [
                'key' => $cfg['key'],
                'secret' => $cfg['secret'],
            ];
        }

        return new AwsRekognitionFaceComparator(new RekognitionClient($args));
    }
}
