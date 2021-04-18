<?php


namespace TaHUoP\Enums\Lexemes;

/**
 * @method static ClassVarDeclarationType FIELD()
 * @method static ClassVarDeclarationType STATIC()
 */
class ClassVarDeclarationType extends AbstractLexemeEnum
{
    public const FIELD = 'field';
    public const STATIC = 'static';

    public function getType(): LexemeType
    {
        return LexemeType::CLASS_VAR_DECLARATION_TYPE();
    }
}