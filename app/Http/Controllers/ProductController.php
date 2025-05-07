<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {

        $jsonPath = storage_path('app/products.json');
        $products = File::exists($jsonPath) ? json_decode(File::get($jsonPath), true) : [];


        usort($products, fn($a, $b) => strtotime($b['datetime']) <=> strtotime($a['datetime']));

        $totalSum = array_sum(array_column($products, 'total'));

        return view('products.index', compact('products', 'totalSum'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $entry = [
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'datetime' => now()->toDateTimeString(),
            'total' => $validated['quantity'] * $validated['price'],
        ];


        $jsonPath = storage_path('app/products.json');
        $products = File::exists($jsonPath) ? json_decode(File::get($jsonPath), true) : [];
        $products[] = $entry;
        File::put($jsonPath, json_encode($products, JSON_PRETTY_PRINT));

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
{
    $validated = $request->validate([
        'index' => 'required|integer',
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
    ]);

    $jsonPath = storage_path('app/products.json');
    $products = File::exists($jsonPath) ? json_decode(File::get($jsonPath), true) : [];

    if (!isset($products[$validated['index']])) {
        return response()->json(['error' => 'Invalid index'], 400);
    }

    $products[$validated['index']]['name'] = $validated['name'];
    $products[$validated['index']]['quantity'] = $validated['quantity'];
    $products[$validated['index']]['price'] = $validated['price'];
    $products[$validated['index']]['total'] = $validated['quantity'] * $validated['price'];

    File::put($jsonPath, json_encode($products, JSON_PRETTY_PRINT));

    return response()->json(['success' => true]);
}


}
