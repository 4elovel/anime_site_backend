<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\UserLists\CreateUserList;
use AnimeSite\Actions\UserLists\DeleteUserList;
use AnimeSite\Actions\UserLists\GetAllUserLists;
use AnimeSite\Actions\UserLists\GetUserListsByType;
use AnimeSite\Actions\UserLists\ShowUserList;
use AnimeSite\Actions\UserLists\UpdateUserList;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreUserListRequest;
use AnimeSite\Http\Requests\UpdateUserListRequest;
use AnimeSite\Http\Resources\UserListResource;
use AnimeSite\Models\UserList;

class UserListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllUserLists $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => UserListResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserListRequest $request, CreateUserList $action): JsonResponse
    {
        $userList = $action($request->validated());

        return response()->json(
            new UserListResource($userList),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(UserList $userList, ShowUserList $action): JsonResponse
    {
        $userList = $action($userList);

        return response()->json(new UserListResource($userList));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserListRequest $request, UserList $userList, UpdateUserList $action): JsonResponse
    {
        $userList = $action($userList, $request->validated());

        return response()->json(new UserListResource($userList));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserList $userList, DeleteUserList $action): JsonResponse
    {
        $action($userList);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get user lists by type.
     */
    public function byType(string $type, Request $request, GetUserListsByType $action): JsonResponse
    {
        $paginated = $action($type, $request);

        return response()->json([
            'data' => UserListResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
}
