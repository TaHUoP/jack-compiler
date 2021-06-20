<?php


namespace TaHUoP\Enums\Lexemes;

/**
 * @method static SubroutineDeclarationType CONSTRUCTOR()
 * @method static SubroutineDeclarationType FUNCTION()
 * @method static SubroutineDeclarationType METHOD()
 */
class SubroutineDeclarationType extends AbstractLexemeEnum
{
    public const CONSTRUCTOR = 'constructor';
    public const FUNCTION = 'function';
    public const METHOD = 'method';

    public function getType(): LexemeType
    {
        return LexemeType::SUBROUTINE_DECLARATION_TYPE();
    }
}