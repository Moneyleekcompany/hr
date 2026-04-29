<?php

namespace App\Helpers;

class RolePermissionHelper
{
    public static function permissionModuleTypeArray(): array
    {
        return [

            [//1
                "name" => "Web",
                "slug" => "web",
            ],

            [//2
                "name" => "API",
                "slug" => "api",
            ],

        ];
    }

    public static function permissionModuleArray(): array
    {
        return [
            [//1
                "name" => "Role",
                'group_type_id' => 1
            ],
            [//2
                "name" => "Company",
                'group_type_id' => 1
            ],
            [//3
                "name" => "Branch",
                'group_type_id' => 1
            ],
            [//4
                "name" => "Department",
                'group_type_id' => 1
            ],
            [//5
                "name" => "Post",
                'group_type_id' => 1
            ],
            [//6
                "name" => "Employee",
                'group_type_id' => 1
            ],
            [//7
                "name" => "Setting",
                'group_type_id' => 1
            ],
            [//8
                "name" => "Attendance",
                'group_type_id' => 1
            ],
            [//9
                "name" => "Leave",
                'group_type_id' => 1
            ],
            [//10
                "name" => "Holiday",
                'group_type_id' => 1
            ],
            [//11
                "name" => "Notice",
                'group_type_id' => 1
            ],
            [//12
                "name" => "Team Meeting",
                'group_type_id' => 1
            ],
            [//13
                "name" => "Content Management",
                'group_type_id' => 1
            ],
            [//14
                "name" => "Shift Management",
                'group_type_id' => 1
            ],
            [//15
                "name" => "Notification",
                'group_type_id' => 1
            ],
            [//16
                "name" => "Support",
                'group_type_id' => 1
            ],
            [//17
                "name" => "Tada",
                'group_type_id' => 1
            ],
            [//18
                "name" => "Client",
                'group_type_id' => 1
            ],
            [//19
                "name" => "Project Management",
                'group_type_id' => 1
            ],
            [//20
                "name" => "Task Management",
                'group_type_id' => 1
            ],
            [//21
                'name' => 'Employee API',
                'group_type_id' => 2
            ],
            [//22
                'name' => 'Attendance API',
                'group_type_id' => 2
            ],
            [//23
                'name' => 'Leave API',
                'group_type_id' => 2
            ],
            [//24
                'name' => 'Support API',
                'group_type_id' => 2
            ],
            [//25
                'name' => 'Tada API',
                'group_type_id' => 2
            ],
            [//26
                'name' => 'Task Management API',
                'group_type_id' => 2
            ],
            [//27
                "name" => "Dashboard",
                'group_type_id' => 1
            ],

            [//28
                "name" => "Asset Management",
                'group_type_id' => 1
            ],

            [//29
                "name" => "Mobile Notification",
                'group_type_id' => 1
            ],
            [//30
                "name" => "Attendance Method",
                'group_type_id' => 1
            ],
            [//31
                "name" => "Attendance Method API",
                'group_type_id' => 2
            ],
            [//32
                "name" => "Payroll Management",
                'group_type_id' => 1
            ],
            [//33
                "name" => "Payroll Setting",
                'group_type_id' => 1
            ],
            [//34
                "name" => "Advance Salary",
                'group_type_id' => 1
            ],
            [//35
                "name" => "Employee Salary",
                'group_type_id' => 1
            ],
            [//36
                "name" => "Payroll Management API",
                'group_type_id' => 2
            ],
            [//37
                "name" => "Advance Salary API",
                'group_type_id' => 2
            ],
            [//38
                "name" => "Feature Control",
                'group_type_id' => 1
            ],

            [//39
                "name" => "Time Leave",
                'group_type_id' => 1
            ],
            [//40
                "name" => "Award Management",
                'group_type_id' => 1
            ],
            [//41
                "name" => "Tax Report",
                'group_type_id' => 1
            ],
            [//42
                "name" => "Event Management",
                'group_type_id' => 1
            ],

            [//43
                "name" => "Training Management",
                'group_type_id' => 1
            ],
            [//44
                "name" => "Leave Approval",
                'group_type_id' => 1
            ],

        ];
    }

    public static function permissionArray(): array
    {
        return [
            /** Role Permissions */
            [
                "name" => "List Role",
                "permission_key" => "list_role",
                "permission_groups_id" => 1
            ],
            [
                "name" => "Create Role",
                "permission_key" => "create_role",
                "permission_groups_id" => 1
            ],
            [
                "name" => "Edit Role",
                "permission_key" => "edit_role",
                "permission_groups_id" => 1
            ],
            [
                "name" => "Delete Role",
                "permission_key" => "delete_role",
                "permission_groups_id" => 1
            ],
            [
                "name" => "List Permission",
                "permission_key" => "list_permission",
                "permission_groups_id" => 1
            ],
            [
                "name" => "Assign Permission",
                "permission_key" => "assign_permission",
                "permission_groups_id" => 1
            ],

            /** Company Permissions */
            [
                "name" => "View Company",
                "permission_key" => "view_company",
                "permission_groups_id" => 2
            ],
            [
                "name" => "Create Company",
                "permission_key" => "create_company",
                "permission_groups_id" => 2
            ],
            [
                "name" => "Edit Company",
                "permission_key" => "edit_company",
                "permission_groups_id" => 2
            ],

            /** Branch Permissions */
            [
                "name" => "List Branch",
                "permission_key" => "list_branch",
                "permission_groups_id" => 3
            ],
            [
                "name" => "Create Branch",
                "permission_key" => "create_branch",
                "permission_groups_id" => 3
            ],
            [
                "name" => "Edit Branch",
                "permission_key" => "edit_branch",
                "permission_groups_id" => 3
            ],
            [
                "name" => "Delete Branch",
                "permission_key" => "delete_branch",
                "permission_groups_id" => 3
            ],

            /** Department Permissions */
            [
                "name" => "List Department",
                "permission_key" => "list_department",
                "permission_groups_id" => 4
            ],
            [
                "name" => "Create Department",
                "permission_key" => "create_department",
                "permission_groups_id" => 4
            ],
            [
                "name" => "Edit Department",
                "permission_key" => "edit_department",
                "permission_groups_id" => 4
            ],
            [
                "name" => "Delete Department",
                "permission_key" => "delete_department",
                "permission_groups_id" => 4
            ],

            /** Post Permissions */
            [
                "name" => "List Post",
                "permission_key" => "list_post",
                "permission_groups_id" => 5
            ],
            [
                "name" => "Create Post",
                "permission_key" => "create_post",
                "permission_groups_id" => 5
            ],
            [
                "name" => "Edit Post",
                "permission_key" => "edit_post",
                "permission_groups_id" => 5
            ],
            [
                "name" => "Delete Post",
                "permission_key" => "delete_post",
                "permission_groups_id" => 5
            ],

            /** Employee Management Permissions */
            [
                "name" => "List Employee",
                "permission_key" => "list_employee",
                "permission_groups_id" => 6
            ],
            [
                "name" => "Create Employee",
                "permission_key" => "create_employee",
                "permission_groups_id" => 6
            ],
            [
                "name" => "Show Detail Employee",
                "permission_key" => "show_detail_employee",
                "permission_groups_id" => 6
            ],
            [
                "name" => "Edit Employee",
                "permission_key" => "edit_employee",
                "permission_groups_id" => 6
            ],
            [
                "name" => "Delete Employee",
                "permission_key" => "delete_employee",
                "permission_groups_id" => 6
            ],
            [
                "name" => "Change Password",
                "permission_key" => "change_password",
                "permission_groups_id" => 6
            ],
            [
                "name" => "Force Logout Employee",
                "permission_key" => "force_logout",
                "permission_groups_id" => 6
            ],
            [
                "name" => "List Logout Request",
                "permission_key" => "list_logout_request",
                "permission_groups_id" => 6
            ],
            [
                "name" => "Logout Request Accept",
                "permission_key" => "accept_logout_request",
                "permission_groups_id" => 6
            ],

            /** Setting Permissions */
            [
                "name" => "List General Setting",
                "permission_key" => "list_general_setting",
                "permission_groups_id" => 7
            ],
            [
                "name" => "General Setting Update",
                "permission_key" => "general_setting_update",
                "permission_groups_id" => 7
            ],
            [
                "name" => "List App Setting",
                "permission_key" => "list_app_setting",
                "permission_groups_id" => 7
            ],
            [
                "name" => "Update App Setting",
                "permission_key" => "update_app_setting",
                "permission_groups_id" => 7
            ],

            /** Attendance Permissions */
            [
                "name" => "List Attendance",
                "permission_key" => "list_attendance",
                "permission_groups_id" => 8
            ],
            [
                "name" => "Attendance CSV Export",
                "permission_key" => "attendance_csv_export",
                "permission_groups_id" => 8
            ],
            [
                "name" => "Attendance Create",
                "permission_key" => "attendance_create",
                "permission_groups_id" => 8
            ],
            [
                "name" => "Attendance Update",
                "permission_key" => "attendance_update",
                "permission_groups_id" => 8
            ],
            [
                "name" => "Attendance Show",
                "permission_key" => "attendance_show",
                "permission_groups_id" => 8
            ],
            [
                "name" => "Attendance Delete",
                "permission_key" => "attendance_delete",
                "permission_groups_id" => 8
            ],

            /** Leave Permissions */
            [
                "name" => "List Leave Type",
                "permission_key" => "list_leave_type",
                "permission_groups_id" => 9
            ],
            [
                "name" => "Leave Type Create",
                "permission_key" => "leave_type_create",
                "permission_groups_id" => 9
            ],
            [
                "name" => "Leave Type Edit",
                "permission_key" => "leave_type_edit",
                "permission_groups_id" => 9
            ],
            [
                "name" => "Leave Type Delete",
                "permission_key" => "leave_type_delete",
                "permission_groups_id" => 9
            ],
            [
                "name" => "List Leave Requests",
                "permission_key" => "list_leave_request",
                "permission_groups_id" => 9
            ],
            [
                "name" => "Show Leave Request Detail",
                "permission_key" => "show_leave_request_detail",
                "permission_groups_id" => 9
            ],
            [
                "name" => "Update Leave request",
                "permission_key" => "update_leave_request",
                "permission_groups_id" => 9
            ],

            /** Holiday Permissions */
            [
                "name" => "List Holiday",
                "permission_key" => "list_holiday",
                "permission_groups_id" => 10
            ],
            [
                "name" => "Holiday Create",
                "permission_key" => "create_holiday",
                "permission_groups_id" => 10
            ],
            [
                "name" => "Show Detail",
                "permission_key" => "show_holiday",
                "permission_groups_id" => 10
            ],
            [
                "name" => "Holiday Edit",
                "permission_key" => "edit_holiday",
                "permission_groups_id" => 10
            ],
            [
                "name" => "Holiday Delete",
                "permission_key" => "delete_holiday",
                "permission_groups_id" => 10
            ],
            [
                "name" => "Csv Import Holiday",
                "permission_key" => "import_holiday",
                "permission_groups_id" => 10
            ],

            /** Notice Permissions */
            [
                "name" => "List Notice",
                "permission_key" => "list_notice",
                "permission_groups_id" => 11
            ],
            [
                "name" => "Notice Create",
                "permission_key" => "create_notice",
                "permission_groups_id" => 11
            ],
            [
                "name" => "Show Notice Detail",
                "permission_key" => "show_notice",
                "permission_groups_id" => 11
            ],
            [
                "name" => "Notice Edit",
                "permission_key" => "edit_notice",
                "permission_groups_id" => 11
            ],
            [
                "name" => "Notice Delete",
                "permission_key" => "delete_notice",
                "permission_groups_id" => 11
            ],
            [
                "name" => "Send Notice",
                "permission_key" => "send_notice",
                "permission_groups_id" => 11
            ],

            /** Team Meeting Permissions */
            [
                "name" => "List Team Meeting",
                "permission_key" => "list_team_meeting",
                "permission_groups_id" => 12
            ],
            [
                "name" => "Team Meeting Create",
                "permission_key" => "create_team_meeting",
                "permission_groups_id" => 12
            ],
            [
                "name" => "Show Team Meeting Detail",
                "permission_key" => "show_team_meeting",
                "permission_groups_id" => 12
            ],
            [
                "name" => "Team Meeting Edit",
                "permission_key" => "edit_team_meeting",
                "permission_groups_id" => 12
            ],
            [
                "name" => "Team Meeting Delete",
                "permission_key" => "delete_team_meeting",
                "permission_groups_id" => 12
            ],

            /** Content management Permissions */
            [
                "name" => "List Content",
                "permission_key" => "list_content",
                "permission_groups_id" => 13
            ],
            [
                "name" => "Content Create",
                "permission_key" => "create_content",
                "permission_groups_id" => 13
            ],
            [
                "name" => "Show Content Detail",
                "permission_key" => "show_content",
                "permission_groups_id" => 13
            ],
            [
                "name" => "Content Edit",
                "permission_key" => "edit_content",
                "permission_groups_id" => 13
            ],
            [
                "name" => "Content Delete",
                "permission_key" => "delete_content",
                "permission_groups_id" => 13
            ],

            /** Shift management Permissions */
            [
                "name" => "List Office Time",
                "permission_key" => "list_office_time",
                "permission_groups_id" => 14
            ],
            [
                "name" => "Office Time Create",
                "permission_key" => "create_office_time",
                "permission_groups_id" => 14
            ],
            [
                "name" => "Show Office Time Detail",
                "permission_key" => "show_office_time",
                "permission_groups_id" => 14
            ],
            [
                "name" => "Office Time Edit",
                "permission_key" => "edit_office_time",
                "permission_groups_id" => 14
            ],
            [
                "name" => "Office Time Delete",
                "permission_key" => "delete_office_time",
                "permission_groups_id" => 14
            ],

            /** Notification Permissions */
            [
                "name" => "List Notification",
                "permission_key" => "list_notification",
                "permission_groups_id" => 15
            ],
            [
                "name" => "Notification Create",
                "permission_key" => "create_notification",
                "permission_groups_id" => 15
            ],
            [
                "name" => "Show Notification Detail",
                "permission_key" => "show_notification",
                "permission_groups_id" => 15
            ],
            [
                "name" => "Notification Edit",
                "permission_key" => "edit_notification",
                "permission_groups_id" => 15
            ],
            [
                "name" => "Notification Delete",
                "permission_key" => "delete_notification",
                "permission_groups_id" => 15
            ],
            [
                "name" => "Send Notification",
                "permission_key" => "send_notification",
                "permission_groups_id" => 15
            ],

            /** Support Permissions */
            [
                "name" => "View Query List",
                "permission_key" => "view_query_list",
                "permission_groups_id" => 16
            ],
            [
                "name" => "Show Query Detail",
                "permission_key" => "show_query_detail",
                "permission_groups_id" => 16
            ],
            [
                "name" => "Update Status",
                "permission_key" => "update_query_status",
                "permission_groups_id" => 16
            ],
            [
                "name" => "Delete Query",
                "permission_key" => "delete_query",
                "permission_groups_id" => 16
            ],

            /** Tada Permissions */
            [
                "name" => "View Tada List",
                "permission_key" => "view_tada_list",
                "permission_groups_id" => 17
            ],
            [
                "name" => "Create Tada ",
                "permission_key" => "create_tada",
                "permission_groups_id" => 17
            ],
            [
                "name" => "Show Tada Detail",
                "permission_key" => "show_tada_detail",
                "permission_groups_id" => 17
            ],
            [
                "name" => "Edit Tada",
                "permission_key" => "edit_tada",
                "permission_groups_id" => 17
            ],
            [
                "name" => "Delete Tada",
                "permission_key" => "delete_tada",
                "permission_groups_id" => 17
            ],
            [
                "name" => "Upload Attachment ",
                "permission_key" => "create_attachment",
                "permission_groups_id" => 17
            ],
            [
                "name" => "Delete Attachment ",
                "permission_key" => "delete_attachment",
                "permission_groups_id" => 17
            ],

            /** Client Permissions */
            [
                "name" => "View Client List",
                "permission_key" => "view_client_list",
                "permission_groups_id" => 18
            ],
            [
                "name" => "Create Client ",
                "permission_key" => "create_client",
                "permission_groups_id" => 18
            ],
            [
                "name" => "Show Client Detail",
                "permission_key" => "show_client_detail",
                "permission_groups_id" => 18
            ],
            [
                "name" => "Edit Client",
                "permission_key" => "edit_client",
                "permission_groups_id" => 18
            ],
            [
                "name" => "Delete Client",
                "permission_key" => "delete_client",
                "permission_groups_id" => 18
            ],

            /** Project management Permissions */
            [
                "name" => "View Project List",
                "permission_key" => "view_project_list",
                "permission_groups_id" => 19
            ],
            [
                "name" => "Create Project",
                "permission_key" => "create_project",
                "permission_groups_id" => 19
            ],
            [
                "name" => "Show Project Detail",
                "permission_key" => "show_project_detail",
                "permission_groups_id" => 19
            ],
            [
                "name" => "Edit Project",
                "permission_key" => "edit_project",
                "permission_groups_id" => 19
            ],
            [
                "name" => "Delete Project",
                "permission_key" => "delete_project",
                "permission_groups_id" => 19
            ],
            [
                "name" => "Upload Project Attachment",
                "permission_key" => "upload_project_attachment",
                "permission_groups_id" => 19
            ],
            [
                "name" => "Delete PM Attachment",
                "permission_key" => "delete_pm_attachment",
                "permission_groups_id" => 19
            ],

            /** Task management Permissions */
            [
                "name" => "View Task List",
                "permission_key" => "view_task_list",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Create Task",
                "permission_key" => "create_task",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Show Task Detail",
                "permission_key" => "show_task_detail",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Edit Task",
                "permission_key" => "edit_task",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Delete Task",
                "permission_key" => "delete_task",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Upload Task Attachment",
                "permission_key" => "upload_task_attachment",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Create Checklist",
                "permission_key" => "create_checklist",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Edit Checklist",
                "permission_key" => "edit_checklist",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Delete Checklist",
                "permission_key" => "delete_checklist",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Create Comment",
                "permission_key" => "create_comment",
                "permission_groups_id" => 20
            ],
            [
                "name" => "Delete Comment",
                "permission_key" => "delete_comment",
                "permission_groups_id" => 20
            ],

            /** Employee Apl Permissions */

            [
                "name" => "View Profile",
                "permission_key" => "view_profile",
                "permission_groups_id" => 21
            ],
            [
                "name" => "Allow Password Change",
                "permission_key" => "allow_change_password",
                "permission_groups_id" => 21
            ],
            [
                "name" => "Update Profile",
                "permission_key" => "update_profile",
                "permission_groups_id" => 21
            ],
            [
                "name" => "Show Employee Detail",
                "permission_key" => "show_profile_detail",
                "permission_groups_id" => 21
            ],
            [
                "name" => "Show Team Sheet",
                "permission_key" => "list_team_sheet",
                "permission_groups_id" => 21
            ],

            /** Attendance Apl Permissions */
            [
                "name" => "Allow CheckIn",
                "permission_key" => "check_in",
                "permission_groups_id" => 22
            ],
            [
                "name" => "Allow CheckOut",
                "permission_key" => "check_out",
                "permission_groups_id" => 22
            ],
//            [
//                "name" => "Allow Attendance",
//                "permission_key" => "attendance",
//                "permission_groups_id" => 22
//            ],


            /** Leave Apl Permissions */
            [
                "name" => "Submit Leave Request",
                "permission_key" => "leave_request_create",
                "permission_groups_id" => 23
            ],

            /** Query Apl Permissions */
            [
                "name" => "Submit Query",
                "permission_key" => "query_create",
                "permission_groups_id" => 24
            ],

            /** Tada Apl Permissions */
            [
                "name" => "Submit Tada Detail",
                "permission_key" => "tada_create",
                "permission_groups_id" => 25
            ],
            [
                "name" => "Update Tada Detail",
                "permission_key" => "tada_update",
                "permission_groups_id" => 25
            ],
            [
                "name" => "Delete Tada Attachment",
                "permission_key" => "delete_tada_attachment",
                "permission_groups_id" => 25
            ],

            /** Task Mgmt Apl Permissions */
            [
                "name" => "Change Task Status",
                "permission_key" => "edit_task_status",
                "permission_groups_id" => 26
            ],
            [
                "name" => "Change Checklist Status",
                "permission_key" => "toggle_checklist_status",
                "permission_groups_id" => 26
            ],
            [
                "name" => "Submit Comment",
                "permission_key" => "submit_comment",
                "permission_groups_id" => 26
            ],
            [
                "name" => "Comment Delete",
                "permission_key" => "comment_delete",
                "permission_groups_id" => 26
            ],
            [
                "name" => "Reply Delete",
                "permission_key" => "reply_delete",
                "permission_groups_id" => 26
            ],

            /** Dashboard Detail Permissions */
            [
                "name" => "Show Project Details",
                "permission_key" => "project_detail",
                "permission_groups_id" => 27
            ],

            [
                "name" => "Show Client Details",
                "permission_key" => "client_detail",
                "permission_groups_id" => 27
            ],

            [
                "name" => "Employee Attendance",
                "permission_key" => "allow_attendance",
                "permission_groups_id" => 27
            ],

            /** Asset Management  */

            [
                "name" => "List Asset Type",
                "permission_key" => "list_type",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Create Asset Type",
                "permission_key" => "create_type",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Show Type Detail",
                "permission_key" => "show_type",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Edit Asset Type",
                "permission_key" => "edit_type",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Delete Asset Type",
                "permission_key" => "delete_type",
                "permission_groups_id" => 28
            ],

            [
                "name" => "List Assets",
                "permission_key" => "list_assets",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Create Assets Detail",
                "permission_key" => "create_assets",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Edit Assets Detail",
                "permission_key" => "edit_assets",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Show Assets Detail",
                "permission_key" => "show_asset",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Delete Assets Detail",
                "permission_key" => "delete_assets",
                "permission_groups_id" => 28
            ],

            [
                "name" => "Request Leave",
                "permission_key" => "request_leave",
                "permission_groups_id" => 9
            ],

            /** Mobile Notification  */
            [
                "name" => "Leave Request Notification",
                "permission_key" => "employee_leave_request",
                "permission_groups_id" => 29
            ],
            [
                "name" => "Check In Notification",
                "permission_key" => "employee_check_in",
                "permission_groups_id" => 29
            ],

            [
                "name" => "Check Out Notification",
                "permission_key" => "employee_check_out",
                "permission_groups_id" => 29
            ],

            [
                "name" => "Support Notification",
                "permission_key" => "employee_support",
                "permission_groups_id" => 29
            ],

            [
                "name" => "Tada Notification",
                "permission_key" => "tada_alert",
                "permission_groups_id" => 29
            ],

            [
                "name" => "Advance Salary Request Notification",
                "permission_key" => "advance_salary_alert",
                "permission_groups_id" => 29
            ],
            /** Attendance Method  */
            [
                "name" => "List Router",
                "permission_key" => "list_router",
                "permission_groups_id" => 30
            ],
            [
                "name" => "Create Router",
                "permission_key" => "create_router",
                "permission_groups_id" => 30
            ],
            [
                "name" => "Edit Router",
                "permission_key" => "edit_router",
                "permission_groups_id" => 30
            ],
            [
                "name" => "Delete Router",
                "permission_key" => "delete_router",
                "permission_groups_id" => 30
            ],
            [
                "name" => "List NFC",
                "permission_key" => "list_nfc",
                "permission_groups_id" => 30
            ],
            [
                "name" => "Delete NFC",
                "permission_key" => "delete_nfc",
                "permission_groups_id" => 30
            ],
            [
                "name" => "List QR",
                "permission_key" => "list_qr",
                "permission_groups_id" => 30
            ],
            [
                "name" => "Create QR",
                "permission_key" => "create_qr",
                "permission_groups_id" => 30
            ],
            [
                "name" => "Edit QR",
                "permission_key" => "edit_qr",
                "permission_groups_id" => 30
            ],
            [
                "name" => "Delete QR",
                "permission_key" => "delete_qr",
                "permission_groups_id" => 30
            ],

            /** Attendance Method API  */
            [
                "name" => "Create NFC",
                "permission_key" => "create_nfc",
                "permission_groups_id" => 31
            ],

            /** Leave Permissions */
            [
                "name" => "Create Leave Request",
                "permission_key" => "create_leave_request",
                "permission_groups_id" => 9
            ],

            /** Payroll Permissions  -32 */
            [
                "name" => "View Payroll List",
                "permission_key" => "view_payroll_list",
                "permission_groups_id" => 32
            ],
            [
                "name" => "Generate Payroll",
                "permission_key" => "generate_payroll",
                "permission_groups_id" => 32
            ],
            [
                "name" => "Show Payroll Detail",
                "permission_key" => "show_payroll_detail",
                "permission_groups_id" => 32
            ],
            [
                "name" => "Edit Payroll",
                "permission_key" => "edit_payroll",
                "permission_groups_id" => 32
            ],
            [
                "name" => "Delete Payroll",
                "permission_key" => "delete_payroll",
                "permission_groups_id" => 32
            ],

            [
                "name" => "Payroll Payment",
                "permission_key" => "payroll_payment",
                "permission_groups_id" => 32
            ],
            [
                "name" => "Print Payroll",
                "permission_key" => "print_payroll",
                "permission_groups_id" => 32
            ],

            /** Payroll Setting Permissions  -33 */
                /** Salary Components */
                [
                    "name" => "Add Salary Component",
                    "permission_key" => "add_salary_component",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Edit Salary Component",
                    "permission_key" => "edit_salary_component",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Delete Salary Component",
                    "permission_key" => "delete_salary_component",
                    "permission_groups_id" => 33
                ],
                /** Salary Group */
                [
                    "name" => "Add Salary Group",
                    "permission_key" => "add_salary_group",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Edit Salary Group",
                    "permission_key" => "edit_salary_group",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Delete Salary Group",
                    "permission_key" => "delete_salary_group",
                    "permission_groups_id" => 33
                ],
                /** Salary TDS */
                [
                    "name" => "Add Salary TDS Rule",
                    "permission_key" => "add_tds",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Edit Salary TDS Rule",
                    "permission_key" => "edit_tds",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Delete Salary TDS Rule",
                    "permission_key" => "delete_tds",
                    "permission_groups_id" => 33
                ],
                /** Overtime */
                [
                    "name" => "Add OverTime Setting",
                    "permission_key" => "add_overtime",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Edit OverTime Setting",
                    "permission_key" => "edit_overtime",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Delete OverTime Setting",
                    "permission_key" => "delete_overtime",
                    "permission_groups_id" => 33
                ],
                /** Undertime */
                [
                    "name" => "Add UnderTime Setting",
                    "permission_key" => "add_undertime",
                    "permission_groups_id" => 33
                ],

                [
                    "name" => "Edit UnderTime Setting",
                    "permission_key" => "edit_undertime",
                    "permission_groups_id" => 33
                ],

                 /** Payment Methods */
                [
                    "name" => "Add Payment Method",
                    "permission_key" => "add_payment_method",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Edit Payment Method",
                    "permission_key" => "edit_payment_method",
                    "permission_groups_id" => 33
                ],
                [
                    "name" => "Delete Payment Method",
                    "permission_key" => "delete_payment_method",
                    "permission_groups_id" => 33
                ],


            /** Advance Salary Permissions  -34 */
            [
                "name" => "View Advance Salary List",
                "permission_key" => "view_advance_salary_list",
                "permission_groups_id" => 34
            ],
            [
                "name" => "Update Advance Salary",
                "permission_key" => "update_advance_salary",
                "permission_groups_id" => 34
            ],
            [
                "name" => "Delete Advance Salary",
                "permission_key" => "delete_advance_salary",
                "permission_groups_id" => 34
            ],

            /** Employee Salary Permissions  -35 */
            [
                "name" => "View Employee Salary List",
                "permission_key" => "view_salary_list",
                "permission_groups_id" => 35
            ],
            [
                "name" => "Add Employee Salary",
                "permission_key" => "add_salary",
                "permission_groups_id" => 35
            ],
            [
                "name" => "Employee Salary History",
                "permission_key" => "show_salary_history",
                "permission_groups_id" => 35
            ],
            [
                "name" => "Employee Salary Increment",
                "permission_key" => "salary_increment",
                "permission_groups_id" => 35
            ],
            [
                "name" => "Edit Employee Salary",
                "permission_key" => "edit_salary",
                "permission_groups_id" => 35
            ],
            [
                "name" => "Delete Employee Salary",
                "permission_key" => "delete_salary",
                "permission_groups_id" => 35
            ],
            [
                "name" => "Change Salary Cycle",
                "permission_key" => "change_salary_cycle",
                "permission_groups_id" => 35
            ],

            /** Payroll Management API  -36 */
            [
                "name" => "View Payslip List",
                "permission_key" => "view_payslip_list",
                "permission_groups_id" => 36
            ],
            [
                "name" => "Payslip Detail",
                "permission_key" => "view_payslip_detail",
                "permission_groups_id" => 36
            ],

            /** Advance Salary API  -37 */
            [
                "name" => "Advance Salary List",
                "permission_key" => "advance_salary_list",
                "permission_groups_id" => 37
            ],
            [
                "name" => "Add Advance Salary List",
                "permission_key" => "add_advance_salary",
                "permission_groups_id" => 37
            ],
            [
                "name" => "Update Advance Salary API",
                "permission_key" => "update_advance_salary_api",
                "permission_groups_id" => 37
            ],

            /** Feature Control  -38 */
            [
                "name" => "Feature List",
                "permission_key" => "feature_list",
                "permission_groups_id" => 38
            ],

            [
                "name" => "Update Feature",
                "permission_key" => "update_feature",
                "permission_groups_id" => 38
            ],

            /** Time Leave   -39 */
            [
                "name" => "Time Leave List",
                "permission_key" => "time_leave_list",
                "permission_groups_id" => 39
            ],

            [
                "name" => "Update Time Leave",
                "permission_key" => "update_time_leave",
                "permission_groups_id" => 39
            ], [
                "name" => "Create Time Leave",
                "permission_key" => "create_time_leave_request",
                "permission_groups_id" => 39
            ],

            /** Award Management  -40 */
            /** Award Type */
            [
                "name" => "Award Type List",
                "permission_key" => "award_type_list",
                "permission_groups_id" => 40
            ],
            [
                "name" => "Create Award Type",
                "permission_key" => "create_award_type",
                "permission_groups_id" => 40
            ],
            [
                "name" => "Update Award Type",
                "permission_key" => "update_award_type",
                "permission_groups_id" => 40
            ],
            [
                "name" => "Delete Award Type",
                "permission_key" => "delete_award_type",
                "permission_groups_id" => 40
            ],
            /** Award  */
            [
                "name" => "Award List",
                "permission_key" => "award_list",
                "permission_groups_id" => 40
            ],
            [
                "name" => "Create Award",
                "permission_key" => "create_award",
                "permission_groups_id" => 40
            ],
            [
                "name" => "Update Award",
                "permission_key" => "update_award",
                "permission_groups_id" => 40
            ],
            [
                "name" => "Show Award Detail",
                "permission_key" => "show_award",
                "permission_groups_id" => 40
            ],
            [
                "name" => "Delete Award",
                "permission_key" => "delete_award",
                "permission_groups_id" => 40
            ],
            /** Dashboard Detail Permissions */

            [
                "name" => "View Attendance Summary",
                "permission_key" => "attendance_summary",
                "permission_groups_id" => 27
            ],
            /** Tax report Permissions  -41 */
            [
                "name" => "View Tax Report",
                "permission_key" => "view_tax_report",
                "permission_groups_id" => 41
            ],
            [
                "name" => "Edit Tax Report",
                "permission_key" => "edit_tax_report",
                "permission_groups_id" => 41
            ],

            /** Event Management  -42 */
            [
                "name" => "Event List",
                "permission_key" => "list_event",
                "permission_groups_id" => 42
            ],
            [
                "name" => "Create Event",
                "permission_key" => "create_event",
                "permission_groups_id" => 42
            ],
            [
                "name" => "Update Event",
                "permission_key" => "edit_event",
                "permission_groups_id" => 42
            ],
            [
                "name" => "Show Event Detail",
                "permission_key" => "show_event",
                "permission_groups_id" => 42
            ],
            [
                "name" => "Delete Event",
                "permission_key" => "delete_event",
                "permission_groups_id" => 42
            ],

            /** Training Management  -42 */
            /** Training Type */
            [
                "name" => "Training Type List",
                "permission_key" => "training_type_list",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Create Training Type",
                "permission_key" => "create_training_type",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Update Training Type",
                "permission_key" => "update_training_type",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Show Training Type",
                "permission_key" => "show_training_type",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Delete Training Type",
                "permission_key" => "delete_training_type",
                "permission_groups_id" => 43
            ],

            /** Trainers */
            [
                "name" => "Trainer List",
                "permission_key" => "list_trainer",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Create Trainer",
                "permission_key" => "create_trainer",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Update Trainer",
                "permission_key" => "update_trainer",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Show Trainer",
                "permission_key" => "show_trainer",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Delete Trainer",
                "permission_key" => "delete_trainer",
                "permission_groups_id" => 43
            ],
            /** Training */
            [
                "name" => "Training List",
                "permission_key" => "list_training",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Create Training",
                "permission_key" => "create_training",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Update Training",
                "permission_key" => "update_training",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Show Training",
                "permission_key" => "show_training",
                "permission_groups_id" => 43
            ],
            [
                "name" => "Delete Training",
                "permission_key" => "delete_training",
                "permission_groups_id" => 43
            ],


            /** Leave Permissions */
            [
                "name" => "Grant Admin Leave Permission",
                "permission_key" => "access_admin_leave",
                "permission_groups_id" => 9
            ],
            /** Leave Approval Permissions */
            [
                "name" => "Leave Approval List",
                "permission_key" => "list_leave_approval",
                "permission_groups_id" => 44
            ],
            [
                "name" => "Create Leave Approval",
                "permission_key" => "create_leave_approval",
                "permission_groups_id" => 44
            ],
            [
                "name" => "Update Leave Approval",
                "permission_key" => "update_leave_approval",
                "permission_groups_id" => 44
            ],
            [
                "name" => "Show Leave Approval",
                "permission_key" => "show_leave_approval",
                "permission_groups_id" => 44
            ],
            [
                "name" => "Delete Leave Approval",
                "permission_key" => "delete_leave_approval",
                "permission_groups_id" => 44
            ],

        ];
    }

}
