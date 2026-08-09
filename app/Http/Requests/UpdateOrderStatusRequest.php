<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:' . implode(',', Order::STATUSES)],
            'payment_status' => ['required', 'string', 'in:' . implode(',', Order::PAYMENT_STATUSES)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $order = $this->route('order');

            if ($order === null) {
                return;
            }

            $status = $this->input('status');
            $paymentStatus = $this->input('payment_status');

            if ($status !== null && ! $order->canTransitionTo($status)) {
                $validator->errors()->add('status', 'The selected order status transition is not allowed.');
            }

            if ($paymentStatus !== null && ! in_array($paymentStatus, Order::PAYMENT_STATUSES, true)) {
                $validator->errors()->add('payment_status', 'The selected payment status is invalid.');
            }
        });
    }
}
