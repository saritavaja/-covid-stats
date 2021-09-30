<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class Stats extends GraphQLType
{
    protected $attributes = [
        'name' => 'Stats',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'new_confirmed' => [
                'type' => Type::int(),
                'description' => 'The new_confirmed'
            ],
            'total_confirmed' => [
                'type' => Type::int(),
                'description' => 'The total_confirmed'
            ],
            'new_deaths' => [
                'type' => Type::int(),
                'description' => 'The new_deaths'
            ],
            'total_deaths' => [
                'type' => Type::int(),
                'description' => 'The total_deaths'
            ],
            'new_recovered' => [
                'type' => Type::int(),
                'description' => 'The new_recovered'
            ],
            'total_recovered' => [
                'type' => Type::int(),
                'description' => 'The total_recovered'
            ],
        ];
    }
}
