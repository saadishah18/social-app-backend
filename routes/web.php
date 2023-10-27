<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('json-list',function (){
    $queryResult = DB::table('year')
        ->select(['year', 'make.name as make_name', 'model.name as model_name', 'model.name as variation_name'])
        ->join('make', 'make.vehicle_year_id', '=', 'year.id')
        ->join('model', 'model.vehicle_make_id', '=', 'make.id')
        #->join('model as m2', 'm2.vehicle_make_id', '=', 'model.id')
        ->groupBy('year', 'make_name', 'model_name', 'variation_name')
        ->get();

// Process the query result
    $result = [];
    foreach ($queryResult as $row) {
        $year = $row->year;
        $make = $row->make_name;
        $model = $row->model_name;
        $variation = $row->variation_name;

        if (!isset($result[$year])) {
            $result[$year] = [];
        }

        if (!isset($result[$year][$make])) {
            $result[$year][$make] = [];
        }

        if (!isset($result[$year][$make][$model])) {
            $result[$year][$make][$model] = [];
        }

        // Add model variations to the array
        $result[$year][$make][$model][] = $variation;
    }

// Convert the result to JSON
    $jsonResult = json_encode($result);
    $headers = [
        'Content-Type' => 'application/json',
        'Content-Disposition' => 'attachment; filename="result.json"',
    ];

// Create the download response
    $response = response()->make($jsonResult, 200, $headers);

// Return the download response
    return $response;
});
