<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetServiceTypeRequest;
use App\Http\Requests\StoreServiceTypeRequest;
use App\Http\Requests\UpdateServiceTypeRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ServiceTypeResource;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetServiceTypeRequest $request)
    {
        $types = ServiceType::search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($types, ServiceTypeResource::class),
            'Service type list'
        );
    }

    public function options(GetServiceTypeRequest $request)
    {
        $types = ServiceType::select('id', 'name')
            ->search($request->search)
            ->orderBy('name')
            ->get();

        return ApiResponse::success(
            ServiceTypeResource::collection($types),
            'Service types list'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceTypeRequest $request)
    {
        $types = ServiceType::create($request->validated());
        return ApiResponse::success(
            new ServiceTypeResource($types),
            'Service type created successfull',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $types = ServiceType::find($id);

        if (!$types) {
            return ApiResponse::error(
                'Service type not found',
                Response::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            new ServiceTypeResource($types),
            'Service type details'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceTypeRequest $request, string $id)
    {
        $types = ServiceType::find($id);

        if (!$types) {
            return ApiResponse::error(
                'Service type not found',
                Response::HTTP_NOT_FOUND
            );
        }

        $types->update($request->validated());
        return ApiResponse::success(
            new ServiceTypeResource($types),
            'Service type updates successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $types = ServiceType::find($id);

        if (!$types) {
            return ApiResponse::Error(
                'Service type not found',
                Response::HTTP_NOT_FOUND
            );
        }

        if ($types->image) {
            Storage::disk('public')->delete($types->image);
        }

        $types->delete();
        return ApiResponse::success(
            null,
            'Service types deleted successfully'
        );
    }
}
