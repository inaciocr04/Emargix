<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->linkUsersToStudents();
        $this->linkUsersToTeachers();
    }

    public function linkUsersToStudents()
    {
        $users = User::all();

        $students = Student::all();

        foreach ($students as $student){

            $matchingUser = $users->firstWhere('email', $student->email);

            if ($matchingUser){
                $student->user_id = $matchingUser->id;
                $student->save();
            }
        }
    }

    public function linkUsersToTeachers()
    {
        $users = User::all();

        $teachers = Teacher::all();

        foreach ($teachers as $teacher) {
            $matchingUser = $users->firstWhere('email', $teacher->email);

            if ($matchingUser){
                $teacher->user_id = $matchingUser->id;
                $teacher->save();
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
