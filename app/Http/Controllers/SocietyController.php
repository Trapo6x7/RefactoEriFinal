<?php

namespace App\Http\Controllers;

use App\Models\Society;

class SocietyController extends Controller
{
    public function problemes($id)
    {
        $societe = Society::with('problems')->find($id);
     
        return response()->json($societe ? $societe->problems : []);
    }
}
