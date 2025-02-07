<?php

declare (strict_types=1);
namespace HeurekaDeps\PHPStan\PhpDocParser\Ast\Type;

use HeurekaDeps\PHPStan\PhpDocParser\Ast\NodeAttributes;
class IntersectionTypeNode implements TypeNode
{
    use NodeAttributes;
    /** @var TypeNode[] */
    public $types;
    public function __construct(array $types)
    {
        $this->types = $types;
    }
    public function __toString() : string
    {
        return '(' . \implode(' & ', $this->types) . ')';
    }
}
