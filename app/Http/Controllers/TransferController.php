<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Services\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    private $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function store(TransferRequest $request)
    {
        $result = $this->transferService->transfer(
            auth()->user(),
            $request->validated()
        );

        return response()->json($result);
    }
}
