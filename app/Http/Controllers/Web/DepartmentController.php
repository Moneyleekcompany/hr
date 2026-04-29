<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Repositories\BranchRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;
use App\Requests\Department\DepartmentStoreRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    private $view = 'admin.department.';

    private DepartmentRepository $departmentRepo;
    private CompanyRepository $companyRepo;
    private UserRepository $userRepo;
    private BranchRepository $branchRepo;

    public function __construct(DepartmentRepository $departmentRepo,
                                CompanyRepository    $companyRepo,
                                UserRepository $userRepo,
                                BranchRepository $branchRepo)
    {
        $this->departmentRepo = $departmentRepo;
        $this->companyRepo = $companyRepo;
        $this->userRepo = $userRepo;
        $this->branchRepo = $branchRepo;
    }

    public function index(Request $request)
    {
        $this->authorize('list_department');
        try {
            $filterParameters = [
                'company_id' => AppHelper::getAuthUserCompanyId(),
                'name' =>  $request->name ?? null,
                'branch' =>  $request->branch ?? null,
                'per_page' => $request->per_page ?? Department::RECORDS_PER_PAGE,
            ];
            $selectBranch = ['id','name'];
            $with = ['branch:id,name', 'departmentHead:id,name'];
            $branch = $this->branchRepo->getLoggedInUserCompanyBranches($filterParameters['company_id'],$selectBranch);
            $departments = $this->departmentRepo->getAllPaginatedDepartments($filterParameters,$with);
            return view($this->view . 'index', compact('departments','filterParameters','branch'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('create_department');
        try {
            $select = ['name','id'];
            $users = $this->userRepo->getAllUsers($select);
            $with = ['branches:company_id,id,name'];
            $select = ['id', 'name'];
            $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
            return view($this->view . 'create',
                compact('companyDetail', 'users')
            );
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function getAllDepartmentsByBranchId($branchId): JsonResponse|RedirectResponse
    {
        $this->authorize('create_department');
        try {

            $with = [];
            $select = ['dept_name', 'id'];
            $departments = $this->departmentRepo->getAllActiveDepartmentsByBranchId($branchId,$with,$select);
            return response()->json([
                'data' => $departments
            ]);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(),$exception->getCode());
        }
    }

    public function store(DepartmentStoreRequest $request)
    {
        $this->authorize('create_department');
        try {
            $validatedData = $request->validated();
            DB::beginTransaction();
            $this->departmentRepo->store($validatedData);
            DB::commit();
            return redirect()
                ->route('admin.departments.index')
                ->with('success', __('message.add_department'));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.departments.index')
                ->with('danger', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $this->authorize('edit_department');
        try {
            $departmentsDetail = $this->departmentRepo->findDepartmentById($id);

            $users = User::select('name', 'id');
            $users = $users->get();
            $with = ['branches:company_id,id,name'];
            $select = ['id', 'name'];
            $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
            return view($this->view . 'edit',
                compact('companyDetail', 'users', 'departmentsDetail')
            );
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function update(DepartmentStoreRequest $request, $id)
    {
        $this->authorize('edit_department');
        try {
            $validatedData = $request->validated();
            $departmentDetail = $this->departmentRepo->findDepartmentById($id);
            if (!$departmentDetail) {
                throw new Exception(__('message.update_department'), 404);
            }
            DB::beginTransaction();
            $department = $this->departmentRepo->update($departmentDetail, $validatedData);
            DB::commit();
            return redirect()
                ->route('admin.departments.index')
                ->with('success', __('message.'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage())->withInput();
        }

    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_department');
        try {
            DB::beginTransaction();
            $this->departmentRepo->toggleStatus($id);
            DB::commit();
            return redirect()->back()->with('success', __('message.status_changed'));
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function delete($id)
    {
        $this->authorize('delete_department');
        try {
            $select = ['*'];
            $with = ['posts'];
            $departmentDetail = $this->departmentRepo->findDepartmentById($id,$select,$with);
            if (!$departmentDetail) {
                throw new Exception(__('message.department_not_found'), 404);
            }
            if(count($departmentDetail->posts) > 0){
                throw new Exception(__('message.delete_department_warning'),403);
            }

            DB::beginTransaction();
            $this->departmentRepo->delete($departmentDetail);
            DB::commit();
            return redirect()->back()->with('success', __('message.delete_department'));
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

}
