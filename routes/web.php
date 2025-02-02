<?php

use App\Http\Controllers\AttendanceFormController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\StudentSignatureController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherSignatureController;
use App\Http\Middleware\UserIsManager;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::get('/teacher/planning', [TeacherController::class, 'showPlanning'])->name('teacher.planning');
Route::get  ('/teacher/planning/generate-qr-code/{eventId}', [TeacherController::class, 'generateQrCode'])
    ->name('attendance.generateQrCode');

Route::get('/attendance/form/{attendanceForm}', [AttendanceFormController::class, 'showForm'])
    ->name('attendance.form');

Route::get('student/signature/create/{eventId}', [StudentSignatureController::class, 'create'])
    ->name('studentSignature.create');

Route::post('/student-signature/store/{studentId}/{eventId}', [StudentSignatureController::class, 'store'])
    ->name('studentSignature.store');


Route::get('/attendance/event/{eventId}', [AttendanceFormController::class, 'showAttendanceList'])->name('attendance.list');
Route::get('/teacher/attendance-list', [AttendanceFormController::class, 'getAttendanceFormsByTeacher'])->name('attendance.attendance-list');

Route::post('/teacher/signature/{eventId}', [TeacherSignatureController::class, 'store'])->name('teacherSignature.store');


Route::get('/scan', function () {
    return view('student.scan');
})->name('qr.scan');

// Dans routes/web.php
Route::get('/export-attendance/{eventId}', [ExportController::class, 'export'])->name('export');
Route::get('/export-attendance-pdf/{eventId}', [ExportController::class, 'exportPdf'])->name('export.attendance');



Route::name('manager.')
    ->prefix('/manager')
    ->middleware(UserIsManager::class)
    ->group(function () {
        Route::get('/manager/attendance-list', [AttendanceFormController::class, 'getAttendanceFormsManager'])->name('attendance-list');
        Route::post('/manager/import', [ImportController::class, 'import'])->name('import');
    });



