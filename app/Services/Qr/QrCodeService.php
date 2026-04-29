<?php

namespace App\Services\Qr;

use App\Repositories\QRCodeRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class QrCodeService
{

    public function __construct(public QRCodeRepository $QRCodeRepository)
    {}

    public function getAllQr()
    {
        return $this->QRCodeRepository->getAll();
    }

    public function verifyQr($identifier)
    {
        return $this->QRCodeRepository->getAll($identifier);
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function findQrDetailById($id): mixed
    {
        return $this->QRCodeRepository->findQr($id);
    }

    /**
     * @throws \Exception
     */
    public function saveQrDetail($validatedData)
    {
        try {

            // إنشاء رابط كامل لصفحة الحضور لكي يحول الموظف إليها عند مسح الكود
            $token = bin2hex(random_bytes(15));
            $validatedData['identifier'] = route('admin.dashboard.takeAttendance', ['type' => 'checkin']) . '?qr_token=' . $token;

            DB::beginTransaction();
                $qrDetail = $this->QRCodeRepository->store($validatedData);
            DB::commit();
            return $qrDetail;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function updateQrDetail($validatedData, $id): bool
    {
        try {
            $qrDetail = $this->findQrDetailById($id);
            DB::beginTransaction();
                $updateStatus = $this->QRCodeRepository->update($qrDetail, $validatedData);
            DB::commit();
            return $updateStatus;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * @throws \Exception
     */
    public function deleteQrDetail($id): bool
    {
        try {
            $qrDetail = $this->findQrDetailById($id);
            DB::beginTransaction();
                $this->QRCodeRepository->delete($qrDetail);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }


}
