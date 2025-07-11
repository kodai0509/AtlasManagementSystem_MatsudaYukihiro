<?php

namespace App\Models\Calendars;

use Illuminate\Database\Eloquent\Model;

class ReserveSettings extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'setting_reserve',
        'setting_part',
        'limit_users',
    ];

    public function users()
    {
        return $this->belongsToMany(
            \App\Models\Users\User::class,
            'reserve_setting_users',
            'reserve_setting_id',
            'user_id'
        );
    }
}
