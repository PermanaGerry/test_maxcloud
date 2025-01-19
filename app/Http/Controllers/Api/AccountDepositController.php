<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AccountDepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $account = Account::with('user')->where('user_id', auth()->user()->id)->first();
        return response()->json([
            'message' => 'success',
            'data' => $account
        ], Response::HTTP_OK);
    }

    /**
     * Top up saldo account
     */
    public function deposit(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'amount' => ['required', 'decimal:2'],
        ]);

        if ($validated->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $account = Account::with('user')->where('user_id', auth()->user()->id)->first();
            $account->balance = $account->balance + $request->amount;
            $account->save();

            Entry::create([
                'account_id' => $account->id,
                'amount' => $request->amount
            ]);

            DB::commit();

            return response()->json([
                'message' => 'success',
                'data' => $account
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_BAD_REQUEST);
        }

    }

    public function billing()
    {

    }

}
