<?php

declare (strict_types=1);
namespace HeurekaDeps\DI;

use HeurekaDeps\Psr\Container\ContainerExceptionInterface;
/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
