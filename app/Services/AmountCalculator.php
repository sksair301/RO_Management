<?php

namespace App\Services;

use InvalidArgumentException;

class AmountCalculator
{
    public function calculate(
        string $buyType,
        string $deliverable,
        float $volume,
        float $bid
    ): float {

        $buyType = strtoupper(trim($buyType));
        $deliverable = strtolower(trim($deliverable));

        $mapping = [
            'CPC'   => 'clicks',
            'CPM'   => 'impressions',
            'CPV'   => 'views',
            'CPE'   => 'engagements',
            'CPL'   => 'leads',
            'FIXED' => 'fixed',
        ];

        if (!isset($mapping[$buyType])) {
            throw new InvalidArgumentException('Invalid buy type.');
        }

        if ($mapping[$buyType] !== $deliverable) {
            throw new InvalidArgumentException(
                "Deliverable '{$deliverable}' is not valid for Buy Type '{$buyType}'."
            );
        }

        return match ($buyType) {
            'CPM'   => ($volume / 1000) * $bid,
            'FIXED' => $bid,
            default => $volume * $bid,
        };
    }
}
 