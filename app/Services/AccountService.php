<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Exception;

class AccountService
{
    
public function createAccount(User $currentUser, array $data) {
        
        $overdraft = ($data['type'] === 'Checking') ? ($data['overdraft_limit'] ?? 1000) : 0;
        $interest  = ($data['type'] !== 'Checking') ? ($data['interest_rate'] ?? 0) : 0;

        
        $account = Account::create([
            'type' => $data['type'],
            'balance' => 0,
            'overdraft_limit' => $overdraft,
            'interest_rate' => $interest,
            'status' => 'ACTIVE',
        ]);

        
        if ($data['type'] === 'Minor') {
            $child = User::findOrFail($data['child_id']);
            $guardian = User::findOrFail($data['guardian_id']);

            
            if ($child->date_naissance->age >= 18) {
                throw new Exception("L'enfant doit avoir moins de 18 ans.");
            }

            
            $account->users()->attach($child->id, ['role' => 'owner']);
            $account->users()->attach($guardian->id, ['role' => 'guardian']);
        } else {
            
            $account->users()->attach($currentUser->id, ['role' => 'owner']);
        }

        return $account;
    }

    public function addCoOwner(Account $account, User $user, int $newUserId) {
    if ($account->status !== 'Active') {
        throw new Exception("Account is not active");
    }

    $isOwner = $account->users()->where('user_id', $user->id)
        ->wherePivot('role', 'owner')->exists();

    if (!$isOwner) {
        throw new Exception("Unauthorized");
    }

    $alreadyExists = $account->users()
        ->where('user_id', $newUserId)->exists();

    if ($alreadyExists) {
        throw new Exception("User already attached");
    }

    $account->users()->attach($newUserId, [
        'role' => 'owner'
    ]);
    }

    public function closeAccount(Account $account) {
    if ($account->balance != 0) {
        throw new Exception("Le solde doit être à 0 pour clôturer le compte");
    }

    $account->update(['status' => 'CLOSED']);
    }
}
    