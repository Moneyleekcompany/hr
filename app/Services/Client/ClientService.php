<?php

namespace App\Services\Client;

use App\Repositories\ClientRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ClientService
{
    private ClientRepository $clientRepo;

    public function __construct(ClientRepository $clientRepo)
    {
        $this->clientRepo = $clientRepo;
    }

    public function getAllClientsList($select = ['*'], $with = [])
    {
        return $this->clientRepo->getAllClients($select, $with);
    }

    public function getTopClientsOfCompany()
    {
        return $this->clientRepo->getTopClientsOfCompany();
    }

    public function getAllActiveClients($select = ['*'], $with = [])
    {
        return $this->clientRepo->getAllActiveClients($select, $with);
    }

    public function findClientDetailById($id,$select = ['*'],$with= [])
    {
        try{
            $clientDetail =  $this->clientRepo->findClientDetailById($id,$select,$with);
            if(!$clientDetail){
                throw new \Exception(__('message.client_not_found'),400);
            }
            return $clientDetail;
        }catch(Exception $exception){
            throw $exception;
        }
    }

    /**
     * @throws \Exception
     */
    public function saveClientDetail($validatedData)
    {
        try {
            DB::beginTransaction();
                $clientDetail = $this->clientRepo->store($validatedData);
            DB::commit();
            return $clientDetail;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function updateClientDetail($validatedData, $clientId): bool
    {
        try {
            $clientDetail = $this->findClientDetailById($clientId);
            DB::beginTransaction();
                $updateStatus = $this->clientRepo->update($clientDetail, $validatedData);
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
    public function toggleIsActiveStatus($id): bool
    {
        try {
            DB::beginTransaction();
                $clientDetail = $this->findClientDetailById($id);
                $this->clientRepo->toggleIsActiveStatus($clientDetail);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @throws \Exception
     */
    public function deleteClientDetail($id): bool
    {
        try {
            $clientDetail = $this->findClientDetailById($id);
            DB::beginTransaction();
                $this->clientRepo->delete($clientDetail);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }


}

