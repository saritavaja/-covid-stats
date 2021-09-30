<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CountryData extends GraphQLType
{
    protected $attributes = [
        'name' => 'CountryData',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'country' => [
                'type' => \GraphQL::type('Country'),
                'description' => 'The country'
            ],
            'stats' => [
                'type' => \GraphQL::type('Stats'),
                'description' => 'The stats'
            ],
            'date' => [
                'type' => Type::int(),
                'description' => 'The date'
            ],
        ];
    }
}
