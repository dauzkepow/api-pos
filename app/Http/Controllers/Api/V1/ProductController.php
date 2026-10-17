<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // munculkan product + pagination + hasil filter product category
    public function index(GetProductRequest $request)
    {
        $products = Product::with('category')
            ->search($request->search)
            // Filter menggunakan parameter product_category_id
            ->byCategory($request->input('product_category_id')) // Gunakan scopeByCategory di Product.php
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($products, ProductResource::class),
            'Product List'
        );
    }

    // Get Product Options, tampilkan product tanpa pagination
    public function options(GetProductRequest $request)
    {
        // option pilih ke search
        $products = Product::search($request->search)
            ->byCategory($request->input('product_category_id'))
            ->orderBy('name')
            ->get();

        return ApiResponse::success(
            ProductResource::collection($products),
            'Product Options List'
        );
    }

    // Store Product, user bisa input data
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return ApiResponse::success(
            new ProductResource($product->load('category')),
            'Product Created Successfuly',
            Response::HTTP_CREATED
        );
    }

    // Get Product by ID
    public function show(string $id)
    {
        $product = Product::with('category')->find($id);

        // jika product tidak ada
        if(!$product) {
            return ApiResponse::error(
                'Product Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        // jika product ada munculkan success
        return ApiResponse::success(
            new ProductResource($product),
            'Product Details'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::find($id);

        // jika product tidak ada
        if(!$product) {
            return ApiResponse::error(
                'Product Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        $product->update($request->validated());

        // jika ada munculkan success
        return ApiResponse::success(
            new ProductResource($product->load('category')),
            'Product Update Successfuly'
        );
    }

    // Delete Product by ID
    public function destroy(string $id)
    {
        $product = Product::find($id);

        // jika product tidak ada
        if(!$product) {
            return ApiResponse::error(
                'Product Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        // jika product punya gambar, maka hapus image
        if($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return ApiResponse::success(
            null,
            'Product Delete Successfuly'
        );
    }
}
