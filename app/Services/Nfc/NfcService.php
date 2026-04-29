<?php

namespace App\Services\Nfc;

use App\Repositories\NFCRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class NfcService
{

    public function __construct(public NFCRepository $NFCRepository)
    {}

    public function getAllNfc()
    {
        return $this->NFCRepository->getAll();
    }

    public function verifyNfc($identifier)
    {
        return $this->NFCRepository->getAll($identifier);
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function findNfcDetailById($id): mixed
    {
        $nfcDetail =  $this->NFCRepository->findNFCDetailById($id);
        if(!$nfcDetail){
            throw new \Exception(__('message.nfc_not_found'),400);
        }
        return $nfcDetail;
    }

    /**
     * @throws \Exception
     */
    public function saveNfcDetail($validatedData)
    {
        try {

            DB::beginTransaction();
                $nfcDetail = $this->NFCRepository->store($validatedData);
            DB::commit();
            return $nfcDetail;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * @throws \Exception
     */
    public function deleteNfcDetail($id): bool
    {
        try {
            $nfcDetail = $this->findNfcDetailById($id);
            DB::beginTransaction();
                $this->NFCRepository->delete($nfcDetail);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }


}

