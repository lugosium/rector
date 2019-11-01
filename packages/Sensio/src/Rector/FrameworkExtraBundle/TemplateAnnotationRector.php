<?php

declare(strict_types=1);

namespace Rector\Sensio\Rector\FrameworkExtraBundle;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\BetterPhpDocParser\PhpDocNode\Sensio\SensioTemplateTagValueNode;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;
use Rector\Sensio\Helper\TemplateGuesser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TemplateAnnotationRector extends AbstractRector
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var TemplateGuesser
     */
    private $templateGuesser;

    public function __construct(TemplateGuesser $templateGuesser, int $version = 3)
    {
        $this->templateGuesser = $templateGuesser;
        $this->version = $version;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition(
            'Turns `@Template` annotation to explicit method call in Controller of FrameworkExtraBundle in Symfony',
            [
                new CodeSample(
                    <<<'PHP'
/**
 * @Template()
 */
public function indexAction()
{
}
PHP
                    ,
                    <<<'PHP'
public function indexAction()
{
    return $this->render("index.html.twig");
}
PHP
                ),
            ]
        );
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Class_) {
            return $this->addBaseClassIfMissing($node);
        }

        if ($node instanceof ClassMethod) {
            return $this->replaceTemplateAnnotation($node);
        }

        return null;
    }

    private function addBaseClassIfMissing(Class_ $node): ?Node
    {
        if ($node->extends !== null) {
            return null;
        }

        if (! $this->classHasTemplateAnnotations($node)) {
            return null;
        }

        $node->extends = new FullyQualified(AbstractController::class);

        return $node;
    }

    private function classHasTemplateAnnotations(Class_ $node): bool
    {
        foreach ($node->stmts as $stmtNode) {
            $phpDocInfo = $this->getPhpDocInfo($stmtNode);
            if ($phpDocInfo === null) {
                continue;
            }

            $templateTagValueNode = $phpDocInfo->getByType(SensioTemplateTagValueNode::class);
            if ($templateTagValueNode !== null) {
                return true;
            }
        }

        return false;
    }

    private function replaceTemplateAnnotation(ClassMethod $classMethod): ?Node
    {
        $phpDocInfo = $this->getPhpDocInfo($classMethod);
        if ($phpDocInfo === null) {
            return null;
        }

        $templateTagValueNode = $phpDocInfo->getByType(SensioTemplateTagValueNode::class);
        if ($templateTagValueNode === null) {
            return null;
        }

        /** @var Return_|null $returnNode */
        $returnNode = $this->betterNodeFinder->findLastInstanceOf((array) $classMethod->stmts, Return_::class);

        // create "$this->render('template.file.twig.html', ['key' => 'value']);" method call
        $renderArguments = $this->resolveRenderArguments($classMethod, $returnNode, $templateTagValueNode);
        $thisRenderMethodCall = $this->createMethodCall('this', 'render', $renderArguments);

        if ($returnNode === null) {
            // or add as last statement in the method
            $classMethod->stmts[] = new Return_($thisRenderMethodCall);
        }

        // replace Return_ node value if exists and is not already in correct format
        if ($returnNode && ! $returnNode->expr instanceof MethodCall) {
            $returnNode->expr = $thisRenderMethodCall;
        }

        // remove annotation
        $this->docBlockManipulator->removeTagFromNode($classMethod, SensioTemplateTagValueNode::class);

        return $classMethod;
    }

    /**
     * @return Arg[]
     */
    private function resolveRenderArguments(
        ClassMethod $classMethod,
        ?Return_ $returnNode,
        SensioTemplateTagValueNode $sensioTemplateTagValueNode
    ): array {
        $arguments = [$this->resolveTemplateName($classMethod, $sensioTemplateTagValueNode)];

        if ($returnNode === null) {
            return $this->createArgs($arguments);
        }

        if ($returnNode->expr instanceof Array_ && count($returnNode->expr->items)) {
            $arguments[] = $returnNode->expr;
        }

        $arguments = array_merge($arguments, $this->resolveArrayArgumentsFromMethodCall($returnNode));

        return $this->createArgs($arguments);
    }

    private function resolveTemplateName(
        ClassMethod $classMethod,
        SensioTemplateTagValueNode $sensioTemplateTagValueNode
    ): string {
        if ($sensioTemplateTagValueNode->getTemplate() !== null) {
            return $sensioTemplateTagValueNode->getTemplate();
        }

        return $this->templateGuesser->resolveFromClassMethodNode($classMethod, $this->version);
    }

    /**
     * Already existing method call
     *
     * @return Array_[]
     */
    private function resolveArrayArgumentsFromMethodCall(Return_ $returnNode): array
    {
        if (! $returnNode->expr instanceof MethodCall) {
            return [];
        }

        $arguments = [];
        foreach ($returnNode->expr->args as $arg) {
            if (! $arg->value instanceof Array_) {
                continue;
            }

            $arguments[] = $arg->value;
        }

        return $arguments;
    }
}
