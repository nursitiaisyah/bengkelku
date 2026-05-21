<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetServiceRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetServiceRequest $request)
    {
        $services = Service::with('service')
            ->search($request->search)
            ->when($request->service_type_id, function ($query, $typeId) {
                $query->where('service_type_id', $typeId);
            })
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($services, ServiceResource::class),
            'Services list'
        );
    }

    public function options(GetServiceRequest $request)
    {
        $services = Service::select('id', 'name')
            ->search($request->search)
            ->orderBy('name')
            ->get();

        return ApiResponse::success(
            ServiceResource::collection($services),
            'Services list'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $services = Service::create($request->validated());
        return ApiResponse::success(
            new ServiceResource($services->load('service')),
            'Service created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) //ini dibuat setelah UploadServiceImageRequest dan sudah buat Upload Service Image di Postman
    {
        $services = Service::with('service')->find($id);

        if (!$services) {
            return ApiResponse::error(
                'Service not found',
                Response::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            new ServiceResource($services),
            'Service details'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, string $id)
    {
        $services = Service::find($id);

        if (!$services) {
            return ApiResponse::error(
                'Service not found',
                Response::HTTP_NOT_FOUND
            );
        }

        $services->update($request->validated());
        return ApiResponse::success(
            new ServiceResource($services->load('service')),
            'SErvice updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $services = Service::find($id);

        if (!$services) {
            return ApiResponse::error(
                'Service not found',
                Response::HTTP_NOT_FOUND
            );
        }

        if ($services->image) {
            Storage::disk('public')->delete($services->image);
        }

        $services->delete();
        return ApiResponse::success(
            null,
            'Service deleted successfully'
        );
    }
}
