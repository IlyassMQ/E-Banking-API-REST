<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;


class AccountController extends Controller
{
    
    private $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function store(CreateAccountRequest $request)
    {
        $account = $this->accountService->createAccount(
            auth()->user(),
            $request->validated()
        );

        return response()->json(['Account' => $account], 201);
    
    }

    public function show($id)
    {
        $account = Account::with('users')->findOrFail($id);
        return response()->json(['Account' => $account]);
    }


    public function addCoOwner(Request $request, $id)
    {
    $account = Account::findOrFail($id);

    $this->accountService->addCoOwner(
        $account,
        auth()->user(),
        $request->user_id
    );

    return response()->json(['message' => 'Co-owner added']);
    }

    public function close($id)
{
    $account = Account::findOrFail($id);

    $isClosed = $this->accountService->closeAccount($account);

    if ($isClosed == false) {
        return response()->json([
            'message' => 'Erreur : Le solde doit être 0 DH pour fermer le compte.'
        ], 400);
    }

    return response()->json([
        'message' => 'Le compte a bien été fermé.'
    ],201);
}
}

