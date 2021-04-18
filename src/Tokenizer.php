<?php


namespace TaHUoP;

use TaHUoP\Enums\Lexemes\AbstractLexemeEnum;
use TaHUoP\Enums\Lexemes\ClassVarDeclarationType;
use TaHUoP\Enums\Lexemes\Keyword;
use TaHUoP\Enums\Lexemes\LexemeType;
use TaHUoP\Enums\Lexemes\ScalarType;
use TaHUoP\Exceptions\LexicalErrorException;

/**
 * '+' in the start of the state indicates that state is accepting
 *
 * Class Tokenizer
 * @package TaHUoP
 */
class Tokenizer
{
    private array $fixedCharStateTransitions;


    public function __construct(
        private string $input
    ) {
        $this->fixedCharStateTransitions = [
            '' => [
                '{' => ['+lcb', LexemeType::SYMBOL()],
                '}' => ['+rcb', LexemeType::SYMBOL()],
                '(' => ['+lb', LexemeType::SYMBOL()],
                ')' => ['+rb', LexemeType::SYMBOL()],
                '[' => ['+lsb', LexemeType::SYMBOL()],
                ']' => ['+rsb', LexemeType::SYMBOL()],
                '.' => ['+dot', LexemeType::SYMBOL()],
                ',' => ['+comma', LexemeType::SYMBOL()],
                ';' => ['+semicolon', LexemeType::SYMBOL()],
                '+' => ['+plus', LexemeType::SYMBOL()],
                '-' => ['+minus', LexemeType::SYMBOL()],
                '*' => ['+asterisk', LexemeType::SYMBOL()],
                '/' => ['+slash', LexemeType::SYMBOL()],
                '&' => ['+ampersand', LexemeType::SYMBOL()],
                '|' => ['+vbar', LexemeType::SYMBOL()],
                '<' => ['+lt', LexemeType::SYMBOL()],
                '>' => ['+gt', LexemeType::SYMBOL()],
                '=' => ['+eq', LexemeType::SYMBOL()],
                '~' => ['+tilde', LexemeType::SYMBOL()],
                '"' => ['strlq', null],
            ],
            'str' => [
                '"' => ['+strrq', LexemeType::STRING_CONSTANT()],
            ],
        ];

        /** @var AbstractLexemeEnum $keyword */
        $keywords = [...Keyword::instances(), ...ClassVarDeclarationType::instances(), ...ScalarType::instances()];
        foreach ($keywords as $keyword) {
            foreach (str_split($keyword->getValue()) as $index => $char) {
                if ($index === 0) {
                    $this->fixedCharStateTransitions[''][$char] = ["+_{$char}/id", LexemeType::IDENTIFIER()];
                } else {
                    $keywordPart = substr($keyword->getValue(), 0, $index);
                    $state = "+_{$keywordPart}{$char}";
                    if (($index + 1) !== strlen($keyword->getValue())) {
                        $state .= '/id';
                        $type = LexemeType::IDENTIFIER();
                    } else {
                        $type = $keyword->getType();
                    }
                    $this->fixedCharStateTransitions["+_{$keywordPart}/id"][$char] = [$state, $type];
                }
            }
        }
    }

    public function getNextToken(): ?Token
    {
        $state = ''; //empty string is start state
        $type = null;
        $lastAcceptingState = null;
        $lastPos = null;
        $lastType = null;

        for($pos = 0; !is_null($state); $pos++) {
            if ($this->isStateAccepting($state)) {
                $lastAcceptingState = $state;
                $lastPos = $pos;
                $lastType = $type;
            }

            $currentChar = $this->input[$pos] ?? null;
            list($state, $type) = $this->getNextStateAndType($state, $currentChar);
        }

        if (!is_null($lastAcceptingState)) {
            $tokenValue = substr($this->input, 0, $lastPos);
            $this->input = substr($this->input, $lastPos);

            //skipping whitespaces
            return preg_match('/^\s+$/', $tokenValue)
                ? $this->getNextToken()
                : new Token($tokenValue, $lastType);
        } else if (strlen($this->input) === 0) {
            return null;
        } else {
            throw new LexicalErrorException('Invalid token ' . substr($this->input, 0, $pos));
        }
    }

    //TODO: remove if it stays unused
//    public function peekNextToken(): ?Token
//    {
//        $token = $this->getNextToken();
//        $this->input = $token->getValue() . $this->input;
//        return $token;
//    }

    private function isStateAccepting(?string $state): bool
    {
        return !is_null($state) && str_starts_with($state, '+');
    }

    private function getNextStateAndType(string $state, ?string $currentChar): array
    {
        if (is_null($currentChar))
            return [null, null];

        if (isset($this->fixedCharStateTransitions[$state][$currentChar])) {
            return $this->fixedCharStateTransitions[$state][$currentChar];
        } else if (($state === '' || $state === '+number') && preg_match('/^[0-9]{1}$/', $currentChar)) {
            return ['+number', LexemeType::INTEGER_CONSTANT()];
        } else if (($state === 'strlq' || $state === 'str') && !in_array($currentChar, ['"', PHP_EOL])) {
            return ['str', null];
        } else if (($state === '' || $state === '+id' || str_starts_with($state, '+_')) && preg_match('/^[A-Za-z0-9_]{1}$/', $currentChar)) {
            return ['+id', LexemeType::IDENTIFIER()];
        } else if (($state === '' || $state === '+whitespace') && preg_match('/^\s{1}$/', $currentChar)) {
            return ['+whitespace', null];
        }

        return [null, null];
    }
}