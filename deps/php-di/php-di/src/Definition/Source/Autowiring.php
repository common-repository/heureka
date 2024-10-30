<?php

declare (strict_types=1);
namespace HeurekaDeps\DI\Definition\Source;

use HeurekaDeps\DI\Definition\Exception\InvalidDefinition;
use HeurekaDeps\DI\Definition\ObjectDefinition;
/**
 * Source of definitions for entries of the container.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Autowiring
{
    /**
     * Autowire the given definition.
     *
     * @throws InvalidDefinition An invalid definition was found.
     * @return ObjectDefinition|null
     */
    public function autowire(string $name, ObjectDefinition $definition = null);
}
