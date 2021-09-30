<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\DataProvider\DataProviderManager;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class StatsQuery extends Query
{
    protected $attributes = [
        'name' => 'stats',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return \GraphQL::type('Stats');
    }

    public function args(): array
    {
        return [
            'country' => [
                'type' => Type::string(),
                'description' => 'The iso code of the country'
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return app(DataProviderManager::class)->cache(now()->addHour())->globalStats(data_get($args, 'country'));
    }
}
