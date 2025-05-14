<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Payments\CheckPaymentStatus;
use AnimeSite\Actions\Payments\CreatePayment;
use AnimeSite\Actions\Payments\GetAllPayments;
use AnimeSite\Actions\Payments\ProcessPaymentCallback;
use AnimeSite\Actions\Payments\ShowPayment;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\ProcessPaymentCallbackRequest;
use AnimeSite\Http\Requests\StorePaymentRequest;
use AnimeSite\Http\Resources\PaymentResource;
use AnimeSite\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllPayments $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => PaymentResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage (create payment intent).
     */
    public function store(StorePaymentRequest $request, CreatePayment $action): JsonResponse
    {
        $payment = $action($request->validated());
        
        return response()->json(
            new PaymentResource($payment),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment, ShowPayment $action): JsonResponse
    {
        $payment = $action($payment);
        
        return response()->json(new PaymentResource($payment));
    }

    /**
     * Process payment callback from LiqPay.
     */
    public function callback(ProcessPaymentCallbackRequest $request, ProcessPaymentCallback $action): JsonResponse
    {
        $payment = $action($request->validated());
        
        return response()->json(new PaymentResource($payment));
    }
    
    /**
     * Check payment status by transaction ID.
     */
    public function checkStatus(string $transactionId, CheckPaymentStatus $action): JsonResponse
    {
        $payment = $action($transactionId);
        
        return response()->json(new PaymentResource($payment));
    }
}
