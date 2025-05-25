<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\UserLists\AddItemsToUserList;
use AnimeSite\Actions\UserLists\CreateUserList;
use AnimeSite\Actions\UserLists\DeleteUserList;
use AnimeSite\Actions\UserLists\GetAllUserLists;
use AnimeSite\Actions\UserLists\GetUserLists;
use AnimeSite\Actions\UserLists\GetUserListsByType;
use AnimeSite\Actions\UserLists\RemoveItemsFromUserList;
use AnimeSite\Actions\UserLists\ShowUserList;
use AnimeSite\Actions\UserLists\UpdateUserList;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\AddItemsToUserListRequest;
use AnimeSite\Http\Requests\StoreUserListRequest;
use AnimeSite\Http\Requests\UpdateUserListRequest;
use AnimeSite\Http\Resources\UserListResource;
use AnimeSite\Models\User;
use AnimeSite\Models\UserList;

class UserListController extends Controller
{
    /**
     * Отримати список списків користувачів.
     *
     * @param Request $request
     * @param GetAllUserLists $action
     * @return JsonResponse
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
     * Створити новий список користувача.
     *
     * @param StoreUserListRequest $request
     * @param CreateUserList $action
     * @return JsonResponse
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
     * Отримати інформацію про конкретний список користувача.
     *
     * @param UserList $userList
     * @param ShowUserList $action
     * @return JsonResponse
     */
    public function show(UserList $userList, ShowUserList $action): JsonResponse
    {
        $userList = $action($userList);

        return response()->json(new UserListResource($userList));
    }

    /**
     * Оновити список користувача.
     *
     * @param UpdateUserListRequest $request
     * @param UserList $userList
     * @param UpdateUserList $action
     * @return JsonResponse
     */
    public function update(UpdateUserListRequest $request, UserList $userList, UpdateUserList $action): JsonResponse
    {
        $userList = $action($userList, $request->validated());

        return response()->json(new UserListResource($userList));
    }

    /**
     * Видалити список користувача.
     *
     * @param UserList $userList
     * @param DeleteUserList $action
     * @return JsonResponse
     */
    public function destroy(UserList $userList, DeleteUserList $action): JsonResponse
    {
        $action($userList);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Отримати списки користувача за типом.
     *
     * @param string $type
     * @param Request $request
     * @param GetUserListsByType $action
     * @return JsonResponse
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

    /**
     * Отримати списки конкретного користувача.
     *
     * @param User $user
     * @param Request $request
     * @param GetUserLists $action
     * @return JsonResponse
     */
    public function userLists(User $user, Request $request, GetUserLists $action): JsonResponse
    {
        $paginated = $action($user, $request);

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
     * Масово додати об'єкти до списку користувача.
     *
     * @param User $user
     * @param AddItemsToUserListRequest $request
     * @param AddItemsToUserList $action
     * @return JsonResponse
     */
    public function addItems(User $user, AddItemsToUserListRequest $request, AddItemsToUserList $action): JsonResponse
    {
        $userLists = $action($user, $request->validated());

        return response()->json([
            'data' => array_map(fn($list) => new UserListResource($list), $userLists),
            'message' => 'Об\'єкти успішно додані до списку',
        ]);
    }

    /**
     * Масово видалити об'єкти зі списку користувача.
     *
     * @param User $user
     * @param AddItemsToUserListRequest $request
     * @param RemoveItemsFromUserList $action
     * @return JsonResponse
     */
    public function removeItems(User $user, AddItemsToUserListRequest $request, RemoveItemsFromUserList $action): JsonResponse
    {
        $count = $action($user, $request->validated());

        return response()->json([
            'message' => "Успішно видалено {$count} об\'єктів зі списку",
        ]);
    }
}
