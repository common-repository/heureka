<?php

declare (strict_types=1);
namespace HeurekaDeps\PHPStan\PhpDocParser\Ast\PhpDoc;

use HeurekaDeps\PHPStan\PhpDocParser\Ast\NodeAttributes;
class InvalidTagValueNode implements PhpDocTagValueNode
{
    use NodeAttributes;
    /** @var string (may be empty) */
    public $value;
    /** @var \PHPStan\PhpDocParser\Parser\ParserException */
    public $exception;
    public function __construct(string $value, \HeurekaDeps\PHPStan\PhpDocParser\Parser\ParserException $exception)
    {
        $this->value = $value;
        $this->exception = $exception;
    }
    public function __toString() : string
    {
        return $this->value;
    }
}
