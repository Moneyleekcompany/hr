<?php

namespace App\Services\Attendance;

use Illuminate\Support\Facades\Log;

/**
 * يستخرج إحداثيات GPS من EXIF metadata الخاص بصورة السيلفي.
 *
 * السياسة:
 *  - لو الصورة فيها GPS EXIF: نتحقق إنها قريبة من الموقع المُعلن.
 *  - لو الصورة مش فيها EXIF (شائع جدًا — كثير من الهواتف تحذفه): نسمح.
 *
 * الحد الأقصى للانحراف افتراضي 500م، يمكن ضبطه عبر config.
 */
class SelfieExifInspector
{
    private const MAX_DEVIATION_METERS_DEFAULT = 500;

    public function __construct(private LocationService $locationService) {}

    /**
     * يفحص صورة (raw bytes أو base64-decoded) ضد إحداثيات مُعلنة.
     *
     * @param  string  $imageBytes  raw bytes للصورة
     * @param  float|null  $claimedLat
     * @param  float|null  $claimedLng
     * @return array{has_exif_gps: bool, exif_lat: float|null, exif_lng: float|null, deviation_m: float|null, is_consistent: bool}
     */
    public function inspect(string $imageBytes, ?float $claimedLat, ?float $claimedLng): array
    {
        $default = [
            'has_exif_gps' => false,
            'exif_lat' => null,
            'exif_lng' => null,
            'deviation_m' => null,
            'is_consistent' => true,
        ];

        if (!function_exists('exif_read_data')) {
            return $default;
        }

        $coords = $this->extractGpsFromBytes($imageBytes);
        if ($coords === null) {
            return $default;
        }

        if ($claimedLat === null || $claimedLng === null) {
            return [
                'has_exif_gps' => true,
                'exif_lat' => $coords['lat'],
                'exif_lng' => $coords['lng'],
                'deviation_m' => null,
                'is_consistent' => true, // لا يوجد ما نقارن به
            ];
        }

        $deviation = $this->locationService->calculateDistance(
            $claimedLat,
            $claimedLng,
            $coords['lat'],
            $coords['lng']
        );

        $maxDeviation = (int) config('attendance.gps.exif_max_deviation_m', self::MAX_DEVIATION_METERS_DEFAULT);

        return [
            'has_exif_gps' => true,
            'exif_lat' => $coords['lat'],
            'exif_lng' => $coords['lng'],
            'deviation_m' => $deviation,
            'is_consistent' => $deviation <= $maxDeviation,
        ];
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private function extractGpsFromBytes(string $imageBytes): ?array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'selfie_exif_');
        if ($tmp === false) {
            return null;
        }

        try {
            file_put_contents($tmp, $imageBytes);
            $exif = @exif_read_data($tmp, 'GPS', true);
        } catch (\Throwable $e) {
            Log::debug('SelfieExifInspector: exif_read_data failed', ['error' => $e->getMessage()]);
            return null;
        } finally {
            @unlink($tmp);
        }

        if (!$exif || empty($exif['GPS'])) {
            return null;
        }

        $gps = $exif['GPS'];
        $lat = $this->parseExifCoord($gps['GPSLatitude'] ?? null, $gps['GPSLatitudeRef'] ?? 'N');
        $lng = $this->parseExifCoord($gps['GPSLongitude'] ?? null, $gps['GPSLongitudeRef'] ?? 'E');

        if ($lat === null || $lng === null) return null;

        return ['lat' => $lat, 'lng' => $lng];
    }

    /**
     * يحوّل DMS من EXIF (مصفوفة 3 كسور "deg/1, min/1, sec/100") إلى عشري.
     */
    private function parseExifCoord($value, ?string $ref): ?float
    {
        if (!is_array($value) || count($value) !== 3) {
            return null;
        }

        $deg = $this->fractionToFloat($value[0]);
        $min = $this->fractionToFloat($value[1]);
        $sec = $this->fractionToFloat($value[2]);

        if ($deg === null || $min === null || $sec === null) {
            return null;
        }

        $decimal = $deg + ($min / 60.0) + ($sec / 3600.0);
        if (in_array(strtoupper((string) $ref), ['S', 'W'], true)) {
            $decimal = -$decimal;
        }
        return $decimal;
    }

    private function fractionToFloat($v): ?float
    {
        if (is_numeric($v)) return (float) $v;
        if (!is_string($v) || !str_contains($v, '/')) return null;
        [$num, $den] = explode('/', $v, 2);
        if (!is_numeric($num) || !is_numeric($den) || (float) $den === 0.0) return null;
        return (float) $num / (float) $den;
    }
}
