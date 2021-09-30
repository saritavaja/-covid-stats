<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\DataProvider\DataProviderManager;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Query;

class CountriesListQuery extends Query
{
    protected $attributes = [
        'name' => 'CountriesList',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return Type::listOf(\GraphQL::type('CountryData'));
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $countriesTotal = app(DataProviderManager::class)->cache(now()->addHour())->countriesTotal();

        return collect($countriesTotal)->sortByDesc(function ($item) {
            return (int) data_get($item, 'stats.total_confirmed');
        });
    }
}
