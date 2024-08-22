<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LedStatusController extends Controller
{
    public function index()
    {

    }

    public function show($id)
    {
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
<<<<<<< HEAD
        $ledStatus = LedStatus::findOrFail($id);
        $ledStatus->update($request->all());
        return response()->json($ledStatus);
=======
>>>>>>> bfecfb47899702aa99055d28056ab73bb3799c5f
    }

    public function destroy($id)
    {
    }
}
