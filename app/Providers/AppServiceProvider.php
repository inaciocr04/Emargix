<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Observers\StudentObserver;
use App\Observers\TeacherObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Student::observe(StudentObserver::class);
        Teacher::observe(TeacherObserver::class);
    }
}
