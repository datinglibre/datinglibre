<?php

declare(strict_types=1);

namespace App\Service;

use Iterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

/**
 * Taken from https://github.com/ramsey/uuid/issues/163
 * @see https://symfony.com/doc/current/controller/argument_value_resolver.html
 */
final class UuidValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_a($argument->getType(), Uuid::class, true);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Iterator
    {
        $argumentValue = $request->get($argument->getName());
        if (! is_string($argumentValue)) {
            yield null;
        }

        yield Uuid::fromString($argumentValue);
    }
}
