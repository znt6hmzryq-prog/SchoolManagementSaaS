<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'plan_type',
        'subscription_status',
        'subscription_expires_at',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
    ];

    // 🔥 Relationships

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class);
    }

    public function teachers()
    {
        return $this->hasMany(\App\Models\Teacher::class);
    }

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }
}