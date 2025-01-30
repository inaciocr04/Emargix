<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\User;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        $this->linkStudentsToUsers();
    }

    public function linkStudentsToUsers()
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

    /**
     * Handle the Student "updated" event.
     */
    public function updated(Student $student): void
    {
        $this->linkStudentsToUsers();
    }

    /**
     * Handle the Student "deleted" event.
     */
    public function deleted(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "restored" event.
     */
    public function restored(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "force deleted" event.
     */
    public function forceDeleted(Student $student): void
    {
        //
    }
}
