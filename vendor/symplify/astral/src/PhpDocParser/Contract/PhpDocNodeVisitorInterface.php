<?php

declare (strict_types=1);
namespace RectorPrefix202208\Symplify\Astral\PhpDocParser\Contract;

use PHPStan\PhpDocParser\Ast\Node;
/**
 * Inspired by https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitor.php
 */
interface PhpDocNodeVisitorInterface
{
    public function beforeTraverse(Node $node) : void;
    public function enterNode(Node $node);
    /**
     * @return null|int|\PhpParser\Node|Node[] Replacement node (or special return)
     */
    public function leaveNode(Node $node);
    public function afterTraverse(Node $node) : void;
}
