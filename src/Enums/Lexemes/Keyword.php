<?php


namespace TaHUoP\Enums\Lexemes;

/**
 * @method static Keyword CLASS_()
 * @method static Keyword VAR()
 * @method static Keyword VOID()
 * @method static Keyword TRUE()
 * @method static Keyword FALSE()
 * @method static Keyword NULL()
 * @method static Keyword THIS()
 * @method static Keyword LET()
 * @method static Keyword DO()
 * @method static Keyword IF()
 * @method static Keyword ELSE()
 * @method static Keyword WHILE()
 * @method static Keyword RETURN()
 */
class Keyword extends AbstractLexemeEnum
{
    public const CLASS_ = 'class';
    public const VAR = 'var';
    public const VOID = 'void';
    public const TRUE = 'true';
    public const FALSE = 'false';
    public const NULL = 'null';
    public const THIS = 'this';
    public const LET = 'let';
    public const DO = 'do';
    public const IF = 'if';
    public const ELSE = 'else';
    public const WHILE = 'while';
    public const RETURN = 'return';

    public function getType(): LexemeType
    {
        return LexemeType::KEYWORD();
    }
}