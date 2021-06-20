<?php


namespace TaHUoP;


use Exception;
use TaHUoP\CodeGenerator\CodeGeneratorInterface;
use TaHUoP\Enums\Lexemes\AbstractLexemeEnum;
use TaHUoP\Enums\Lexemes\Keyword;
use TaHUoP\Enums\Lexemes\LexemeType;
use TaHUoP\Enums\Lexemes\Symbol;
use TaHUoP\Exceptions\LexicalErrorException;
use TaHUoP\Exceptions\SyntaxErrorException;

class Parser
{
    public function __construct(
        private CodeGeneratorInterface $codeGenerator
    ) { }

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
        $this->assert($this->tokenizer->getNextToken(), Keyword::CLASS_());
        $this->assert($className = $this->tokenizer->getNextToken(), LexemeType::IDENTIFIER());
        $this->assert($this->tokenizer->getNextToken(), Symbol::LCB());

        $content = $this->codeGenerator->getClassStart($className->getValue());

        $nextToken = $this->tokenizer->getNextToken();
        while ($nextToken->getType() === LexemeType::CLASS_VAR_DECLARATION_TYPE()) {
            $content .= $this->compileClassVarDeclaration($nextToken);
            $nextToken = $this->tokenizer->getNextToken();
        }

        while ($nextToken->getType() === LexemeType::SUBROUTINE_DECLARATION_TYPE()) {
            $content .= $this->compileSubroutineDeclaration($nextToken);
            $nextToken = $this->tokenizer->getNextToken();
        }

        $this->assert($nextToken, Symbol::RCB());

        $content .= $this->codeGenerator->getClassEnd($className->getValue());

        return $content;
    }

    private function compileClassVarDeclaration(Token $declarationType): string
    {
        $this->assert($declarationType, LexemeType::CLASS_VAR_DECLARATION_TYPE());

        $type = $this->tokenizer->getNextToken();
        $this->assertType($type);

        $varNames = [];

        $fn = function (?Token $varName) use (&$varNames): ?Token {
            $this->assert($varName, LexemeType::IDENTIFIER());
            $varNames[]= $varName->getValue();
            return $this->tokenizer->getNextToken();
        };

        $nextToken = $fn($this->tokenizer->getNextToken());

        while ($nextToken->getValue() === Symbol::COMMA) {
            $nextToken = $fn($this->tokenizer->getNextToken());
        }

        $this->assert($nextToken, Symbol::SEMICOLON());

        return $this->codeGenerator->getClassVarDeclaration($declarationType->getValue(), $type->getValue(), $varNames);
    }

    /**
     * @throws LexicalErrorException
     * @throws SyntaxErrorException
     */
    private function compileSubroutineDeclaration(Token $declarationType): string
    {
        $content = '';

        $this->assert($declarationType, LexemeType::SUBROUTINE_DECLARATION_TYPE());

        $returnType = $this->tokenizer->getNextToken();
        try {
            $this->assert($returnType, Keyword::VOID());
        } catch (SyntaxErrorException) {
            $this->assertType($returnType);
        }

        $subroutineName = $this->tokenizer->getNextToken();

        $this->assert($this->tokenizer->getNextToken(), Symbol::LB());
        $nextToken = $this->tokenizer->getNextToken();
        try {
            $this->assertType($nextToken);
            $content .= $this->compileParameterList($nextToken);
        } catch (SyntaxErrorException) {}

        $this->assert($this->tokenizer->getNextToken(), Symbol::RB());

        $content .= $this->compileSubroutineBody();

        return $content;
    }

    private function assert(?Token $token, LexemeType|AbstractLexemeEnum $lexeme): void
    {
        $errorMessage = null;
        if (($lexeme instanceof LexemeType) && $token->getType() !== $lexeme) {
            $errorMessage = "Expected {$lexeme->getValue()} lexeme type, got {$token->getType()->getValue()}. Token: {$token->getValue()}";
        }
        if (($lexeme instanceof AbstractLexemeEnum) && $token->getValue() !== $lexeme->getValue()) {
            $errorMessage = "Expected {$lexeme->getValue()} lexeme, got {$token->getValue()}. Token type: {$token->getValue()}";
        }
        if ($errorMessage) {
            throw new SyntaxErrorException($errorMessage);
        }
    }

    /**
     * @param Token|null $type
     * @throws SyntaxErrorException
     */
    private function assertType(?Token $type): void
    {
        try {
            $this->assert($type, LexemeType::SCALAR_TYPE());
        } catch (SyntaxErrorException) {
            $this->assert($type, LexemeType::IDENTIFIER());
        }
    }

    private function compileParameterList(?Token $nextToken): string
    {
        return '';
    }
}