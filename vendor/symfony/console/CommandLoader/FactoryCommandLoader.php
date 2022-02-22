<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RectorPrefix20220222\Symfony\Component\Console\CommandLoader;

use RectorPrefix20220222\Symfony\Component\Console\Command\Command;
use RectorPrefix20220222\Symfony\Component\Console\Exception\CommandNotFoundException;
/**
 * A simple command loader using factories to instantiate commands lazily.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class FactoryCommandLoader implements \RectorPrefix20220222\Symfony\Component\Console\CommandLoader\CommandLoaderInterface
{
    /**
     * @var mixed[]
     */
    private $factories;
    /**
     * @param callable[] $factories Indexed by command names
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }
    /**
     * {@inheritdoc}
     */
    public function has(string $name) : bool
    {
        return isset($this->factories[$name]);
    }
    /**
     * {@inheritdoc}
     */
    public function get(string $name) : \RectorPrefix20220222\Symfony\Component\Console\Command\Command
    {
        if (!isset($this->factories[$name])) {
            throw new \RectorPrefix20220222\Symfony\Component\Console\Exception\CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
        }
        $factory = $this->factories[$name];
        return $factory();
    }
    /**
     * {@inheritdoc}
     */
    public function getNames() : array
    {
        return \array_keys($this->factories);
    }
}
