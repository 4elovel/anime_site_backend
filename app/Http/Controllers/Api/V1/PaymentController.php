<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Payments\CancelPayment;
use AnimeSite\Actions\Payments\CheckPaymentStatus;
use AnimeSite\Actions\Payments\CreatePayment;
use AnimeSite\Actions\Payments\GetAllPayments;
use AnimeSite\Actions\Payments\ProcessPaymentCallback;
use AnimeSite\Actions\Payments\RefundPayment;
use AnimeSite\Actions\Payments\ShowPayment;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\ProcessPaymentCallbackRequest;
use AnimeSite\Http\Requests\StorePaymentRequest;
use AnimeSite\Http\Resources\PaymentResource;
use AnimeSite\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Отримати список платежів.
     *
     * @param Request $request
     * @param GetAllPayments $action
     * @return JsonResponse
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
     * Створити новий платіж (платіжний намір).
     *
     * @param StorePaymentRequest $request
     * @param CreatePayment $action
     * @return JsonResponse
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
     * Отримати інформацію про конкретний платіж.
     *
     * @param Payment $payment
     * @param ShowPayment $action
     * @return JsonResponse
     */
    public function show(Payment $payment, ShowPayment $action): JsonResponse
    {
        $payment = $action($payment);

        return response()->json(new PaymentResource($payment));
    }

    /**
     * Обробити колбек від платіжної системи LiqPay.
     *
     * @param ProcessPaymentCallbackRequest $request
     * @param ProcessPaymentCallback $action
     * @return JsonResponse
     */
    public function callback(ProcessPaymentCallbackRequest $request, ProcessPaymentCallback $action): JsonResponse
    {
        $payment = $action($request->validated());

        return response()->json(new PaymentResource($payment));
    }

    /**
     * Перевірити статус платежу за ID транзакції.
     *
     * @param string $transactionId
     * @param CheckPaymentStatus $action
     * @return JsonResponse
     */
    public function checkStatus(string $transactionId, CheckPaymentStatus $action): JsonResponse
    {
        $payment = $action($transactionId);

        return response()->json(new PaymentResource($payment));
    }

    /**
     * Скасувати платіж.
     *
     * @param Payment $payment
     * @param CancelPayment $action
     * @return JsonResponse
     */
    public function cancel(Payment $payment, CancelPayment $action): JsonResponse
    {
        $payment = $action($payment);

        return response()->json(new PaymentResource($payment));
    }

    /**
     * Повернути кошти за платіж.
     *
     * @param Payment $payment
     * @param Request $request
     * @param RefundPayment $action
     * @return JsonResponse
     */
    public function refund(Payment $payment, Request $request, RefundPayment $action): JsonResponse
    {
        $data = $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'comment' => 'nullable|string|max:255',
        ]);

        $payment = $action($payment, $data);

        return response()->json(new PaymentResource($payment));
    }
}
