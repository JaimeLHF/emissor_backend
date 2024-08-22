<?php

namespace App\Http\Controllers;

use App\Models\LedStatus;
use Illuminate\Http\Request;

class LedStatusController extends Controller
{
    public function index()
    {
        $ledStatuses = LedStatus::all();
        return response()->json($ledStatuses);
    }

    public function show($id)
    {
        $ledStatus = LedStatus::findOrFail($id);
        return response()->json($ledStatus);
    }

    public function store(Request $request)
    {
        $request->validate([
            'led1' => 'required|integer',
            'led2' => 'required|integer',
            'led3' => 'required|integer',
            'led4' => 'required|integer',
        ]);

        $ledStatus = LedStatus::create($request->all());
        return response()->json($ledStatus, 201);
    }

    public function update(Request $request, $id)
    {
        $ledStatus = LedStatus::findOrFail($id);
        $ledStatus->update($request->all());
        return response()->json($ledStatus);
    }

    public function destroy($id)
    {
        $ledStatus = LedStatus::findOrFail($id);
        $ledStatus->delete();
        return response()->json(null, 204);
    }
}
