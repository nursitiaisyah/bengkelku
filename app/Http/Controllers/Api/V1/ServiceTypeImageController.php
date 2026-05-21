<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadServiceTypeImageRequest;
use App\Http\Resources\ServiceTypeResource;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ServiceTypeImageController extends Controller
{
    public function store(UploadServiceTypeImageRequest $request, string $id)
    {
        $types = ServiceType::find($id);
        if (!$types) {
            return ApiResponse::error(
                'Service type not found',
                Response::HTTP_NOT_FOUND
            );
        }
        if ($types->image) {
            Storage::disk('public')->delete($types->image);
        }

        $path = $request->file('image')->store('service_types', 'public');
        $types->update(['image' => $path]);

        return ApiResponse::success(
            new ServiceTypeResource($types),
            'Service type image uploaded'
        );
    }
}
