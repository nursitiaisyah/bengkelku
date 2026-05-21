<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadServiceImageRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ServiceImageController extends Controller
{
    public function store(UploadServiceImageRequest $request, string $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return ApiResponse::error(
                'Service not found',
                Response::HTTP_NOT_FOUND
            );
        }

        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $path = $request->file('image')->store('services', 'public');
        $service->update(['image' => $path]);

        return ApiResponse::success(
            new ServiceResource($service->load('service')),
            'Service image uploaded'
        );
    }
}
