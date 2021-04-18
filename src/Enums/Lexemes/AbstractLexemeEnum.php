<?php


namespace TaHUoP\Enums\Lexemes;

use TaHUoP\Enums\AbstractEnum;

abstract class AbstractLexemeEnum extends AbstractEnum
{
    abstract public function getType(): LexemeType;
}