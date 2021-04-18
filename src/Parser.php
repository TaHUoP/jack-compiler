<?php


namespace TaHUoP;


use Exception;
use TaHUoP\Enums\Lexemes\AbstractLexemeEnum;
use TaHUoP\Enums\Lexemes\Keyword;
use TaHUoP\Enums\Lexemes\LexemeType;
use TaHUoP\Enums\Lexemes\Symbol;
use TaHUoP\Exceptions\SyntaxErrorException;

//TODO: separate content generation to individual class
class Parser
{
    private Tokenizer $tokenizer;

    /**
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function parseFile(string $path): string
    {
        $this->tokenizer = new Tokenizer(file_get_contents($path));

        $content = $this->compileClass();

        unset($this->tokenizer);

        return $content;
    }

    private function compileClass(): string
    {
        $content = '<class>';

        $content .= $this->assertAndGetContent($this->tokenizer->getNextToken(), Keyword::CLASS_());
        $content .= $this->assertAndGetContent($this->tokenizer->getNextToken(), LexemeType::IDENTIFIER());
        $content .= $this->assertAndGetContent($this->tokenizer->getNextToken(), Symbol::LCB());

        $nextToken = $this->tokenizer->getNextToken();
        while ($nextToken->getType() === LexemeType::CLASS_VAR_DECLARATION_TYPE()) {
            $content .= $this->compileClassVarDeclaration($nextToken);
            $nextToken = $this->tokenizer->getNextToken();
        }

        $content .= $this->assertAndGetContent($nextToken, Symbol::RCB());

        $content .= '</class>';

        return $content;
    }

    private function compileClassVarDeclaration(Token $firstToken): string
    {
        $content = '<classVarDec>';

        $content .= $this->assertAndGetContent($firstToken, LexemeType::CLASS_VAR_DECLARATION_TYPE());

        try {
            $nextToken = $this->tokenizer->getNextToken();
            $content .= $this->assertAndGetContent($nextToken, LexemeType::SCALAR_TYPE());
        } catch (SyntaxErrorException) {
            $content .= $this->assertAndGetContent($nextToken, LexemeType::IDENTIFIER());
        }

        $content .= $this->assertAndGetContent($this->tokenizer->getNextToken(), LexemeType::IDENTIFIER());

        $nextToken = $this->tokenizer->getNextToken();
        while ($nextToken->getValue() === Symbol::COMMA) {
            $content .= $this->assertAndGetContent($nextToken, Symbol::COMMA());
            $content .= $this->assertAndGetContent($this->tokenizer->getNextToken(), LexemeType::IDENTIFIER());
            $nextToken = $this->tokenizer->getNextToken();
        }

        $content .= $this->assertAndGetContent($nextToken, Symbol::SEMICOLON());

        $content .= '</classVarDec>';

        return $content;
    }

    private function assertAndGetContent(?Token $token, LexemeType|AbstractLexemeEnum $lexeme): string
    {
        if (
            (($lexeme instanceof LexemeType) && $token->getType() !== $lexeme)
            || (($lexeme instanceof AbstractLexemeEnum) && $token->getValue() !== $lexeme->getValue())
            || is_null($token)
        ) {
            //TODO: add info to exception
            throw new SyntaxErrorException();
        }

        return "<{$token->getType()->getValue()}>{$token->getValue()}</{$token->getType()->getValue()}>";
    }
}