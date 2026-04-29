<?php

namespace App\Http\Controllers\Web;

use App\Enum\TrainerTypeEnum;
use App\Helpers\AppHelper;
use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Models\EmployeeTraining;
use App\Repositories\BranchRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;
use App\Requests\TrainingManagement\TrainingRequest;
use App\Services\TrainingManagement\TrainerService;
use App\Services\TrainingManagement\TrainingService;
use App\Services\TrainingManagement\TrainingTypeService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    private string $view = 'admin.trainingManagement.training.';

    public function __construct(
        protected TrainingService $trainingService, protected TrainingTypeService $trainingTypeService,
        protected UserRepository $userRepository, protected TrainerService $trainerService, protected BranchRepository $branchRepository, protected DepartmentRepository $departmentRepository
    ){}

    /**
     * Display a listing of the resource.
     *
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('list_training');
        try{
            $this->updateTrainingStatus();
            $select = ['*'];
            $with = ['trainingType:id,title','employeeTraining.employee:id,name'];
            $trainingLists = $this->trainingService->getAllTrainingPaginated($select,$with);
            $trainerTypes = TrainerTypeEnum::cases();
            $departmentIds = [];
            $employeeIds = [];
            return view($this->view.'index', compact('trainingLists','trainerTypes','departmentIds','employeeIds'));
        }catch(Exception $exception){
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        $this->authorize('create_training');

        try{
            $departmentIds = [];
            $employeeIds = [];
            $companyId = AppHelper::getAuthUserCompanyId();

            $isBsEnabled = AppHelper::ifDateInBsEnabled();
            $selectBranch = ['id','name'];
            $trainerTypes = TrainerTypeEnum::cases();
            $branch = $this->branchRepository->getLoggedInUserCompanyBranches($companyId,$selectBranch);
            $trainingTypes = $this->trainingTypeService->getAllActiveTrainingTypes(['id','title']);

            return view($this->view.'create', compact('trainingTypes','trainerTypes','branch','isBsEnabled','employeeIds','departmentIds'));
        }catch(Exception $exception){
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    /**
     * @param TrainingRequest $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(TrainingRequest $request)
    {
        $this->authorize('create_training');

        try{
            $validatedData = $request->validated();

            DB::beginTransaction();
            $trainingDetail = $this->trainingService->saveTrainingDetail($validatedData);
            DB::commit();

            if($trainingDetail && $validatedData['notification'] == 1){
                $type = $this->trainingTypeService->findTrainingTypeById($trainingDetail['training_type_id']);
                $message = 'Your are invited to participate in '. ucfirst($type->title);

                if(isset($trainingDetail['end_date'])){
                    $message .=' starting from '.\App\Helpers\AppHelper::formatDateForView($trainingDetail['start_date']). ' to '. \App\Helpers\AppHelper::formatDateForView($trainingDetail['end_date']);
                }else{
                    $message .=' on '.\App\Helpers\AppHelper::formatDateForView($trainingDetail['start_date']);
                }

                $this->sendNoticeNotification('Training Notification', $message, $validatedData['employee_id']);
            }
            return redirect()->route('admin.training.index')->with('success',__('message.add_training') );
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id

     */
    public function show($id)
    {
        $this->authorize('show_training');

        try{
            $select = ['*'];
            $with = ['trainingType:id,title','branch:id,name','trainingInstructor.trainer.employee:id,name','trainingDepartment.department:id,dept_name','createdBy:id,name', 'updatedBy:id,name','employeeTraining.employee:id,name'];
            $trainingDetail = $this->trainingService->findTrainingById($id,$select,$with);
            $trainerTypes = TrainerTypeEnum::cases();
            $departmentIds = [];
            $employeeIds = [];
            return view($this->view.'show', compact('trainingDetail','trainerTypes','departmentIds','employeeIds'));
        }catch(Exception $exception){
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        $this->authorize('update_training');
        try{
            $with = ['employeeTraining','trainingDepartment','trainingInstructor.trainer.employee:id,name'];
            $trainingDetail = $this->trainingService->findTrainingById($id,['*'],$with);
            $companyId = AppHelper::getAuthUserCompanyId();

            $isBsEnabled = AppHelper::ifDateInBsEnabled();
            $selectBranch = ['id','name'];
            $trainerTypes = TrainerTypeEnum::cases();

            $branch = $this->branchRepository->getLoggedInUserCompanyBranches($companyId,$selectBranch);

            $selectUser = ['id', 'name'];
            $users = $this->userRepository->getAllVerifiedEmployeeOfCompany($selectUser);
            $trainingTypes = $this->trainingTypeService->getAllActiveTrainingTypes(['id','title']);
            $employeeIds = [];
            foreach ($trainingDetail->employeeTraining as $key => $value) {
                $employeeIds[] = $value->employee_id;
            }

            $departmentIds = [];
            foreach ($trainingDetail->trainingDepartment as $key => $value) {
                $departmentIds[] = $value->department_id;
            }
            // Fetch users by selected departments
            $filteredDepartment = isset($trainingDetail->branch_id)
                ? $this->departmentRepository->getAllActiveDepartmentsByBranchId($trainingDetail->branch_id,[], ['id','dept_name'])
                : [];

            $select = ['name', 'id'];
            // Fetch users by selected departments
            $filteredUsers = !empty($departmentIds)
                ? $this->userRepository->getActiveEmployeesByDepartment($departmentIds, $select)
                : $users;

            return view($this->view.'edit', compact('trainingDetail','isBsEnabled','trainerTypes','branch','trainingTypes','employeeIds','filteredUsers','departmentIds','filteredDepartment'));
        }catch(Exception $exception){
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TrainingRequest $request
     * @param int $id
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(TrainingRequest $request, $id): RedirectResponse
    {
        $this->authorize('update_training');
        try{
            $validatedData = $request->validated();

            $previousEmployee = EmployeeTraining::where('training_id',$id)->get('employee_id')->toArray();

            DB::beginTransaction();
            $trainingDetail = $this->trainingService->updateTrainingDetail($id,$validatedData);
            DB::commit();

            $previousEmployeeIds = array_column($previousEmployee, 'employee_id');
            $removedIds = array_diff($previousEmployeeIds, $validatedData['employee_id']);
            $addedEmployeeIds = array_diff($validatedData['employee_id'], $previousEmployeeIds);

            $remainingEmployeeIds = array_intersect($previousEmployeeIds, $validatedData['employee_id']);

            if($trainingDetail && $validatedData['notification'] == 1){
                $sendNotification = false;
                $today = date('Y-m-d H:i');
                $start = $trainingDetail['start_date'].' '. $trainingDetail['end_time'] ;
                if(isset($trainingDetail['end_date'])){
                    $end = $trainingDetail['end_date'] .' '. $trainingDetail['end_time'];

                    if(strtotime($today) <= strtotime($end)){

                        $sendNotification = true;
                    }
                }else{
                    if(strtotime($today) <= strtotime($start)){
                        $sendNotification = true;
                    }
                }

                if($sendNotification) {

                    $type = $this->trainingTypeService->findTrainingTypeById($trainingDetail['training_type_id']);
                    // add notification
                    $message = 'Your are invited to participate in ' . ucfirst($type->title);
                    if (isset($trainingDetail['end_date'])) {
                        $message .= ' starting from ' . \App\Helpers\AppHelper::formatDateForView($trainingDetail['start_date']) . ' to ' . \App\Helpers\AppHelper::formatDateForView($trainingDetail['end_date']);
                    } else {
                        $message .= ' on ' . \App\Helpers\AppHelper::formatDateForView($trainingDetail['start_date']);
                    }

                    $this->sendNoticeNotification('Training Notification', $message, $addedEmployeeIds);


                    //remove notification
                    $removeMassage = 'Sorry, we have cancelled your invitation in ' . ucfirst($type->title);
                    if (isset($trainingDetail['end_date'])) {
                        $removeMassage .= ' starting from ' . \App\Helpers\AppHelper::formatDateForView($trainingDetail['start_date']) . ' to ' . \App\Helpers\AppHelper::formatDateForView($trainingDetail['end_date']);
                    } else {
                        $removeMassage .= ' on ' . \App\Helpers\AppHelper::formatDateForView($trainingDetail['start_date']);
                    }
                    $this->sendNoticeNotification('Training Notification', $removeMassage, $removedIds);


                    // change notification
                    $changeMassage = 'The training "' . ucfirst($type->title) . '" that you are participating in has been updated';

                    $this->sendNoticeNotification('Training Notification', $changeMassage, $remainingEmployeeIds);
                }
            }
            return redirect()->route('admin.training.index')
                ->with('success', __('message.update_training'));
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage())
                ->withInput();
        }
    }

    public function delete($id)
    {
        $this->authorize('delete_training');
        try{
            DB::beginTransaction();
            $this->trainingService->deleteTraining($id);
            DB::commit();
            return redirect()->back()->with('success', __('message.delete_training'));
        }catch(Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('danger',$exception->getMessage());
        }
    }

    public function updateTrainingStatus()
    {

        $this->trainingService->updateStatus();
    }

    private function sendNoticeNotification($title, $description, $userIds)
    {
        SMPushHelper::sendTrainingNotification($title, $description, $userIds);
    }
}
