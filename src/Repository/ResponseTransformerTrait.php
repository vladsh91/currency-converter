<?php

declare(strict_types=1);

namespace App\Repository;

use CuyZ\Valinor\MapperBuilder;

trait ResponseTransformerTrait
{
    /**
     * @template T
     *
     * @param class-string<T> $className
     *
     * @return T[]
     */
    private function transformIntoDtoAll(mixed $data, string $className): mixed
    {
        $results = (new MapperBuilder())
            ->allowSuperfluousKeys()
            ->mapper()
            ->map(\sprintf('array<%s>', $className), $data);

        return \array_values($results);
    }
}
