<?php

declare (strict_types=1);
namespace RectorPrefix20210708\Symplify\ComposerJsonManipulator\Bundle;

use RectorPrefix20210708\Symfony\Component\HttpKernel\Bundle\Bundle;
use RectorPrefix20210708\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension;
final class ComposerJsonManipulatorBundle extends \RectorPrefix20210708\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return
     */
    protected function createContainerExtension() : ?\RectorPrefix20210708\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \RectorPrefix20210708\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension();
    }
}
