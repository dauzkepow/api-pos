<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadProductImageRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    // string $id karena akan ambil id dari product
    public function store(UploadProductImageRequest $request, string $id)
    {
        // cari data berdasarkan $id
        $product = Product::find($id);

        // jika product tidak ada
        if(!$product) {
            return ApiResponse::error(
                'Product Not Found!',
                Response::HTTP_NOT_FOUND
            );
        }

        // jika product image ada, ketika update image hapus dulu yang image lama
        if($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $path = $request->file('image')->store('products', 'public');
        $product->update(['image' => $path]);

        return ApiResponse::success(
            new ProductResource($product->load('category')),
            'Product Image Uploaded.'
        );
    }
}
