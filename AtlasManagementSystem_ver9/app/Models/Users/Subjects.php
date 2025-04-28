<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $fillable = ['subject'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'subject_users', 'subject_id', 'user_id');
    }
}
