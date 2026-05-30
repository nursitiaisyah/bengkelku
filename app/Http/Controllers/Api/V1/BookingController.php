<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetBookingRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\PaginatedResource;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetBookingRequest $request)
    {
        $bookings = Booking::with(['client', 'details.booking'])
            ->search($request->search)
            ->when($request->client_id, function ($query, $clientId) {
                $query->where('client_id', $clientId);
            })
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($bookings, BookingResource::class),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        try {
            $bookings = DB::transaction(function () use ($request) {
                $subtotal = 0;
                $itemsData = [];

                foreach ($request->items as $item) {
                    $service = Service::lockForUpdate()->find($item['service_id']);

                    if ($service->stock < $item['quantity']) {
                        throw new \Exception(
                            "Insufficient stock for service '{$service->name}'. Available: {$service->stock}, Requested: {$item['quantity']}"
                        );
                    }

                    $itemSubtotal = $service->price * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $itemsData[] = [
                        'service_id'    => $service->id,
                        'price'         => $service->price,
                        'quantity'      => $item['quantity'],
                        'subtotal'      => $itemSubtotal,
                    ];

                    $service->decrement('stock', $item['quantity']);
                }

                $tax = $subtotal * 0.11;
                $total = $subtotal + $tax;

                $bookings = Booking::create([
                    'code'      => 'TRX-' . now()->format('YmdHis') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                    'client_id' => $request->client_id,
                    'subtotal'  => $subtotal,
                    'tax'       => $tax,
                    'total'     => $total,
                ]);

                $bookings->details()->createMany($itemsData);

                return $bookings->load(['client', 'details.service']);
            });

            return ApiResponse::success(
                new BookingResource($bookings),
                'Booking created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bookings = Booking::with(['client', 'details.service'])->find($id);

        if (!$bookings) {
            return ApiResponse::error(
                'Bookings not found',
                Response::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            new BookingResource($bookings),
            'Booking details'
        );
    }
}
