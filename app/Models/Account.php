<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'type', 'status', 'balance', 'overdraft_limit', 'interest_rate', 'blocking_reason'
    ];

    
    public function users()
    {
        return $this->belongsToMany(User::class, 'account_users')->withPivot('is_guardian', 'accepted_closure')
        ->withTimestamps();
    }

    
    public function guardians()
    {
        return $this->belongsToMany(User::class, 'account_users')->wherePivot('is_guardian', 1)
        ->withTimestamps();
    }

    
    public function coOwners()
    {
        return $this->belongsToMany(User::class, 'account_users')->wherePivot('is_guardian', 0)->withTimestamps();
    }

    
    public function transfersSent()
    {
        return $this->hasMany(Transfer::class, 'sender_account_id');
    }

    
    public function transfersReceived()
    {
        return $this->hasMany(Transfer::class, 'receiver_account_id');
    }
}
