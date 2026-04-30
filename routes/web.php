<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Web\AppSettingController;
use App\Http\Controllers\Web\AssetController;
use App\Http\Controllers\Web\AssetTypeController;
use App\Http\Controllers\Web\AttachmentController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\Web\AwardController;
use App\Http\Controllers\Web\AwardTypeController;
use App\Http\Controllers\Web\BonusController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\CompanyController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DataExportController;
use App\Http\Controllers\Web\DepartmentController;
use App\Http\Controllers\Web\EmployeeLogOutRequestController;
use App\Http\Controllers\Web\EmployeeSalaryController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\FeatureController;
use App\Http\Controllers\Web\FiscalYearController;
use App\Http\Controllers\Web\GeneralSettingController;
use App\Http\Controllers\Web\HolidayController;
use App\Http\Controllers\Web\LeaveApprovalController;
use App\Http\Controllers\Web\LeaveController;
use App\Http\Controllers\Web\LeaveTypeController;
use App\Http\Controllers\Web\NFCController;
use App\Http\Controllers\Web\NoticeController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\OfficeTimeController;
use App\Http\Controllers\Web\OverTimeSettingController;
use App\Http\Controllers\Web\PaymentCurrencyController;
use App\Http\Controllers\Web\PaymentMethodController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\PrivacyPolicyController;
use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\QrCodeController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\RouterController;
use App\Http\Controllers\Web\AdvanceSalaryController;
use App\Http\Controllers\Web\SalaryComponentController;
use App\Http\Controllers\Web\SecurityLogController;
use App\Http\Controllers\Web\SalaryGroupController;
use App\Http\Controllers\Web\SalaryHistoryController;
use App\Http\Controllers\Web\SalaryTDSController;
use App\Http\Controllers\Web\SSFController;
use App\Http\Controllers\Web\StaticPageContentController;
use App\Http\Controllers\Web\SupportController;
use App\Http\Controllers\Web\TadaAttachmentController;
use App\Http\Controllers\Web\TadaController;
use App\Http\Controllers\Web\TaskChecklistController;
use App\Http\Controllers\Web\TaskCommentController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\TaxReportController;
use App\Http\Controllers\Web\TeamMeetingController;
use App\Http\Controllers\Web\ThemeController;
use App\Http\Controllers\Web\TimeLeaveController;
use App\Http\Controllers\Web\TrainerController;
use App\Http\Controllers\Web\TrainingController;
use App\Http\Controllers\Web\TrainingTypeController;
use App\Http\Controllers\Web\UnderTimeSettingController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ZKTecoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes([
    'register' => false,
    'login' => false,
    'logout' => false
]);

Route::get('/', function () {
    return redirect()->route('admin.login');
});

/** app privacy policy route */
Route::get('privacy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['web']
], function () {
    Route::get('login', [AdminAuthController::class, 'showAdminLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.process');

    Route::group(['middleware' => ['admin.auth','permission']], function () {

        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /** User route */
        Route::resource('users', UserController::class);
        Route::get('users/toggle-status/{id}', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('users/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
        Route::get('users/change-workspace/{id}', [UserController::class, 'changeWorkSpace'])->name('users.change-workspace');
        Route::get('users/get-company-employee/{branchId}', [UserController::class, 'getAllCompanyEmployeeDetail'])->name('users.getAllCompanyUsers');
        Route::post('users/change-password/{userId}', [UserController::class, 'changePassword'])->name('users.change-password');
        Route::get('users/force-logout/{userId}', [UserController::class, 'forceLogOutEmployee'])->name('users.force-logout');
        Route::get('users/get-all-employees/{employeeId}', [UserController::class, 'getAllEmployeeByDepartmentId'])->name('users.getAllUsersByDepartmentId');
        Route::post('users/fetch-employees-by-department', [UserController::class, 'fetchEmployeesByDepartment'])->name('users.fetchByDepartment');

        /** company route */
        Route::resource('company', CompanyController::class);

        /** branch route */
        Route::resource('branch', BranchController::class);
        Route::get('branch/toggle-status/{id}', [BranchController::class, 'toggleStatus'])->name('branch.toggle-status');
        Route::get('branch/delete/{id}', [BranchController::class, 'delete'])->name('branch.delete');


        /** Department route */
        Route::resource('departments', DepartmentController::class);
        Route::get('departments/toggle-status/{id}', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
        Route::get('departments/delete/{id}', [DepartmentController::class, 'delete'])->name('departments.delete');
        Route::get('departments/get-All-Departments/{branchId}', [DepartmentController::class, 'getAllDepartmentsByBranchId'])->name('departments.getAllDepartmentsByBranchId');


        /** post route */
        Route::resource('posts', PostController::class);
        Route::get('posts/toggle-status/{id}', [PostController::class, 'toggleStatus'])->name('posts.toggle-status');
        Route::get('posts/delete/{id}', [PostController::class, 'delete'])->name('posts.delete');
        Route::get('posts/get-All-posts/{deptId}', [PostController::class, 'getAllPostsByBranchId'])->name('posts.getAllPostsByBranchId');

        /** roles & permissions route */
        Route::resource('roles', RoleController::class);
        Route::get('roles/toggle-status/{id}', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        Route::get('roles/delete/{id}', [RoleController::class, 'delete'])->name('roles.delete');
        Route::get('roles/permissions/{roleId}', [RoleController::class, 'createPermission'])->name('roles.permission');
        Route::put('roles/assign-permissions/{roleId}', [RoleController::class, 'assignPermissionToRole'])->name('role.assign-permissions');

        /** office_time route */
        Route::resource('office-times', OfficeTimeController::class);
        Route::get('office-times/toggle-status/{id}', [OfficeTimeController::class, 'toggleStatus'])->name('office-times.toggle-status');
        Route::get('office-times/delete/{id}', [OfficeTimeController::class, 'delete'])->name('office-times.delete');

        /** branch_router route */
        Route::resource('routers', RouterController::class);
        Route::get('routers/toggle-status/{id}', [RouterController::class, 'toggleStatus'])->name('routers.toggle-status');
        Route::get('routers/delete/{id}', [RouterController::class, 'delete'])->name('routers.delete');

        /** holiday route */
        Route::get('holidays/import-csv', [HolidayController::class, 'holidayImport'])->name('holidays.import-csv.show');
        Route::post('holidays/import-csv', [HolidayController::class, 'importHolidays'])->name('holidays.import-csv.store');
        Route::resource('holidays', HolidayController::class);
        Route::get('holidays/toggle-status/{id}', [HolidayController::class, 'toggleStatus'])->name('holidays.toggle-status');
        Route::get('holidays/delete/{id}', [HolidayController::class, 'delete'])->name('holidays.delete');

        /** app settings */
        Route::get('app-settings/index', [AppSettingController::class, 'index'])->name('app-settings.index');
        Route::get('app-settings/toggle-status/{id}', [AppSettingController::class, 'toggleStatus'])->name('app-settings.toggle-status');

        /** General settings */
        Route::resource('general-settings', GeneralSettingController::class);
        Route::get('general-settings/delete/{id}', [GeneralSettingController::class, 'delete'])->name('general-settings.delete');

        /** Leave route */
        Route::resource('leaves', LeaveTypeController::class);
        Route::get('leaves/toggle-status/{id}', [LeaveTypeController::class, 'toggleStatus'])->name('leaves.toggle-status');
        Route::get('leaves/toggle-early-exit/{id}', [LeaveTypeController::class, 'toggleEarlyExit'])->name('leaves.toggle-early-exit');
        Route::get('leaves/delete/{id}', [LeaveTypeController::class, 'delete'])->name('leaves.delete');
        Route::get('leaves/get-leave-types/{earlyExitStatus}', [LeaveTypeController::class, 'getLeaveTypesBasedOnEarlyExitStatus']);

        /** Company Content Management route */
        Route::resource('static-page-contents', StaticPageContentController::class);
        Route::get('static-page-contents/toggle-status/{id}', [StaticPageContentController::class, 'toggleStatus'])->name('static-page-contents.toggle-status');
        Route::get('static-page-contents/delete/{id}', [StaticPageContentController::class, 'delete'])->name('static-page-contents.delete');

        /** Notification route */
        Route::get('notifications/get-nav-notification', [NotificationController::class, 'getNotificationForNavBar'])->name('nav-notifications');
        Route::resource('notifications', NotificationController::class);
        Route::get('notifications/toggle-status/{id}', [NotificationController::class, 'toggleStatus'])->name('notifications.toggle-status');
        Route::get('notifications/delete/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
        Route::get('notifications/send-notification/{id}', [NotificationController::class, 'sendNotificationToAllCompanyUser'])->name('notifications.send-notification');

        /** Attendance route */
        Route::put('employees/night-attendance/{id}', [AttendanceController::class, 'updateNightAttendance'])->name('night_attendances.update');

        Route::resource('attendances', AttendanceController::class);
        Route::get('employees/attendance/check-in/{companyId}/{userId}', [AttendanceController::class, 'checkInEmployee'])->name('employees.check-in');
        Route::get('employees/attendance/check-out/{companyId}/{userId}', [AttendanceController::class, 'checkOutEmployee'])->name('employees.check-out');
        Route::get('employees/attendance/delete/{id}', [AttendanceController::class, 'delete'])->name('attendance.delete');
        Route::get('employees/attendance/change-status/{id}', [AttendanceController::class, 'changeAttendanceStatus'])->name('attendances.change-status');
        
        /** Selfie Review Gallery */
        Route::get('attendance/selfies', [AttendanceController::class, 'selfieGallery'])->name('attendance.selfies');
        
        Route::post('employees/attendance/{type}', [AttendanceController::class, 'dashboardAttendance'])->name('dashboard.takeAttendance');

        /** ZKTeco Devices route */
        Route::resource('zkteco-devices', \App\Http\Controllers\Web\ZktecoDeviceController::class)->except(['show', 'destroy']);
        Route::get('zkteco-devices/delete/{id}', [\App\Http\Controllers\Web\ZktecoDeviceController::class, 'delete'])->name('zkteco-devices.delete');
        Route::get('zkteco-devices/toggle-status/{id}', [\App\Http\Controllers\Web\ZktecoDeviceController::class, 'toggleStatus'])->name('zkteco-devices.toggle-status');

        /** ZKTeco Sync route */
        Route::get('zkteco/sync', [ZKTecoController::class, 'syncAttendance'])->name('zkteco.sync');

        /** Leave route */
        Route::get('employees/leave-request', [LeaveController::class, 'index'])->name('leave-request.index');
        Route::post('leave-request/store', [LeaveController::class, 'storeLeaveRequest'])->name('employee-leave-request.store');
        Route::get('employees/leave-request/show/{leaveId}', [LeaveController::class, 'show'])->name('leave-request.show');
        Route::put('employees/leave-request/status-update/{leaveRequestId}', [LeaveController::class, 'updateLeaveRequestStatus'])->name('leave-request.update-status');
        Route::get('leave-request/create', [LeaveController::class, 'createLeaveRequest'])->name('leave-request.create');
        Route::get('employees/leave-request/add', [LeaveController::class, 'addLeaveRequest'])->name('leave-request.add');
        Route::post('employees/leave-request/add', [LeaveController::class, 'saveLeaveRequest'])->name('leave-request.save');

        /** Time Leave Route */
        Route::get('employees/time-leave-request', [TimeLeaveController::class, 'index'])->name('time-leave-request.index');
        Route::put('employees/time-leave-request/status-update/{leaveRequestId}', [TimeLeaveController::class, 'updateLeaveRequestStatus'])->name('time-leave-request.update-status');
        Route::get('employees/time-leave-request/show/{leaveId}', [TimeLeaveController::class, 'show'])->name('time-leave-request.show');
        Route::get('employees/time-leave-request/create', [TimeLeaveController::class, 'createLeaveRequest'])->name('time-leave-request.create');
        Route::post('employees/time-leave-request/store', [TimeLeaveController::class, 'storeLeaveRequest'])->name('time-leave-request.store');


        /**logout request Routes */
        Route::get('employee/logout-requests', [EmployeeLogOutRequestController::class, 'getAllCompanyEmployeeLogOutRequest'])->name('logout-requests.index');
        Route::get('employee/logout-requests/toggle-status/{employeeId}', [EmployeeLogOutRequestController::class, 'acceptLogoutRequest'])->name('logout-requests.accept');

        /** Notice route */
        Route::resource('notices', NoticeController::class);
        Route::get('notices/toggle-status/{id}', [NoticeController::class, 'toggleStatus'])->name('notices.toggle-status');
        Route::get('notices/delete/{id}', [NoticeController::class, 'delete'])->name('notices.delete');
        Route::get('notices/send-notice/{id}', [NoticeController::class, 'sendNotice'])->name('notices.send-notice');

        /** Team Meeting route */
        Route::resource('team-meetings', TeamMeetingController::class);
        Route::get('team-meetings/delete/{id}', [TeamMeetingController::class, 'delete'])->name('team-meetings.delete');
        Route::get('team-meetings/remove-image/{id}', [TeamMeetingController::class, 'removeImage'])->name('team-meetings.remove-image');

        /** Clients route */
        Route::post('clients/ajax/store', [ClientController::class, 'ajaxClientStore'])->name('clients.ajax-store');
        Route::resource('clients', ClientController::class);
        Route::get('clients/delete/{id}', [ClientController::class, 'delete'])->name('clients.delete');
        Route::get('clients/toggle-status/{id}', [ClientController::class, 'toggleIsActiveStatus'])->name('clients.toggle-status');

        /** Project Management route */
        Route::resource('projects', ProjectController::class);
        Route::get('projects/delete/{id}', [ProjectController::class, 'delete'])->name('projects.delete');
        Route::get('projects/toggle-status/{id}', [ProjectController::class, 'toggleStatus'])->name('projects.toggle-status');
        Route::get('projects/get-assigned-members/{projectId}', [ProjectController::class, 'getProjectAssignedMembersByProjectId'])->name('projects.get-assigned-members');
        Route::get('projects/get-employees-to-add/{addEmployeeType}/{projectId}', [ProjectController::class, 'getEmployeesToAddTpProject'])->name('projects.add-employee');
        Route::post('projects/update-leaders', [ProjectController::class, 'updateLeaderToProject'])->name('projects.update-leader-data');
        Route::post('projects/update-members', [ProjectController::class, 'updateMemberToProject'])->name('projects.update-member-data');

        /** Project Media Gallery */
        Route::get('projects/{id}/gallery', [\App\Http\Controllers\Web\ProjectMediaController::class, 'gallery'])->name('projects.gallery');

        /** Project & Task Attachment route */
        Route::get('projects/attachment/create/{projectId}', [AttachmentController::class, 'createProjectAttachment'])->name('project-attachment.create');
        Route::post('projects/attachment/store', [AttachmentController::class, 'storeProjectAttachment'])->name('project-attachment.store');
        Route::get('tasks/attachment/create/{taskId}', [AttachmentController::class, 'createTaskAttachment'])->name('task-attachment.create');
        Route::post('tasks/attachment/store', [AttachmentController::class, 'storeTaskAttachment'])->name('task-attachment.store');
        Route::get('attachment/delete/{id}', [AttachmentController::class, 'deleteAttachmentById'])->name('attachment.delete');


        /** Task Management route */
        Route::get('tasks-kanban', [TaskController::class, 'kanbanBoard'])->name('tasks.kanban');
        Route::post('tasks/update-status-kanban', [TaskController::class, 'updateStatusKanban'])->name('tasks.update-status-kanban');
        Route::resource('tasks', TaskController::class);
        Route::get('projects/task/create/{projectId}', [TaskController::class, 'createTaskFromProjectPage'])->name('project-task.create');
        Route::get('tasks/delete/{id}', [TaskController::class, 'delete'])->name('tasks.delete');
        Route::get('tasks/toggle-status/{id}', [TaskController::class, 'toggleStatus'])->name('tasks.toggle-status');
        Route::get('tasks/get-all-tasks/{projectId}', [TaskController::class, 'getAllTaskByProjectId'])->name('users.getAllTaskByProjectId');

        /** Task Checklist route */
        Route::post('task-checklists/save', [TaskChecklistController::class, 'store'])->name('task-checklists.store');
        Route::get('task-checklists/edit/{id}', [TaskChecklistController::class, 'edit'])->name('task-checklists.edit');
        Route::put('task-checklists/update/{id}', [TaskChecklistController::class, 'update'])->name('task-checklists.update');
        Route::get('task-checklists/delete/{id}', [TaskChecklistController::class, 'delete'])->name('task-checklists.delete');
        Route::get('task-checklists/toggle-status/{id}', [TaskChecklistController::class, 'toggleIsCompletedStatus'])->name('task-checklists.toggle-status');

        /** Task Comments  route */
        Route::post('task-comment/store', [TaskCommentController::class, 'saveCommentDetail'])->name('task-comment.store');
        Route::get('task-comment/delete/{commentId}', [TaskCommentController::class, 'deleteComment'])->name('comment.delete');
        Route::get('task-comment/reply/delete/{replyId}', [TaskCommentController::class, 'deleteReply'])->name('reply.delete');


        /** Support route */
        Route::get('supports/get-all-query',[SupportController::class,'getAllQueriesPaginated'])->name('supports.index');
        Route::get('supports/change-seen-status/{queryId}', [SupportController::class, 'changeIsSeenStatus'])->name('supports.changeSeenStatus');
        Route::put('supports/update-status/{id}', [SupportController::class, 'changeQueryStatus'])->name('supports.updateStatus');
        Route::get('supports/delete/{id}', [SupportController::class, 'delete'])->name('supports.delete');

        /** Tada route */
        Route::put('tadas/update-status/{id}', [TadaController::class, 'changeTadaStatus'])->name('tadas.update-status');
        Route::resource('tadas', TadaController::class);
        Route::get('tadas/delete/{id}', [TadaController::class, 'delete'])->name('tadas.delete');
        Route::get('tadas/toggle-active-status/{id}', [TadaController::class, 'toggleTadaIsActive'])->name('tadas.toggle-status');

        /** Tada Attachment route */
        Route::get('tadas/attachment/create/{tadaId}', [TadaAttachmentController::class, 'create'])->name('tadas.attachment.create');
        Route::post('tadas/attachment/store', [TadaAttachmentController::class, 'store'])->name('tadas.attachment.store');
        Route::get('tadas/attachment/delete/{id}', [TadaAttachmentController::class, 'delete'])->name('tadas.attachment-delete');

        /** Export data route */
        Route::get('leave-types-export', [DataExportController::class, 'exportLeaveType'])->name('leave-type-export');
        Route::get('leave-requests-export', [DataExportController::class, 'exportEmployeeLeaveRequestLists'])->name('leave-request-export');
        Route::get('employee-detail-export', [DataExportController::class, 'exportEmployeeDetail'])->name('employee-lists-export');
        Route::get('attendance-detail-export', [DataExportController::class, 'exportAttendanceDetail'])->name('attendance-lists-export');

        /** Asset Management route */
        Route::resource('asset-types', AssetTypeController::class,[
            'except' => ['destroy']
        ]);
        Route::get('asset-types/delete/{id}', [AssetTypeController::class, 'delete'])->name('asset-types.delete');
        Route::get('asset-types/toggle-status/{id}', [AssetTypeController::class, 'toggleIsActiveStatus'])->name('asset-types.toggle-status');

        Route::resource('assets', AssetController::class,[
            'except' => ['destroy']
        ]);
        Route::get('assets/delete/{id}', [AssetController::class, 'delete'])->name('assets.delete');
        Route::get('assets/toggle-status/{id}', [AssetController::class, 'changeAvailabilityStatus'])->name('assets.change-Availability-status');

        /** Salary Component route */
        Route::resource('salary-components', SalaryComponentController::class,[
            'except' => ['destroy','show']
        ]);
        Route::get('salary-components/delete/{id}', [SalaryComponentController::class, 'delete'])->name('salary-components.delete');
        Route::get('salary-components/change-status/{id}', [SalaryComponentController::class, 'toggleSalaryComponentStatus'])->name('salary-components.toggle-status');

        /** Payment Methods route */
        Route::resource('payment-methods', PaymentMethodController::class,[
            'except' => ['destroy','show','edit']
        ]);
        Route::get('payment-methods/delete/{id}', [PaymentMethodController::class, 'deletePaymentMethod'])->name('payment-methods.delete');
        Route::get('payment-methods/change-status/{id}', [PaymentMethodController::class, 'togglePaymentMethodStatus'])->name('payment-methods.toggle-status');

        /** Payment Currency route */
        Route::get('payment-currency', [PaymentCurrencyController::class, 'index'])->name('payment-currency.index');
        Route::post('payment-currency', [PaymentCurrencyController::class, 'updateOrSetPaymentCurrency'])->name('payment-currency.save');

        /** Salary TDS route */
        Route::resource('salary-tds', SalaryTDSController::class,[
            'except' => ['destroy','show']
        ]);
        Route::get('salary-tds/delete/{id}', [SalaryTDSController::class, 'deleteSalaryTDS'])->name('salary-tds.delete');
        Route::get('salary-tds/change-status/{id}', [SalaryTDSController::class, 'toggleSalaryTDSStatus'])->name('salary-tds.toggle-status');

        /** Salary Group route */
        Route::resource('salary-groups', SalaryGroupController::class,[
            'except' => ['destroy','show']
        ]);
        Route::get('salary-groups/delete/{id}', [SalaryGroupController::class, 'deleteSalaryGroup'])->name('salary-groups.delete');
        Route::get('salary-groups/change-status/{id}', [SalaryGroupController::class, 'toggleSalaryGroupStatus'])->name('salary-groups.toggle-status');

        /** Employee Salary route */
        Route::resource('employee-salaries', EmployeeSalaryController::class,[
            'except' =>['destroy','create','edit','update','store','show']
        ]);
        Route::get('employee-salaries/update-cycle/{employeeId}/{cycle}', [EmployeeSalaryController::class, 'changeSalaryCycle'])->name('employee-salaries.update-salary-cycle');
        Route::post('employee-salaries/payroll-create', [EmployeeSalaryController::class, 'payrollCreate'])->name('employee-salaries.payroll-create');
        Route::get('employee-salaries/payroll', [EmployeeSalaryController::class, 'payroll'])->name('employee-salary.payroll');
        Route::get('employee-salaries/payroll/{payslipId}', [EmployeeSalaryController::class, 'viewPayroll'])->name('employee-salary.payroll-detail');
        Route::get('employee-salaries/payroll/{payslipId}/print', [EmployeeSalaryController::class, 'printPayslip'])->name('employee-salary.payroll-print');
        Route::get('employee-salaries/payroll-sheet/print', [EmployeeSalaryController::class, 'printPayrollSheet'])->name('employee-salary.payroll-sheet');
        Route::get('employee-salaries/payroll/{payslipId}/edit', [EmployeeSalaryController::class, 'editPayroll'])->name('employee-salary.payroll-edit');
        Route::put('employee-salaries/payroll/{payslipId}/update', [EmployeeSalaryController::class, 'updatePayroll'])->name('employee-salary.payroll-update');
        Route::delete('employee-salaries/payroll/{payslipId}/delete', [EmployeeSalaryController::class, 'deletePayroll'])->name('employee-salary.payroll-delete');

        Route::get('employee-salaries/salary/create/{employeeId}', [EmployeeSalaryController::class, 'createSalary'])->name('employee-salaries.add');
        Route::post('employee-salaries/salary/{employeeId}', [EmployeeSalaryController::class, 'saveSalary'])->name('employee-salaries.store-salary');
        Route::get('employee-salaries/salary/edit/{employeeId}', [EmployeeSalaryController::class, 'editSalary'])->name('employee-salaries.edit-salary');
        Route::put('employee-salaries/salary/{employeeId}', [EmployeeSalaryController::class, 'updateSalary'])->name('employee-salaries.update-salary');
        Route::get('employee-salaries/salary/{employeeId}', [EmployeeSalaryController::class, 'deleteSalary'])->name('employee-salaries.delete-salary');

        Route::put('employee-salaries/{payslipId}/make-payment', [EmployeeSalaryController::class, 'makePayment'])->name('employee-salaries.make_payment');

        /** get weeks list */
        Route::get('employee-salaries/getWeeks/{year}', [EmployeeSalaryController::class, 'getWeeks'])->name('employee-salaries.get-weeks');


        /** Employee Salary History route */
        Route::get('employee-salaries/salary-update/{accountId}', [SalaryHistoryController::class, 'create'])->name('employee-salaries.increase-salary');
        Route::post('employee-salaries/salary-history/store', [SalaryHistoryController::class, 'store'])->name('employee-salaries.updated-salary-store');
        Route::get('employee-salaries/salary-increment-history/{employeeId}', [SalaryHistoryController::class, 'getEmployeeAllSalaryHistory'])->name('employee-salaries.salary-revise-history.show');

        Route::get('advance-salaries/setting/', [AdvanceSalaryController::class, 'setting'])->name('advance-salaries.setting');

        /** Advance Salary route */
        Route::resource('advance-salaries', AdvanceSalaryController::class,[
            'except' => ['destroy','store','edit']
        ]);
        Route::get('advance-salaries/delete/{id}', [AdvanceSalaryController::class, 'delete'])->name('advance-salaries.delete');

        /** Tax report */

        Route::get('employee-salaries/tax-report', [TaxReportController::class, 'index'])->name('payroll.tax-report.index');
        Route::get('employee-salaries/tax-report/{id}/detail', [TaxReportController::class, 'taxReport'])->name('payroll.tax-report.detail');
        Route::get('employee-salaries/tax-report/{id}/print', [TaxReportController::class, 'printTaxReport'])->name('payroll.tax-report.print');
        Route::get('employee-salaries/tax-report/{id}/edit', [TaxReportController::class, 'editTaxReport'])->name('payroll.tax-report.edit');
        Route::put('employee-salaries/tax-report/{id}', [TaxReportController::class, 'updateTaxReport'])->name('payroll.tax-report.update');


        /** Payroll OverTime Setting route */

        Route::resource('overtime', OverTimeSettingController::class,[
        'except' => ['destroy']
        ]);
        Route::get('overtime/delete/{id}', [OverTimeSettingController::class, 'delete'])->name('overtime.delete');
        Route::get('overtime/change-status/{id}', [OverTimeSettingController::class, 'toggleOTStatus'])->name('overtime.toggle-status');


        /** Payroll UnderTime Setting route */
        Route::resource('under-time', UnderTimeSettingController::class,[
            'except' => ['destroy']
        ]);
        Route::get('under-time/delete/{id}', [UnderTimeSettingController::class, 'delete'])->name('under-time.delete');
        Route::get('under-time/change-status/{id}', [UnderTimeSettingController::class, 'toggleUTStatus'])->name('under-time.toggle-status');


        Route::resource('qr', QrCodeController::class,[
            'except' => ['destroy','show']
        ]);
        Route::get('qr/delete/{id}', [QrCodeController::class, 'delete'])->name('qr.destroy');
        Route::get('qr/print/{id}', [QrCodeController::class, 'print'])->name('qr.print');

        Route::get('/nfc', [NFCController::class, 'index'])->name('nfc.index');
        Route::get('/nfc/delete/{id}', [NFCController::class, 'delete'])->name('nfc.destroy');

        /** app settings */
        Route::get('feature/index', [FeatureController::class, 'index'])->name('feature.index');
        Route::get('feature/toggle-status/{id}', [FeatureController::class, 'toggleStatus'])->name('feature.toggle-status');

        /** delete employee leave type */
        Route::get('employees/leave_type/delete/{id}', [UserController::class, 'deleteEmployeeLeaveType'])->name('employee_leave_type.delete');


        /** Award Management route */
        Route::resource('award-types', AwardTypeController::class,[
            'except' => ['destroy']
        ]);
        Route::get('award-types/delete/{id}', [AwardTypeController::class, 'delete'])->name('award-types.delete');
        Route::get('award-types/toggle-status/{id}', [AwardTypeController::class, 'toggleStatus'])->name('award-types.toggle-status');

        Route::resource('awards', AwardController::class,[
            'except' => ['destroy']
        ]);
        Route::get('awards/delete/{id}', [AwardController::class, 'delete'])->name('awards.delete');

        /** language route */
        Route::get('language/change', function() {
            // تم تعطيل تغيير اللغة وجعلها عربية فقط
            return redirect()->back();
        })->name('language.change');

        /** Bonus route */
        Route::resource('bonus', BonusController::class,[
            'except' => ['destroy','show']
        ]);
        Route::get('bonus/delete/{id}', [BonusController::class, 'delete'])->name('bonus.delete');
        Route::get('bonus/change-status/{id}', [BonusController::class, 'toggleBonusStatus'])->name('bonus.toggle-status');

        /** Bonus route */
        Route::resource('fiscal_year', FiscalYearController::class,[
            'except' => ['destroy','show']
        ]);
        Route::get('fiscal_year/delete/{id}', [FiscalYearController::class, 'delete'])->name('fiscal_year.delete');

        /** Bonus route */
        Route::resource('ssf', SSFController::class,[
            'except' => ['destroy','show']
        ]);

        /** Attendance Logs */
        Route::get('attendance/logs', [AttendanceController::class, 'logs'])->name('attendance.log');

        /** Security Logs */
        Route::get('security-logs', [SecurityLogController::class, 'index'])->name('security_logs.index');

        /** calculate tax */
        Route::get('calculate_tax',[EmployeeSalaryController::class, 'calculateTax'])->name('get-tax');

        /** theme change */
        Route::get('change-theme', [ThemeController::class, 'changeTheme'])->name('change-theme');

        /** leave approval route */
        Route::get('leave-approval/get-users-by-role', [LeaveApprovalController::class, 'getEmployeesByRole'])->name('leave-approval.fetchByRole');
        Route::resource('leave-approval', LeaveApprovalController::class,[
            'except' => ['destroy']
        ]);
        Route::get('leave-approval/delete/{id}', [LeaveApprovalController::class, 'delete'])->name('leave-approval.delete');
        Route::get('leave-approval/change-status/{id}', [LeaveApprovalController::class, 'toggleStatus'])->name('leave-approval.toggle-status');

        /** get leave request approvals */
        Route::get('leave-request/get-approvers/{id}', [LeaveController::class, 'getLeaveRequestApproval'])->name('leave-request.approval-details');

        /** Attendance Export */
        Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');

        /** Event route */
        Route::resource('event', EventController::class);
        Route::get('event/delete/{id}', [EventController::class, 'delete'])->name('event.delete');
        Route::get('event/remove-image/{id}', [EventController::class, 'removeImage'])->name('event.remove-image');


        /** Training Management */
        Route::resource('training-types', TrainingTypeController::class,
            ['except' => ['destroy','create','edit']]);
        Route::get('training-types/delete/{id}', [TrainingTypeController::class, 'delete'])->name('training-types.delete');
        Route::get('training-types/toggle-status/{id}', [TrainingTypeController::class, 'toggleStatus'])->name('training-types.toggle-status');

        /** Trainer */
        Route::resource('trainers', TrainerController::class,[
            'except' => ['destroy']
        ]);
        Route::get('trainers/delete/{id}', [TrainerController::class, 'delete'])->name('trainers.delete');
        Route::get('trainers/toggle-status/{id}', [TrainerController::class, 'toggleStatus'])->name('trainers.toggle-status');
        Route::get('trainers/get-all-trainers/{type}', [TrainerController::class, 'getAllTrainersByType'])->name('trainers.getAllTrainersByType');

        /** Trainer */
        Route::resource('training', TrainingController::class,[
            'except' => ['destroy']
        ]);
        Route::get('training/delete/{id}', [TrainingController::class, 'delete'])->name('training.delete');
        Route::get('training/toggle-status/{id}', [TrainingController::class, 'toggleStatus'])->name('training.toggle-status');
    });
});

Route::fallback(function() {
    return view('errors.404');
});
 
Route::get('/final-setup', function () {
    // Clear all caches first
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    // Now, try to run the migration
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true
        ]);
        return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅✅✅ ألف مبروووك! تم إنشاء قاعدة البيانات الجديدة والسيستم يعمل الآن!</h1><h3 style='text-align:center;'>امسح /final-setup من الرابط وافتح الموقع وسجل دخول.</h3>";
    } catch (\Exception $e) {
        return "<h1 style='color:red; text-align:center; margin-top:50px;'>❌ فشل الاتصال بالداتا بيز الجديدة!</h1><h3 style='text-align:center;'>تأكد من أنك كتبت اسم الداتا بيز واليوزر والباسوورد الجداد صح في ملف .env</h3><p style='text-align:center; direction:ltr;'><b>Error:</b> " . $e->getMessage() . "</p>";
    }
});

Route::get('/fix-ui', function () {
    $envPath = base_path('.env');
    $env = file_get_contents($envPath);
    $assetUrl = 'http://72.62.178.127:7777/hr.castle.eg/public';
    
    if (strpos($env, 'ASSET_URL=') !== false) {
        $env = preg_replace('/ASSET_URL=.*/', 'ASSET_URL="' . $assetUrl . '"', $env);
    } else {
        $env .= "\nASSET_URL=\"" . $assetUrl . "\"\n";
    }
    
    file_put_contents($envPath, $env);
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅ تم ضبط مسارات التصميم (CSS & JS) بنجاح!</h1><h3 style='text-align:center;'>امسح /fix-ui من الرابط، واضغط (Ctrl + F5) لتحديث الصفحة.</h3>";
});

Route::get('/fix-zk', function () {
    \Illuminate\Support\Facades\Schema::dropIfExists('zkteco_devices');
    \Illuminate\Support\Facades\DB::table('migrations')->where('migration', 'like', '%zkteco_devices%')->delete();
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅ تم تحديث قاعدة البيانات بنجاح!</h1><h3 style='text-align:center;'>ارجع للوحة التحكم وأضف الجهاز الآن.</h3>";
});

Route::get('/fix-images', function () {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('attendances', 'check_in_image')) {
        \Illuminate\Support\Facades\Schema::table('attendances', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->string('check_in_image')->nullable();
            $table->string('check_out_image')->nullable();
        });
        return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅ تم حل المشكلة وإضافة أعمدة الصور بنجاح!</h1><h3 style='text-align:center;'>امسح /fix-images من الرابط وارجع جرب زر السيلفي الآن.</h3>";
    }
    return "<h1 style='color:blue; text-align:center; margin-top:50px;'>ℹ️ الأعمدة موجودة بالفعل!</h1>";
});

Route::get('/fix-tasks-table', function () {
    if (!\Illuminate\Support\Facades\Schema::hasTable('tasks')) {
        \Illuminate\Support\Facades\Schema::create('tasks', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->text('description')->nullable();
            $table->string('priority')->nullable()->default('medium');
            $table->enum('status', ['not_started', 'in_progress', 'in_review', 'completed', 'cancelled', 'on_hold'])->default('not_started');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_recurring')->default(0);
            $table->string('recurring_frequency')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅ تم إنشاء جدول المهام (tasks) بنجاح!</h1><h3 style='text-align:center;'>امسح /fix-tasks-table من الرابط وارجع جرب إنشاء مهمة الآن.</h3>";
    }
    
    return "<h1 style='color:blue; text-align:center; margin-top:50px;'>ℹ️ الجدول موجود بالفعل!</h1>";
});

Route::get('/add-approve-permission', function () {
    // 1. التأكد من وجود الصلاحية في جدول الصلاحيات
    $permission = \Illuminate\Support\Facades\DB::table('permissions')->where('permission_key', 'approve_task')->first();
    if (!$permission) {
        $permissionId = \Illuminate\Support\Facades\DB::table('permissions')->insertGetId([
            'name' => 'اعتماد وإنهاء المهام',
            'permission_key' => 'approve_task',
            'permission_groups_id' => 20, // جروب إدارة المهام في نظامك
        ]);
    } else {
        $permissionId = $permission->id;
    }

    // 2. إعطاء الصلاحية لدور مدير النظام (admin)
    \Illuminate\Support\Facades\DB::table('permission_roles')->updateOrInsert(['role_id' => 1, 'permission_id' => $permissionId]);

    return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅ تم زرع صلاحية 'اعتماد وإنهاء المهام' وإضافتها للمدير بنجاح!</h1><h3 style='text-align:center;'>امسح /add-approve-permission من الرابط وارجع للسيستم.</h3>";
});

Route::get('/translate-permissions', function () {
    $moduleTranslations = [
        'Role' => 'إدارة الأدوار',
        'Company' => 'إدارة الشركة',
        'Branch' => 'إدارة الفروع',
        'Department' => 'إدارة الأقسام',
        'Post' => 'إدارة المسميات الوظيفية',
        'Employee' => 'إدارة الموظفين',
        'Setting' => 'الإعدادات',
        'Attendance' => 'الحضور والانصراف',
        'Leave' => 'الإجازات',
        'Holiday' => 'العطلات',
        'Notice' => 'التعاميم والإعلانات',
        'Team Meeting' => 'اجتماعات الفريق',
        'Content Management' => 'إدارة المحتوى',
        'Shift Management' => 'إدارة الورديات',
        'Notification' => 'الإشعارات',
        'Support' => 'الدعم الفني',
        'Tada' => 'البدلات والسفريات',
        'Client' => 'العملاء',
        'Project Management' => 'إدارة المشاريع',
        'Task Management' => 'إدارة المهام',
        'Dashboard' => 'لوحة التحكم',
        'Asset Management' => 'إدارة الأصول والعهد',
        'Mobile Notification' => 'إشعارات الجوال',
        'Attendance Method' => 'طرق الحضور',
        'Payroll Management' => 'إدارة مسيرات الرواتب',
        'Payroll Setting' => 'إعدادات الرواتب',
        'Advance Salary' => 'السلف والرواتب المقدمة',
        'Employee Salary' => 'رواتب الموظفين',
        'Feature Control' => 'التحكم في الميزات',
        'Time Leave' => 'الاستئذانات (بالساعات)',
        'Award Management' => 'إدارة المكافآت',
        'Tax Report' => 'التقرير الضريبي',
        'Event Management' => 'إدارة الفعاليات',
        'Training Management' => 'إدارة التدريب',
        'Leave Approval' => 'اعتمادات الإجازات',
        'Employee API' => 'API الموظفين',
        'Attendance API' => 'API الحضور',
        'Leave API' => 'API الإجازات',
        'Support API' => 'API الدعم',
        'Tada API' => 'API البدلات',
        'Task Management API' => 'API المهام',
        'Attendance Method API' => 'API طرق الحضور',
        'Payroll Management API' => 'API الرواتب',
        'Advance Salary API' => 'API السلف',
    ];

    $permissionTranslations = [
        'List Role' => 'عرض الأدوار',
        'Create Role' => 'إنشاء دور',
        'Edit Role' => 'تعديل دور',
        'Delete Role' => 'حذف دور',
        'List Permission' => 'عرض الصلاحيات',
        'Assign Permission' => 'تعيين الصلاحيات',
        'View Company' => 'عرض الشركة',
        'Create Company' => 'إنشاء شركة',
        'Edit Company' => 'تعديل شركة',
        'List Branch' => 'عرض الفروع',
        'Create Branch' => 'إنشاء فرع',
        'Edit Branch' => 'تعديل فرع',
        'Delete Branch' => 'حذف فرع',
        'List Department' => 'عرض الأقسام',
        'Create Department' => 'إنشاء قسم',
        'Edit Department' => 'تعديل قسم',
        'Delete Department' => 'حذف قسم',
        'List Post' => 'عرض المسميات',
        'Create Post' => 'إنشاء مسمى',
        'Edit Post' => 'تعديل مسمى',
        'Delete Post' => 'حذف مسمى',
        'List Employee' => 'عرض الموظفين',
        'Create Employee' => 'إنشاء موظف',
        'Show Detail Employee' => 'عرض تفاصيل الموظف',
        'Edit Employee' => 'تعديل موظف',
        'Delete Employee' => 'حذف موظف',
        'Change Password' => 'تغيير كلمة المرور',
        'Force Logout Employee' => 'إنهاء الجلسة إجبارياً',
        'List Logout Request' => 'عرض طلبات الخروج',
        'Logout Request Accept' => 'قبول طلب الخروج',
        'List General Setting' => 'عرض الإعدادات العامة',
        'General Setting Update' => 'تحديث الإعدادات العامة',
        'List App Setting' => 'عرض إعدادات التطبيق',
        'Update App Setting' => 'تحديث إعدادات التطبيق',
        'List Attendance' => 'عرض الحضور',
        'Attendance CSV Export' => 'تصدير الحضور CSV',
        'Attendance Create' => 'تسجيل حضور يدوي',
        'Attendance Update' => 'تعديل الحضور',
        'Attendance Show' => 'عرض تفاصيل الحضور',
        'Attendance Delete' => 'حذف الحضور',
        'List Leave Type' => 'عرض أنواع الإجازات',
        'Leave Type Create' => 'إنشاء نوع إجازة',
        'Leave Type Edit' => 'تعديل نوع إجازة',
        'Leave Type Delete' => 'حذف نوع إجازة',
        'List Leave Requests' => 'عرض طلبات الإجازات',
        'Show Leave Request Detail' => 'عرض تفاصيل طلب الإجازة',
        'Update Leave request' => 'تحديث طلب الإجازة',
        'Request Leave' => 'طلب إجازة',
        'Create Leave Request' => 'إنشاء طلب إجازة',
        'Grant Admin Leave Permission' => 'صلاحيات إجازة الإدارة',
        'List Holiday' => 'عرض العطلات',
        'Holiday Create' => 'إنشاء عطلة',
        'Show Detail' => 'عرض التفاصيل',
        'Holiday Edit' => 'تعديل عطلة',
        'Holiday Delete' => 'حذف عطلة',
        'Csv Import Holiday' => 'استيراد العطلات CSV',
        'List Notice' => 'عرض التعاميم',
        'Notice Create' => 'إنشاء تعميم',
        'Show Notice Detail' => 'عرض تفاصيل التعميم',
        'Notice Edit' => 'تعديل تعميم',
        'Notice Delete' => 'حذف تعميم',
        'Send Notice' => 'إرسال تعميم',
        'List Team Meeting' => 'عرض الاجتماعات',
        'Team Meeting Create' => 'إنشاء اجتماع',
        'Show Team Meeting Detail' => 'عرض تفاصيل الاجتماع',
        'Team Meeting Edit' => 'تعديل اجتماع',
        'Team Meeting Delete' => 'حذف اجتماع',
        'List Content' => 'عرض المحتوى',
        'Content Create' => 'إنشاء محتوى',
        'Show Content Detail' => 'عرض تفاصيل المحتوى',
        'Content Edit' => 'تعديل المحتوى',
        'Content Delete' => 'حذف المحتوى',
        'List Office Time' => 'عرض أوقات الدوام',
        'Office Time Create' => 'إنشاء وقت دوام',
        'Show Office Time Detail' => 'عرض تفاصيل وقت الدوام',
        'Office Time Edit' => 'تعديل وقت الدوام',
        'Office Time Delete' => 'حذف وقت الدوام',
        'List Notification' => 'عرض الإشعارات',
        'Notification Create' => 'إنشاء إشعار',
        'Show Notification Detail' => 'عرض تفاصيل الإشعار',
        'Notification Edit' => 'تعديل إشعار',
        'Notification Delete' => 'حذف إشعار',
        'Send Notification' => 'إرسال إشعار',
        'View Query List' => 'عرض قائمة الاستفسارات',
        'Show Query Detail' => 'عرض تفاصيل الاستفسار',
        'Update Status' => 'تحديث الحالة',
        'Delete Query' => 'حذف الاستفسار',
        'View Tada List' => 'عرض البدلات',
        'Create Tada' => 'إنشاء بدل',
        'Show Tada Detail' => 'عرض تفاصيل البدل',
        'Edit Tada' => 'تعديل بدل',
        'Delete Tada' => 'حذف بدل',
        'Upload Attachment' => 'رفع مرفق',
        'Delete Attachment' => 'حذف مرفق',
        'View Client List' => 'عرض العملاء',
        'Create Client' => 'إنشاء عميل',
        'Show Client Detail' => 'عرض تفاصيل العميل',
        'Edit Client' => 'تعديل عميل',
        'Delete Client' => 'حذف عميل',
        'View Project List' => 'عرض المشاريع',
        'Create Project' => 'إنشاء مشروع',
        'Show Project Detail' => 'عرض تفاصيل المشروع',
        'Edit Project' => 'تعديل مشروع',
        'Delete Project' => 'حذف مشروع',
        'Upload Project Attachment' => 'رفع مرفقات المشروع',
        'Delete PM Attachment' => 'حذف مرفقات المشروع',
        'View Task List' => 'عرض المهام',
        'Create Task' => 'إنشاء مهمة',
        'Show Task Detail' => 'عرض تفاصيل المهمة',
        'Edit Task' => 'تعديل مهمة',
        'Delete Task' => 'حذف مهمة',
        'Upload Task Attachment' => 'رفع مرفقات المهمة',
        'Create Checklist' => 'إنشاء قائمة مهام',
        'Edit Checklist' => 'تعديل قائمة المهام',
        'Delete Checklist' => 'حذف قائمة المهام',
        'Create Comment' => 'إنشاء تعليق',
        'Delete Comment' => 'حذف تعليق',
        'Show Project Details' => 'عرض تفاصيل المشروع (باللوحة)',
        'Show Client Details' => 'عرض تفاصيل العميل (باللوحة)',
        'Employee Attendance' => 'حضور الموظفين (باللوحة)',
        'View Attendance Summary' => 'عرض ملخص الحضور (باللوحة)',
        'List Asset Type' => 'عرض أنواع الأصول',
        'Create Asset Type' => 'إنشاء نوع أصل',
        'Show Type Detail' => 'عرض تفاصيل نوع الأصل',
        'Edit Asset Type' => 'تعديل نوع أصل',
        'Delete Asset Type' => 'حذف نوع أصل',
        'List Assets' => 'عرض الأصول',
        'Create Assets Detail' => 'إنشاء أصل',
        'Edit Assets Detail' => 'تعديل أصل',
        'Show Assets Detail' => 'عرض تفاصيل الأصل',
        'Delete Assets Detail' => 'حذف أصل',
        'Leave Request Notification' => 'إشعار طلب إجازة',
        'Check In Notification' => 'إشعار تسجيل الدخول',
        'Check Out Notification' => 'إشعار تسجيل الخروج',
        'Support Notification' => 'إشعار الدعم الفني',
        'Tada Notification' => 'إشعار البدلات',
        'Advance Salary Request Notification' => 'إشعار طلب السلفة',
        'List Router' => 'عرض أجهزة الراوتر',
        'Create Router' => 'إضافة راوتر',
        'Edit Router' => 'تعديل راوتر',
        'Delete Router' => 'حذف راوتر',
        'List NFC' => 'عرض بطاقات NFC',
        'Delete NFC' => 'حذف NFC',
        'List QR' => 'عرض رموز QR',
        'Create QR' => 'إنشاء QR',
        'Edit QR' => 'تعديل QR',
        'Delete QR' => 'حذف QR',
        'View Payroll List' => 'عرض مسيرات الرواتب',
        'Generate Payroll' => 'إصدار الرواتب',
        'Show Payroll Detail' => 'عرض تفاصيل مسير الرواتب',
        'Edit Payroll' => 'تعديل مسير الرواتب',
        'Delete Payroll' => 'حذف مسير الرواتب',
        'Payroll Payment' => 'الدفع',
        'Print Payroll' => 'طباعة مسير الرواتب',
        'Add Salary Component' => 'إضافة مكون راتب',
        'Edit Salary Component' => 'تعديل مكون راتب',
        'Delete Salary Component' => 'حذف مكون راتب',
        'Add Salary Group' => 'إضافة مجموعة رواتب',
        'Edit Salary Group' => 'تعديل مجموعة رواتب',
        'Delete Salary Group' => 'حذف مجموعة رواتب',
        'Add Salary TDS Rule' => 'إضافة قاعدة استقطاع',
        'Edit Salary TDS Rule' => 'تعديل قاعدة استقطاع',
        'Delete Salary TDS Rule' => 'حذف قاعدة استقطاع',
        'Add OverTime Setting' => 'إضافة إعداد الإضافي',
        'Edit OverTime Setting' => 'تعديل إعداد الإضافي',
        'Delete OverTime Setting' => 'حذف إعداد الإضافي',
        'Add UnderTime Setting' => 'إضافة إعداد التأخير',
        'Edit UnderTime Setting' => 'تعديل إعداد التأخير',
        'Add Payment Method' => 'إضافة طريقة دفع',
        'Edit Payment Method' => 'تعديل طريقة دفع',
        'Delete Payment Method' => 'حذف طريقة دفع',
        'View Advance Salary List' => 'عرض قائمة السلف',
        'Update Advance Salary' => 'تحديث السلفة',
        'Delete Advance Salary' => 'حذف السلفة',
        'View Employee Salary List' => 'عرض قائمة رواتب الموظفين',
        'Add Employee Salary' => 'إضافة راتب للموظف',
        'Employee Salary History' => 'سجل رواتب الموظف',
        'Employee Salary Increment' => 'زيادة راتب الموظف',
        'Edit Employee Salary' => 'تعديل راتب الموظف',
        'Delete Employee Salary' => 'حذف راتب الموظف',
        'Change Salary Cycle' => 'تغيير دورة الراتب',
        'Feature List' => 'عرض الميزات',
        'Update Feature' => 'تحديث الميزة',
        'Time Leave List' => 'عرض قائمة الاستئذانات',
        'Update Time Leave' => 'تحديث الاستئذان',
        'Create Time Leave' => 'إنشاء استئذان',
        'Award Type List' => 'عرض أنواع المكافآت',
        'Create Award Type' => 'إنشاء نوع مكافأة',
        'Update Award Type' => 'تحديث نوع مكافأة',
        'Delete Award Type' => 'حذف نوع مكافأة',
        'Award List' => 'عرض المكافآت',
        'Create Award' => 'إنشاء مكافأة',
        'Update Award' => 'تحديث مكافأة',
        'Show Award Detail' => 'عرض تفاصيل المكافأة',
        'Delete Award' => 'حذف مكافأة',
        'View Tax Report' => 'عرض التقرير الضريبي',
        'Edit Tax Report' => 'تعديل التقرير الضريبي',
        'Event List' => 'عرض الفعاليات',
        'Create Event' => 'إنشاء فعالية',
        'Update Event' => 'تحديث فعالية',
        'Show Event Detail' => 'عرض تفاصيل الفعالية',
        'Delete Event' => 'حذف فعالية',
        'Training Type List' => 'عرض أنواع التدريب',
        'Create Training Type' => 'إنشاء نوع تدريب',
        'Update Training Type' => 'تحديث نوع تدريب',
        'Show Training Type' => 'عرض تفاصيل نوع التدريب',
        'Delete Training Type' => 'حذف نوع تدريب',
        'Trainer List' => 'عرض المدربين',
        'Create Trainer' => 'إنشاء مدرب',
        'Update Trainer' => 'تحديث مدرب',
        'Show Trainer' => 'عرض تفاصيل المدرب',
        'Delete Trainer' => 'حذف مدرب',
        'Training List' => 'عرض التدريب',
        'Create Training' => 'إنشاء تدريب',
        'Update Training' => 'تحديث تدريب',
        'Show Training' => 'عرض تفاصيل التدريب',
        'Delete Training' => 'حذف تدريب',
        'Leave Approval List' => 'عرض قائمة الاعتمادات',
        'Create Leave Approval' => 'إنشاء اعتماد إجازة',
        'Update Leave Approval' => 'تحديث اعتماد إجازة',
        'Show Leave Approval' => 'عرض تفاصيل الاعتماد',
        'Delete Leave Approval' => 'حذف اعتماد إجازة',
        'View Profile' => 'عرض الملف الشخصي',
        'Allow Password Change' => 'تغيير كلمة المرور',
        'Update Profile' => 'تحديث الملف الشخصي',
        'Show Team Sheet' => 'عرض ورقة الفريق',
        'Allow CheckIn' => 'تسجيل الدخول',
        'Allow CheckOut' => 'تسجيل الخروج',
        'Submit Leave Request' => 'تقديم طلب إجازة',
        'Submit Query' => 'تقديم استفسار',
        'Submit Tada Detail' => 'تقديم تفاصيل البدل',
        'Update Tada Detail' => 'تحديث تفاصيل البدل',
        'Change Task Status' => 'تغيير حالة المهمة',
        'Change Checklist Status' => 'تغيير حالة القائمة',
        'Submit Comment' => 'إرسال تعليق',
        'Reply Delete' => 'حذف رد',
        'Create NFC' => 'إنشاء بطاقة NFC',
        'View Payslip List' => 'عرض قسائم الرواتب',
        'Payslip Detail' => 'تفاصيل قسيمة الراتب',
        'Advance Salary List' => 'قائمة السلف',
        'Add Advance Salary List' => 'تقديم طلب سلفة',
        'Update Advance Salary API' => 'تحديث السلفة'
    ];

    foreach ($moduleTranslations as $en => $ar) {
        \Illuminate\Support\Facades\DB::table('permission_groups')->where('name', $en)->update(['name' => $ar]);
    }

    foreach ($permissionTranslations as $en => $ar) {
        \Illuminate\Support\Facades\DB::table('permissions')->where('name', $en)->update(['name' => $ar]);
    }

    return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅ تم تعريب جميع الصلاحيات والوحدات في النظام بنجاح!</h1><h3 style='text-align:center;'>امسح /translate-permissions من الرابط وارجع لصفحة الصلاحيات.</h3>";
});

Route::get('/test-recurring-tasks', function () {
    $tasks = \App\Models\Task::where('is_recurring', 1)->get();
    if ($tasks->isEmpty()) {
        return "<h2 style='text-align:center; margin-top:50px; color:red;'>لا توجد أي مهام متكررة محفوظة حالياً. قم بإنشاء مهمة متكررة أولاً!</h2>";
    }
    
    $count = 0;
    foreach ($tasks as $task) {
        $newTask = $task->replicate();
        $newTask->status = 'not_started'; // إعادة المهمة المنسوخة لحالة البداية
        $newTask->start_date = now()->toDateString();
        $newTask->end_date = now()->addDays(1)->toDateString();
        $newTask->save();
        
        // نسخ الموظفين المعينين للمهمة الجديدة
        $assignedIds = \Illuminate\Support\Facades\DB::table('task_assigned_members')->where('task_id', $task->id)->pluck('user_id')->toArray();
        foreach ($assignedIds as $userId) {
            \Illuminate\Support\Facades\DB::table('task_assigned_members')->insert([
                'task_id' => $newTask->id,
                'user_id' => $userId,
            ]);
        }
        $count++;
    }
    return "<h1 style='color:green; text-align:center; margin-top:50px;'>✅ تم تشغيل سكريبت المهام المتكررة يدوياً بنجاح!</h1><h3 style='text-align:center;'>تم توليد وتكرار ($count) مهام جديدة للموظفين. ارجع للوحة الكانبان لتراها.</h3>";
});
