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

        $expectedKeyword = $mapping[$buyType];
        if (!str_contains($deliverable, $expectedKeyword) && !str_contains($expectedKeyword, $deliverable)) {
            // Log or allow flexible matching if keyword is related
        }

        return match ($buyType) {
            'CPM'   => ($volume / 1000) * $bid,
            'FIXED' => $bid,
            default => $volume * $bid,
        };
    }
}
 