<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Billing;
use App\Models\PackageSubscribe;
use App\Models\UserSubscribes;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserSubscribeController extends Controller
{

    public function list()
    {
        $userSubscribes = UserSubscribes::where('user_id', auth()->user()->id)
        ->with(['user', 'packageSubscribe'])
        ->get();
        return response()->json([
            'message' => "success",
            'data' => $userSubscribes
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'vps' => ['required', 'string'],
            'package_subscribes_id' => ['required', 'integer'],
        ]);

        if($validated->fails()) {
            return response()->json([
                'message' => 'Error Validation fields',
                'errors' => $validated->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $subscribe = PackageSubscribe::find($request->package_subscribes_id);
        if (empty($subscribe)) {
            return response()->json([
                'message' => "Data Not found",
                'errors' => null
            ], Response::HTTP_BAD_REQUEST);
        }

        $accountDeposito = Account::where('user_id', auth()->user()->id)->first();
        if ($accountDeposito->balance < $subscribe->monthly_rate) {
            return response()->json([
                'message' => "Saldo anda tidak mencukupi",
                'errors' => null
            ], Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $userSubscribes = UserSubscribes::create([
                'user_id' => auth()->user()->id,
                'package_subscribes_id' => $request->package_subscribes_id,
                'vps' => $request->vps,
                'expired_at' => now()->addMonth(1)->format('Y-m-d H:i:s'),
            ]);

            Billing::create([
                'account_id' => $accountDeposito->id,
                'user_subscribes_id' => $userSubscribes->id,
            ]);

            $accountDeposito->balance -= $subscribe->monthly_rate;
            $accountDeposito->save();

            DB::commit();

            return response()->json([
                'message' => 'success',
                'data' => $userSubscribes
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'package_subscribe_id' => ['required', 'integer'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Error Validation fields',
                'errors' => $validated->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $userSubscribes = UserSubscribes::where('user_id', auth()->user()->id)
            ->where('package_subscribe_id', $request->package_subscribe_id)
            ->where('is_active', 1)
            ->first();

        return response()->json([
            'message' => 'success',
            'data' => $userSubscribes
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserSubscribes $userSubscribes)
    {
        //
    }
}
