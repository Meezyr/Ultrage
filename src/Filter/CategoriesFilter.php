<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class CategoriesFilter implements FilterInterface
{

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $request = $context['request'] ?? null;
        if (!$request) {
            return;
        }

        $categories = $request->get('categories');
        if (!$categories) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $paramName = $queryNameGenerator->generateParameterName('categories');

        $queryBuilder
            ->andWhere("JSON_CONTAINS($alias.categories, :$paramName) = 1")
            ->setParameter($paramName, json_encode($categories));
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'categories' => [
                'property' => 'categories',
                'type' => 'string',
                'required' => false,
                'description' => 'Filter links by category name stored in JSON array',
            ],
        ];
    }
}
