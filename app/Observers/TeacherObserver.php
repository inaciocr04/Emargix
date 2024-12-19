<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Models\User;

class TeacherObserver
{
    /**
     * Handle the Teacher "created" event.
     */
    public function created(Teacher $teacher): void
    {
        $this->linkTeachersToUsers();

    }

    public function linkTeachersToUsers()
    {
        $users = User::all();

        $teachers = Teacher::all();

        foreach ($teachers as $teacher) {
            $matchingUser = $users->firstWhere('email', $teacher->unistra_email);

            if ($matchingUser){
                $teacher->user_id = $matchingUser->id;
                $teacher->save();
            }
        }
    }

    /**
     * Handle the Teacher "updated" event.
     */
    public function updated(Teacher $teacher): void
    {
        //
    }

    /**
     * Handle the Teacher "deleted" event.
     */
    public function deleted(Teacher $teacher): void
    {
        //
    }

    /**
     * Handle the Teacher "restored" event.
     */
    public function restored(Teacher $teacher): void
    {
        //
    }

    /**
     * Handle the Teacher "force deleted" event.
     */
    public function forceDeleted(Teacher $teacher): void
    {
        //
    }
}
