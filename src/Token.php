<?php


namespace TaHUoP;


use TaHUoP\Enums\Lexemes\LexemeType;

class Token
{
    public function __construct(
        private string|int $value,
        private LexemeType $type
    ) { }

    public function getValue(): int|string
    {
        return $this->value;
    }

    public function getType(): LexemeType
    {
        return $this->type;
    }
}