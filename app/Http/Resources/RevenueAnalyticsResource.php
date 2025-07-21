<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RevenueAnalyticsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'total_profits' => $this->total_profits,
            'last_transaction_amount' => $this->last_transaction_amount,
            'debit' => $this->debit,
            'period' => [
                'start_date' => $this->start_date,
                'end_date' => $this->end_date
            ],
            'transactions' => TransactionResource::collection($this->transactions)
        ];
    }
}
