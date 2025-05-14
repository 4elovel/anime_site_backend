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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Comment $comment, ShowComment $action): JsonResponse
    {
        $comment = $action($comment);

        return response()->json(new CommentResource($comment));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment, UpdateComment $action): JsonResponse
    {
        $comment = $action($comment, $request->validated());

        return response()->json(new CommentResource($comment));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment, DeleteComment $action): JsonResponse
    {
        $action($comment);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Like a comment.
     */
    public function like(LikeCommentRequest $request, Comment $comment, LikeComment $action): JsonResponse
    {
        $action($comment, $request->validated());

        return response()->json(['message' => 'Comment liked successfully']);
    }

    /**
     * Unlike a comment.
     */
    public function unlike(Comment $comment, UnlikeComment $action): JsonResponse
    {
        $action($comment);

        return response()->json(['message' => 'Comment unliked successfully']);
    }

    /**
     * Report a comment.
     */
    public function report(ReportCommentRequest $request, Comment $comment, ReportComment $action): JsonResponse
    {
        $action($comment, $request->validated());

        return response()->json(['message' => 'Comment reported successfully']);
    }

    /**
     * Get reported comments (for moderators).
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
     * Approve a reported comment (for moderators).
     */
    public function approveComment(Comment $comment, ApproveComment $action): JsonResponse
    {
        $comment = $action($comment);

        return response()->json(new CommentResource($comment));
    }

    /**
     * Reject a reported comment (for moderators).
     */
    public function rejectComment(Comment $comment, RejectComment $action): JsonResponse
    {
        $action($comment);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
