<?php

namespace App\Services\Holiday;

use App\Helpers\AppHelper;
use App\Repositories\HolidayRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class HolidayService
{
    private HolidayRepository $holidayRepo;

    public function __construct(HolidayRepository $holidayRepo)
    {
        $this->holidayRepo = $holidayRepo;
    }

    public function getAllHolidayLists($filterParameters, $select = ['*'], $with = [])
    {
        try {
            if (AppHelper::ifDateInBsEnabled()) {
                $nepaliDate = AppHelper::getCurrentNepaliYearMonth();
                $filterParameters['event_year'] = $filterParameters['event_year'] ?? $nepaliDate['year'];
                $dateInAD = AppHelper::findAdDatesFromNepaliMonthAndYear($filterParameters['event_year'], $filterParameters['month']);
                $filterParameters['start_date'] = $dateInAD['start_date'];
                $filterParameters['end_date'] = $dateInAD['end_date'];
            }
            return $this->holidayRepo->getAllHolidays($filterParameters, $select, $with);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getAllActiveHolidays()
    {
        try {
            $date = AppHelper::yearDetailToFilterData();
            if (isset($date['end_date'])) {
                $date['end_date'] = AppHelper::getBsNxtYearEndDateInAd();
            }
            return $this->holidayRepo->getAllActiveHolidays($date);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function findHolidayDetailById($id)
    {
        try {
            $holidayDetail = $this->holidayRepo->findHolidayDetailById($id);
            if (!$holidayDetail) {
                throw new Exception(__('message.holiday_not_found'), 404);
            }
            return $holidayDetail;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($validatedData)
    {
        try {
            $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();
            DB::beginTransaction();
            $holiday = $this->holidayRepo->store($validatedData);
            DB::commit();
            return $holiday;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($validatedData, $id)
    {
        try {
            $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();
            $holidayDetail = $this->findHolidayDetailById($id);
            DB::beginTransaction();
            $update = $this->holidayRepo->update($holidayDetail, $validatedData);
            DB::commit();
            return $update;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleHolidayStatus($id)
    {
        try {
            DB::beginTransaction();
            $holidayDetail = $this->findHolidayDetailById($id);
            $status = $this->holidayRepo->toggleStatus($holidayDetail);
            DB::commit();
            return $status;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $holidayDetail = $this->findHolidayDetailById($id);
            DB::beginTransaction();
            $delete = $this->holidayRepo->delete($holidayDetail);
            DB::commit();
            return $delete;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function getAllActiveHolidaysFromNowToGivenNumberOfDays($numberOfDays)
    {
        try{
            $nowDate = Carbon::now()->format('Y-m-d');
            $toDate = Carbon::now()->addDay($numberOfDays)->format('Y-m-d');
            return $this->holidayRepo->getAllActiveHolidaysBetweenGivenDates($nowDate,$toDate);
        }catch (Exception $exception){
            throw $exception;
        }
    }

    public function getCurrentActiveHoliday()
    {
        try{
            return $this->holidayRepo->getRecentActiveHoliday();
        }catch (Exception $exception){
            throw $exception;
        }
    }

    public function getHolidayByDate($date, $select=['*'])
    {
        try{
            return $this->holidayRepo->getHolidayByDate($date, $select);
        }catch (Exception $exception){
            throw $exception;
        }
    }

}
