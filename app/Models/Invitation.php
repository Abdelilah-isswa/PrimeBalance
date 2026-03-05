<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['company_id', 'email', 'role', 'token', 'status', 'invited_by', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
