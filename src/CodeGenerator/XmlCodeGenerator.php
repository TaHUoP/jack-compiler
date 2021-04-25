<?php


namespace TaHUoP\CodeGenerator;


class XmlCodeGenerator implements CodeGeneratorInterface
{
    public function getClassStart(string $className): string
    {
        return "<class><keyword>class</keyword><identifier>$className</identifier><symbol>{</symbol>";
    }

    public function getClassEnd(string $className): string
    {
        return '<symbol>}</symbol></class>';
    }

    public function getClassVarDeclaration(string $declarationType, string $type, array $varNames): string
    {
        $content = "<classVarDec><classVarDeclarationType>$declarationType</classVarDeclarationType><classVarType>$type</classVarType>";
        $content .= implode(
            '<symbol>,</symbol>',
            array_map(fn(string $varName):string => "<identifier>$varName</identifier>", $varNames)
        );

        return "$content<symbol>;</symbol></classVarDec>";
    }
}