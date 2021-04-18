<?php


namespace TaHUoP\Enums\Lexemes;

/**
 * @method static Symbol LCB()
 * @method static Symbol RCB()
 * @method static Symbol LB()
 * @method static Symbol RB()
 * @method static Symbol LSB()
 * @method static Symbol RSB()
 * @method static Symbol DOT()
 * @method static Symbol COMMA()
 * @method static Symbol SEMICOLON()
 * @method static Symbol PLUS()
 * @method static Symbol MINUS()
 * @method static Symbol ASTERISK()
 * @method static Symbol SLASH()
 * @method static Symbol AMPERSAND()
 * @method static Symbol VBAR()
 * @method static Symbol LT()
 * @method static Symbol GT()
 * @method static Symbol EQ()
 * @method static Symbol TILDE()
 */
class Symbol extends AbstractLexemeEnum
{
    public const LCB = '{';
    public const RCB = '}';
    public const LB = '(';
    public const RB = ')';
    public const LSB = '[';
    public const RSB = ']';
    public const DOT = '.';
    public const COMMA = ',';
    public const SEMICOLON = ';';
    public const PLUS = '+';
    public const MINUS = '-';
    public const ASTERISK = '*';
    public const SLASH = '/';
    public const AMPERSAND = '&';
    public const VBAR = '|';
    public const LT = '<';
    public const GT = '>';
    public const EQ = '=';
    public const TILDE = '~';

    public function getType(): LexemeType
    {
        return LexemeType::SYMBOL();
    }
}