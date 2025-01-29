<?php

use App\Http\Controllers\AttendanceFormController;
use App\Http\Controllers\StudentSignatureController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherSignatureController;
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
    return view('scan'); // La vue contenant le scanner
})->name('qr.scan');


