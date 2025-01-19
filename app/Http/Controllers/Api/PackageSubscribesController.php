<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackageSubscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PackageSubscribesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $subscribes = PackageSubscribe::all();

        return response()->json([
            'message' => "success",
            'data' => $subscribes
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $subscribe = PackageSubscribe::find($id);
        return response()->json([
            'message' => "success",
            'data' => $subscribe
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'cpu' => ['required', 'integer'],
            'ram' => ['required', 'integer'],
            'disk' => ['required', 'integer'],
            'monthly_rate' => ['required', 'decimal:2'],
        ]);

        if ($validated->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $subscribe = PackageSubscribe::create($request->all());

        return response()->json([
            'data' => $subscribe
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = Validator::make($request->all(), [
            'cpu' => ['required', 'integer'],
            'ram' => ['required', 'integer'],
            'disk' => ['required', 'integer'],
            'monthly_rate' => ['required', 'decimal:2'],
        ]);

        if ($validated->fails()) {
            return response([
                'message' => 'Error Validation fields',
                'errors' => $validated->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $subscribe = PackageSubscribe::find($id);
        if (empty($subscribe)) {
            return response()->json([
                'message' => "Data Not found",
                'errors' => null
            ], Response::HTTP_BAD_REQUEST);
        }

        $subscribe->update($request->all());

        return response()->json([
            'message' => "success",
            'data' => $subscribe
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscribe = PackageSubscribe::find($id);
        $subscribe->delete();

        return response()->json([
            'message' => "success",
            'data' => 'deleted'
        ], Response::HTTP_OK);
    }
}
