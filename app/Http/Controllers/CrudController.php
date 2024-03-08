<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrudController extends Controller
{
    function index()
    {
        // echo "Hello World";
        return view('crud.index');
    }
}
