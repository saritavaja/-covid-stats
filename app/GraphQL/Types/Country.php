<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class Country extends GraphQLType
{
    protected $attributes = [
        'name' => 'Country',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the country'
            ],
            'code' => [
                'type' => Type::string(),
                'description' => 'The iso code of the country'
            ],
        ];
    }
}
