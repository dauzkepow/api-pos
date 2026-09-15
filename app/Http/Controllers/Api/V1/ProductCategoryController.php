<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetProductCategoryRequest;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    // munculkan product category + pagination
    public function index(GetProductCategoryRequest $request)
    {
        $categories = ProductCategory::search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($categories, ProductCategoryResource::class),
            'Product Categories List'
        );
    }

    // Get Category Options, tampilkan product category tanpa pagination
    public function options(GetProductCategoryRequest $request)
    {
        // hanya id dan name
        $categories = ProductCategory::select('id', 'name')
            ->search($request->search)
            ->orderBy('name')
            ->get();

        return ApiResponse::success(
            ProductCategoryResource::collection($categories),
            'Product Category List'
        );
    }


    // Store Category, user bisa input data
    public function store(StoreProductCategoryRequest $request)
    {
        $category = ProductCategory::create($request->validated());

        return ApiResponse::success(
            new ProductCategoryResource($category),
            'Product Category Created Successfuly',
            Response::HTTP_CREATED
        );
    }

    // Get Category by ID
    public function show(string $id)
    {
        $category = ProductCategory::find($id);

        // jika category tidak ada
        if(!$category) {
            return ApiResponse::error(
                'Product Category Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        // jika category ada muncullkan success
        return ApiResponse::success(
            new ProductCategoryResource($category),
            'Product Category Details'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, string $id)
    {
        $category = ProductCategory::find($id);

        // jika category tidak ada
        if(!$category) {
            return ApiResponse::error(
                'Product Category Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        $category->update($request->validated());

        // jika ada munculkan success
        return ApiResponse::success(
            new ProductCategoryResource($category),
            'Product Category Update Successfuly'
        );


    }

    // Delete Category by ID
    public function destroy(string $id)
    {
        $category = ProductCategory::find($id);

        // jika category tidak ada
        if(!$category) {
            return ApiResponse::error(
                'Product Category Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        // jika category punya gambar, maka hapus image
        if($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return ApiResponse::success(
            null,
            'Product Category Delete Successfuly'
        );
    }
}
