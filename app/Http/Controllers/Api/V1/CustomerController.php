<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    // munculkan customer + pagination
    public function index(GetCustomerRequest $request)
    {
        $customers = Customer::search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($customers, CustomerResource::class),
            'Customer List'
        );
    }

    // Get Customer Options, tampilkan customer tanpa pagination
    public function options(GetCustomerRequest $request)
    {
        // hanya id dan name
        $customers = Customer::select('id', 'name', 'phone')
            ->search($request->search)
            ->orderBy('name')
            ->get();

        return ApiResponse::success(
            CustomerResource::collection($customers),
            'Customer Options List'
        );
    }

    // Store Customer, user bisa input data
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return ApiResponse::success(
            new CustomerResource($customer),
            'Customer Created Successfuly',
            Response::HTTP_CREATED
        );
    }

    // Get Customer by ID
    public function show(string $id)
    {
        $customer = Customer::find($id);

        // jika customer tidak ada
        if (!$customer) {
            return ApiResponse::error(
                'Customer Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        // jika customer ada munculkan success
        return ApiResponse::success(
            new CustomerResource($customer),
            'Customer Details'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $customer = Customer::find($id);

        // jika customer tidak ada
        if (!$customer) {
            return ApiResponse::error(
                'Customer Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        $customer->update($request->validated());

        // jika ada munculkan success
        return ApiResponse::success(
            new CustomerResource($customer),
            'Customer Update Successfuly'
        );
    }

    // Delete Customer by ID
    public function destroy(string $id)
    {
        $customer = Customer::find($id);

        // jika customer tidak ada
        if (!$customer) {
            return ApiResponse::error(
                'Customer Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        $customer->delete();

        return ApiResponse::success(
            null,
            'Customer Delete Successfuly'
        );
    }
}
