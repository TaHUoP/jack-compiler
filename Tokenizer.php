<?php

class Tokenizer
{
    private array $keywords = [
        'class', 'constructor', 'function', 'method', 'field', 'static', 'var', 'int', 'char', 'boolean', 'void',
        'true', 'false', 'null', 'this', 'let', 'do', 'if', 'else', 'while', 'return',
    ];

    private array $fixedCharStateTransitions = [
        '' => [
            '{' => '+lcb',
            '}' => '+rcb',
            '(' => '+lb',
            ')' => '+rb',
            '[' => '+lsb',
            ']' => '+rsb',
            '.' => '+dot',
            ',' => '+comma',
            ';' => '+semicolon',
            '+' => '+plus',
            '-' => '+minus',
            '*' => '+asterisk',
            '/' => '+slash',
            '&' => '+ampersand',
            '|' => '+vbar',
            '<' => '+lt',
            '>' => '+gt',
            '=' => '+eq',
            '~' => '+tilde',
            '"' => 'strlq',
        ],
        'str' => [
            '"' => '+strrq',
        ],
    ];


    public function __construct(
        private string $input
    ) {
        foreach ($this->keywords as $keyword) {
            foreach (str_split($keyword) as $index => $char) {
                if ($index === 0) {
                    $this->fixedCharStateTransitions[''][$char] = "+_{$char}/id";
                } else {
                    $keywordPart = substr($keyword, 0, $index);
                    $this->fixedCharStateTransitions['+_' . $keywordPart . '/id'][$char] =
                        '+_' . $keywordPart . $char . (($index + 1) === strlen($keyword) ? '' : '/id');
                }
            }
        }
    }

    public function getNextToken(): ?string
    {
        $state = ''; //empty string is start state
        $lastAcceptingState = null;
        $lastPos = null;

        for($pos = 0; !is_null($state); $pos++) {
            if ($this->isStateAccepting($state)) {
                $lastAcceptingState = $state;
                $lastPos = $pos;
            }

            $currentChar = $this->input[$pos] ?? null;
            $state = $this->getNextState($state, $currentChar);
        }

        if (!is_null($lastAcceptingState)) {
            $token = substr($this->input, 0, $lastPos);
            $this->input = substr($this->input, $lastPos);

            return preg_match('/^\s+$/', $token) ? $this->getNextToken() : $token;
        } else if (strlen($this->input) === 0) {
            return null;
        } else {
            throw new Exception('Invalid token ' . substr($this->input, 0, $pos));
        }
    }

    private function isStateAccepting(?string $state): bool
    {
        return !is_null($state) && str_starts_with($state, '+');
    }

    private function getNextState(string $state, ?string $currentChar): ?string
    {
        if (is_null($currentChar))
            return null;

        if (isset($fixedCharStateTransitions[$state][$currentChar])) {
            return $fixedCharStateTransitions[$state][$currentChar];
        } else if (($state === '' || $state === '+number') && preg_match('/^[0-9]{1}$/', $currentChar)) {
            return '+number';
        } else if (($state === 'strlq' || $state === 'str') && !in_array($currentChar, ['"', PHP_EOL])) {//TODO: cover new line char
            return 'str';
        } else if (($state === '' || $state === '+id' || str_starts_with($state, '+_')) && preg_match('/^[A-Za-z0-9_]{1}$/', $currentChar)) {
            return '+id';
        } else if (($state === '' || $state === '+whitespace') && preg_match('/^\s{1}$/', $currentChar)) {
            return '+whitespace';
        }

        return null;
    }
}

$t = new Tokenizer($argv[1]);
while($token = $t->getNextToken()) {
    echo $token . PHP_EOL;
}