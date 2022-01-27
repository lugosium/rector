<?php

declare (strict_types=1);
namespace RectorPrefix20220127\Helmich\TypoScriptParser\Parser\AST;

final class Comment extends \Helmich\TypoScriptParser\Parser\AST\Statement
{
    /**
     * @var string
     */
    public $comment;
    public function __construct(string $comment, int $sourceLine)
    {
        parent::__construct($sourceLine);
        $this->comment = $comment;
    }
}
