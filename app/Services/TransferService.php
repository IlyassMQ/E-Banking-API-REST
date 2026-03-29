<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Exception;

class TransferService
{
    
        public function transfer(User $user, array $data)
    {
        $from = Account::findOrFail($data['from_account_id']);
        $to = Account::findOrFail($data['to_account_id']);
        $amount = $data['amount'];

        if ($from->id === $to->id) {
            throw new Exception("Cannot transfer to same account");
        }

        if ($from->status !== 'Active' || $to->status !== 'Active') {
            throw new Exception("Account is not active");
        }

        $isUserInAccount = $from->users()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isUserInAccount) {
            throw new Exception("Unauthorized");
        }

        if ($from->type === 'Minor') {
            $isGuardian = $from->users()
                ->where('user_id', $user->id)
                ->wherePivot('role', 'guardian')
                ->exists();

            if (!$isGuardian) {
                throw new Exception("Only guardian can transfer");
            }
        }

        
        if ($from->type === 'Checking') {
            if (($from->balance + $from->overdraft_limit) < $amount) {
                throw new Exception("Overdraft limit exceeded");
            }
        } else {
            if ($from->balance < $amount) {
                throw new Exception("Insufficient balance");
            }
        }

        $from->balance -= $amount;
        $from->save();

        $to->balance += $amount;
        $to->save();

        return [
            'message' => 'Transfer successful'
        ];
    }
}