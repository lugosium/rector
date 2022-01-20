<?php

declare (strict_types=1);
namespace RectorPrefix20220120\Doctrine\Inflector\Rules\Spanish;

use RectorPrefix20220120\Doctrine\Inflector\GenericLanguageInflectorFactory;
use RectorPrefix20220120\Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends \RectorPrefix20220120\Doctrine\Inflector\GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : \RectorPrefix20220120\Doctrine\Inflector\Rules\Ruleset
    {
        return \RectorPrefix20220120\Doctrine\Inflector\Rules\Spanish\Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : \RectorPrefix20220120\Doctrine\Inflector\Rules\Ruleset
    {
        return \RectorPrefix20220120\Doctrine\Inflector\Rules\Spanish\Rules::getPluralRuleset();
    }
}
