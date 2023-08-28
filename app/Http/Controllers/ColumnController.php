<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{

    public function index()
    {
        $columns = Column::all();
        return response()->json($columns);
    }

    public function store(Request $request)
    {
        $column = Column::create([
            'title' => $request->input('title'),
        ]);

        return response()->json(['message' => 'Column created successfully']);
    }


}
