<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\CompanyDetailsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PageController::class, 'index']);
Route::get('/logout', [AccountController::class, 'logout']);
Route::get('/aboutus', [PageController::class, 'aboutus']);
Route::get('/contactus', [PageController::class, 'contactus']);
Route::get('/company', [CompanyDetailsController::class, 'get']);
Route::get('/news', [NewsController::class, 'get']);
Route::get('/newsevent', [PageController::class, 'newsevent']);
Route::get('/terms ', [PageController::class, 'terms']);

Route::group(['middleware' => 'check_auth'], function () {
    Route::prefix('report')->group(function () {
        Route::get('services', [ReportController::class, 'get_services_for_report']);
    });
    Route::prefix('student')->group(function () {
        Route::get('available_services', [ServiceController::class, 'get_available_services_for_student']);
        Route::get('enrollment_history', [ServiceController::class, 'get_student_enrollment_history']);
        Route::get('payments', [PaymentController::class, 'get_student_verified_payments']);
    });
    Route::group(['middleware' => 'admin_only'], function () {
        Route::get('/database/export', [DatabaseController::class, 'export'])->name('database.export');
    });

    Route::get('/dashboard', [PageController::class, 'dashboard']);
    Route::get('/account', [PageController::class, 'account']);
    Route::get('/account/details', [AccountController::class, 'get_account_details']);
    Route::get('/accounts', [PageController::class, 'account_management']);
    Route::get('/audit_trail/get', [AuditTrailController::class, 'get']);
    Route::get('/company/get', [CompanyDetailsController::class, 'get']);
    Route::get('/enrollment', [PageController::class, 'student_enrollment']);
    Route::get('/enrollment/{mode}', [PageController::class, 'student_enrollment']);
    Route::get('/enrollment_form/{service_id}/{batch}', [PageController::class, 'enrollment_form']);
    Route::get('/faqs', [HelpController::class, 'get']);
    Route::get('/get_accounts', [AccountController::class, 'get_accounts']);
    Route::get('/get_enrolled_students', [AccountController::class, 'get_enrolled_students']);
    Route::get('/get_enrolled_students_for_admin', [AccountController::class, 'get_enrolled_students_for_admin']);
    Route::get('/get_enrollments', [ServiceController::class, 'get_enrollments']);
    Route::get('/get_inquiries', [InquiryController::class, 'get']);
    Route::get('/instructors', [AccountController::class, 'get_instructors']);
    Route::get('/notifications/read', [NotificationController::class, 'mark_as_read']);
    Route::get('/help', [PageController::class, 'help']);
    Route::get('/pay', [PaymentController::class, 'pay']);
    Route::get('/requirements', [RequirementController::class, 'get']);
    Route::get('/rooms', [RoomController::class, 'get']);
    Route::get('/staffs', [AccountController::class, 'get_staffs']);
    Route::get('/service/schedule/service_batches', [ServiceController::class, 'get_service_batches']);
    Route::get('/services/available', [ServiceController::class, 'get_available_services']);
    Route::get('/services/sort/{sort_by}', [ServiceController::class, 'get']);
    Route::get('/service/options', [ServiceController::class, 'get_options']);
    Route::get('/payment/success', [PaymentController::class, 'payment_success']);
    Route::get('/vehicles', [VehicleController::class, 'get']);

    Route::prefix('account')->group(function () {
        Route::post('delete', [AccountController::class, 'delete_account']);
        Route::post('save', [AccountController::class, 'save_account']);
    });
    Route::prefix('enrolled_student')->group(function () {
        Route::post('delete', [AccountController::class, 'delete_enrolled']);
        Route::post('save', [AccountController::class, 'save_enrolled']);
        Route::post('reschedule', [AccountController::class, 'rescheduled_enrolled']);
    });
    Route::prefix('faq')->group(function () {
        Route::post('delete', [HelpController::class, 'delete']);
        Route::post('disable', [HelpController::class, 'disable']);
        Route::post('save', [HelpController::class, 'save']);
    });
    Route::prefix('messages')->group(function () {
        Route::post('delete', [MessageController::class, 'delete']);
        Route::post('send_reply', [MessageController::class, 'reply_message']);
        Route::post('send_message', [MessageController::class, 'send_message']);
    });

    Route::prefix('news')->group(function () {
        Route::post('delete', [NewsController::class, 'delete']);
        Route::post('disable', [NewsController::class, 'disable']);
        Route::post('save', [NewsController::class, 'save']);
    });
    Route::prefix('report')->group(function () {
        Route::post('enrolled_students', [ReportController::class, 'get_enrolled_students']);
        Route::post('income', [ReportController::class, 'get_income_report']);
    });
    Route::prefix('requirement')->group(function () {
        Route::post('delete', [RequirementController::class, 'delete']);
        Route::post('save', [RequirementController::class, 'save']);
    });
    Route::prefix('room')->group(function () {
        Route::post('delete', [RoomController::class, 'delete']);
        Route::post('save', [RoomController::class, 'save']);
    });
    Route::prefix('service')->group(function () {
        Route::post('delete', [ServiceController::class, 'delete']);
        Route::post('save', [ServiceController::class, 'save']);
        Route::post('schedules', [ServiceController::class, 'get_schedules']);
        Route::post('schedule/cancel', [ServiceController::class, 'cancel_schedule']);
        Route::post('schedule/batches', [ServiceController::class, 'get_batches']);
        Route::post('schedule/save', [ServiceController::class, 'save_schedule']);
        Route::post('schedule/update', [ServiceController::class, 'update_schedule']);
    });
    Route::prefix('student')->group(function () {
        Route::post('check_enrollment_form', [ServiceController::class, 'check_enrollment_form']);
        Route::post('service', [ServiceController::class, 'get_service_schedules']);
        Route::post('services', [ServiceController::class, 'get_services_from_date']);
        Route::get('reschedule', [ServiceController::class, 'get_reschedule_enrollments']);
    });
    Route::prefix('vehicle')->group(function () {
        Route::post('delete', [VehicleController::class, 'delete']);
        Route::post('save', [VehicleController::class, 'save']);
    });
    Route::group(['middleware' => 'admin_only'], function () {
        Route::post('/database/import', [DatabaseController::class, 'import'])->name('database.import');
    });
    Route::post('/account/details_update', [AccountController::class, 'update_account_details']);
    Route::post('/account/password_update', [AccountController::class, 'update_account_password']);
    Route::post('/company/save', [CompanyDetailsController::class, 'save']);
    Route::post('/messages/get', [MessageController::class, 'get']);
    Route::post('/notifications/get', [NotificationController::class, 'get']);
    Route::post('/notifications/single_read', [NotificationController::class, 'mark_single_notif_as_read']);
    Route::post('/payment/update', [PaymentController::class, 'confirm_payment']);
    Route::post('/reschedule/save', [ServiceController::class, 'reschedule_student']);
});

Route::post('/inquiry/save', [InquiryController::class, 'save']);
Route::post('/login', [AccountController::class, 'login']);
Route::post('/news/active', [NewsController::class, 'get_active']);
Route::post('/password', [AccountController::class, 'get_password']);
Route::post('/register', [AccountController::class, 'register']);
Route::prefix('inquiry')->group(function () {
    Route::post('save', [InquiryController::class, 'save']);
});
