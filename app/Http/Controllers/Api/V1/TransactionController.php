<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(GetTransactionRequest $request)
    {
        $transactions = Transaction::with(['customer', 'items.product'])
            ->search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($transactions, TransactionResource::class),
            'Transaction List'
        );
    }

    // store
    public function store(StoreTransactionRequest $request)
    {
        try {
            // mulai transaksi database, jika ada error (stok habis atau koneksi terputus) semua query dibatalkan
            DB::beginTransaction();

            $subtotal = 0;
            $itemsData = [];

            // Iterasi untuk validasi barang dan hitung subtotal
            foreach ($request->items as $item) {
                // Cari data product berdasarkan ID
                $product = Product::find($item['product_id']);

                // Jika product tidak ditemukan maka throw exception (rollback transaksi)
                if (!$product) {
                    throw new \Exception("Product with ID {$item['product_id']} not found");
                }

                // Jika stok product tidak cukup maka throw exception (rollback transaksi)
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product {$product->name}");
                }

                $itemSubtotal = $product->price * $item['quantity']; // Hitung harga total per item (harga * jumlah)
                $subtotal += $itemSubtotal; // Akumulasi ke subtotal keseluruhan

                // simpan sementara di array memory (belum masuk ke DB)
                $itemsData[] = [
                    'product_id'    => $product->id,
                    'price'         => $product->price,
                    'quantity'      => $item['quantity'],
                    'subtotal'      => $itemSubtotal,
                    'model'         => $product // Menyimpan instance model untuk update stok nanti
                ];
            }

            // Hitung pajak (11%) dan Total keseluruhan
            $tax = $subtotal * 0.11;
            $total = $subtotal + $tax;

            // Generate Unique Transaction Code (TRX-TIMESTAMP-RANDOM)
            $code = 'TRX-' . time() . '-' . rand(1000, 9999);

            // Simpan data induk/header transaksi ke tabel 'transactions'
            $transaction = Transaction::create([
                'code'          => $code,
                'customer_id'   => $request->customer_id,
                'subtotal'      => $subtotal,
                'tax'           => $tax,
                'total'         => $total,
            ]);

            // Simpan detail rincian barang dan potong stok produk
            foreach ($itemsData as $data) {
                // Simpan rincian barang ke table 'transaction_items'
                TransactionItem::create([
                    'transaction_id'    => $transaction->id,
                    'product_id'        => $data['product_id'],
                    'price'             => $data['price'],
                    'quantity'          => $data['quantity'],
                    'subtotal'          => $data['subtotal'],
                ]);

                // Potong stok di tabel 'products' secara atomis
                $data['model']->decrement('stock', $data['quantity']);
            }

            // Jika seluruh proses di atas sukses, simpan permanen ke database
            DB::commit();

            // Response success
            return ApiResponse::success(
                new TransactionResource($transaction->load(['customer', 'items.product'])),
                'Transaction created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            // Jika ada satu saja step yang throw error, batalkan semua perubahan DB
            DB::rollBack();

            // Response error
            return ApiResponse::error(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    // show
    public function show(string $id)
    {
        $transaction = Transaction::with(['customer', 'items.product'])->find($id);

        if (!$transaction) {
            return ApiResponse::error(
                'Transaction Not Found',
                Response::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            new TransactionResource($transaction),
            'Transaction Details'
        );
    }
}
