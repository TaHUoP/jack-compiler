<?php


namespace TaHUoP\Enums\Lexemes;

use TaHUoP\Enums\AbstractEnum;

/**
 * @method static LexemeType KEYWORD()
 * @method static LexemeType SYMBOL()
 * @method static LexemeType INTEGER_CONSTANT()
 * @method static LexemeType STRING_CONSTANT()
 * @method static LexemeType IDENTIFIER()
 * @method static LexemeType CLASS_VAR_DECLARATION_TYPE()
 * @method static LexemeType SUBROUTINE_DECLARATION_TYPE()
 * @method static LexemeType SCALAR_TYPE()
 */
class LexemeType extends AbstractEnum
{
    public const KEYWORD = 'keyword';
    public const SYMBOL = 'symbol';
    public const INTEGER_CONSTANT = 'integerConstant';
    public const STRING_CONSTANT = 'stringConstant';
    public const IDENTIFIER = 'identifier';
    public const CLASS_VAR_DECLARATION_TYPE = 'classVarDeclarationType';
    public const SUBROUTINE_DECLARATION_TYPE = 'subroutineVarDeclarationType';
    public const SCALAR_TYPE = 'scalarType';
}