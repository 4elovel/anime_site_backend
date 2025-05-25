<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Comments\ApproveComment;
use AnimeSite\Actions\Comments\CreateComment;
use AnimeSite\Actions\Comments\DeleteComment;
use AnimeSite\Actions\Comments\GetAllComments;
use AnimeSite\Actions\Comments\GetReportedComments;
use AnimeSite\Actions\Comments\LikeComment;
use AnimeSite\Actions\Comments\RejectComment;
use AnimeSite\Actions\Comments\ReportComment;
use AnimeSite\Actions\Comments\ShowComment;
use AnimeSite\Actions\Comments\UnlikeComment;
use AnimeSite\Actions\Comments\UpdateComment;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\LikeCommentRequest;
use AnimeSite\Http\Requests\ReportCommentRequest;
use AnimeSite\Http\Requests\StoreCommentRequest;
use AnimeSite\Http\Requests\UpdateCommentRequest;
use AnimeSite\Http\Resources\CommentResource;
use AnimeSite\Models\Comment;

class CommentController extends Controller
{
    /**
     * Отримати список коментарів.
     *
     * @param Request $request
     * @param GetAllComments $action
     * @return JsonResponse
     */
    public function index(Request $request, GetAllComments $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => CommentResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Створити новий коментар.
     *
     * @param StoreCommentRequest $request
     * @param CreateComment $action
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, CreateComment $action): JsonResponse
    {
        $comment = $action($request->validated());

        return response()->json(
            new CommentResource($comment),
            Response::HTTP_CREATED
        );
    }

    /**
     * Отримати конкретний коментар.
     *
     * @param Comment $comment
     * @param ShowComment $action
     * @return JsonResponse
     */
    public function show(Comment $comment, ShowComment $action): JsonResponse
    {
        $comment = $action($comment);

        return response()->json(new CommentResource($comment));
    }

    /**
     * Оновити коментар.
     *
     * @param UpdateCommentRequest $request
     * @param Comment $comment
     * @param UpdateComment $action
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment, UpdateComment $action): JsonResponse
    {
        $comment = $action($comment, $request->validated());

        return response()->json(new CommentResource($comment));
    }

    /**
     * Видалити коментар.
     *
     * @param Comment $comment
     * @param DeleteComment $action
     * @return JsonResponse
     */
    public function destroy(Comment $comment, DeleteComment $action): JsonResponse
    {
        $action($comment);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Поставити лайк або дизлайк коментарю.
     *
     * @param LikeCommentRequest $request
     * @param Comment $comment
     * @param LikeComment $action
     * @return JsonResponse
     */
    public function like(LikeCommentRequest $request, Comment $comment, LikeComment $action): JsonResponse
    {
        $action($comment, $request->validated());

        return response()->json([
            'message' => $request->input('is_liked') ? 'Коментар успішно оцінено позитивно' : 'Коментар успішно оцінено негативно'
        ]);
    }

    /**
     * Скасувати лайк або дизлайк коментаря.
     *
     * @param Comment $comment
     * @param UnlikeComment $action
     * @return JsonResponse
     */
    public function unlike(Comment $comment, UnlikeComment $action): JsonResponse
    {
        $action($comment);

        return response()->json([
            'message' => 'Оцінка коментаря успішно скасована'
        ]);
    }

    /**
     * Поскаржитися на коментар.
     *
     * @param ReportCommentRequest $request
     * @param Comment $comment
     * @param ReportComment $action
     * @return JsonResponse
     */
    public function report(ReportCommentRequest $request, Comment $comment, ReportComment $action): JsonResponse
    {
        $action($comment, $request->validated());

        return response()->json([
            'message' => 'Скарга на коментар успішно відправлена'
        ]);
    }

    /**
     * Отримати коментарі, на які надійшли скарги (для модераторів).
     *
     * @param Request $request
     * @param GetReportedComments $action
     * @return JsonResponse
     */
    public function reportedComments(Request $request, GetReportedComments $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => CommentResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Затвердити коментар після скарги (для модераторів).
     *
     * @param Comment $comment
     * @param ApproveComment $action
     * @return JsonResponse
     */
    public function approveComment(Comment $comment, ApproveComment $action): JsonResponse
    {
        $comment = $action($comment);

        return response()->json(new CommentResource($comment));
    }

    /**
     * Відхилити коментар після скарги (для модераторів).
     *
     * @param Comment $comment
     * @param RejectComment $action
     * @return JsonResponse
     */
    public function rejectComment(Comment $comment, RejectComment $action): JsonResponse
    {
        $action($comment);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
