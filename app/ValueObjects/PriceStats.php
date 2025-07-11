<?php

namespace App\ValueObjects;

class PriceStats
{
    public readonly array $prices;
    public readonly int $count;
    public readonly float $min;
    public readonly float $max;
    public readonly float $amplitude;
    public readonly float $average;
    public readonly float $average_without_extremes;
    public readonly string $formatted_average;
    public readonly string $formatted_min;
    public readonly string $formatted_max;
    public readonly string $formatted_amplitude;

    public function __construct(array $prices)
    {
        $this->prices = $prices;
        $this->count = count($prices);
        
        if (empty($prices)) {
            $this->min = 0.0;
            $this->max = 0.0;
            $this->amplitude = 0.0;
            $this->average = 0.0;
            $this->average_without_extremes = 0.0;
            $this->formatted_average = '';
            $this->formatted_min = '';
            $this->formatted_max = '';
            $this->formatted_amplitude = '';
            return;
        }

        // Trier les prix
        sort($prices);
        
        $this->min = $prices[0];
        $this->max = $prices[count($prices) - 1];
        $this->amplitude = $this->max - $this->min;
        
        // Calculer la moyenne simple
        $this->average = array_sum($prices) / count($prices);
        
        // Calculer la moyenne sans les extrêmes si on a au moins 3 prix
        if (count($prices) >= 3) {
            $filteredPrices = array_slice($prices, 1, -1);
            $this->average_without_extremes = array_sum($filteredPrices) / count($filteredPrices);
        } else {
            $this->average_without_extremes = $this->average;
        }
        
        // Formater les valeurs
        $this->formatted_average = number_format($this->average_without_extremes, 2, ',', ' ');
        $this->formatted_min = number_format($this->min, 2, ',', ' ');
        $this->formatted_max = number_format($this->max, 2, ',', ' ');
        $this->formatted_amplitude = number_format($this->amplitude, 2, ',', ' ');
    }

    public static function fromArray(array $data): self
    {
        return new self($data['prices'] ?? []);
    }

    public function toArray(): array
    {
        return [
            'prices' => $this->prices,
            'count' => $this->count,
            'min' => $this->min,
            'max' => $this->max,
            'amplitude' => $this->amplitude,
            'average' => $this->average,
            'average_without_extremes' => $this->average_without_extremes,
            'formatted_average' => $this->formatted_average,
            'formatted_min' => $this->formatted_min,
            'formatted_max' => $this->formatted_max,
            'formatted_amplitude' => $this->formatted_amplitude,
        ];
    }

    public function isEmpty(): bool
    {
        return $this->count === 0 || $this->min === 0.0;
    }
} 