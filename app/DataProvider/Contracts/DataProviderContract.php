<?php


namespace App\DataProvider\Contracts;


use Illuminate\Support\Collection;

interface DataProviderContract
{
    public function countriesTotal(): array;

    public function globalStats($country): array;
}
