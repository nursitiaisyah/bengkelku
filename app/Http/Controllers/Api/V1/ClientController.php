<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetClientRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\PaginatedResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetClientRequest $request)
    {
        $clients = Client::search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);
        return ApiResponse::success(
            new PaginatedResource($clients, ClientResource::class),
            'Client list'
        );
    }

    public function options(GetClientRequest $request)
    {
        $clients = Client::select('id', 'name')
            ->search($request->search)
            ->orderBy('name')
            ->get();

        return ApiResponse::success(
            ClientResource::collection($clients),
            'Client list'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $clients = Client::create($request->validated());
        return ApiResponse::success(
            new ClientResource($clients),
            'Client ceated successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $clients = Client::find($id);

        if (!$clients) {
            return ApiResponse::error(
                'Client not found',
                Response::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            new ClientResource($clients),
            'Client details'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, string $id)
    {
        $clients = Client::find($id);

        if (!$clients) {
            return ApiResponse::error(
                'Client not found',
                Response::HTTP_NOT_FOUND
            );
        }

        $clients->update($request->validated());
        return ApiResponse::success(
            new ClientResource($clients),
            'Client updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $clients = Client::find($id);
        if (!$clients) {
            return ApiResponse::error(
                'Client not found',
                Response::HTTP_NOT_FOUND
            );
        }

        $clients->delete();
        return ApiResponse::success(
            null,
            'Client deleted successfully'
        );
    }
}
