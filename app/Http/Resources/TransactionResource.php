<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'customer' => $this->customer,
            'date' => $this->date,
            'type' => $this->type,
            'amount' => $this->amount,
            'courses' => $this->courses
        ];
    }
}
