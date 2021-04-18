<?php


namespace TaHUoP\Enums\Lexemes;

/**
 * @method static ScalarType INT()
 * @method static ScalarType CHAR()
 * @method static ScalarType BOOLEAN()
 */
class ScalarType extends AbstractLexemeEnum
{
    public const INT = 'int';
    public const CHAR = 'char';
    public const BOOLEAN = 'boolean';

    public function getType(): LexemeType
    {
        return LexemeType::SCALAR_TYPE();
    }
}