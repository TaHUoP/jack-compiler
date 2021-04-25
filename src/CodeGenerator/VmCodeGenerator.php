<?php


namespace TaHUoP\CodeGenerator;


class VmCodeGenerator implements CodeGeneratorInterface
{
    public function getClassStart(string $className): string
    {
        return '';
    }

    public function getClassEnd(string $className): string
    {
        return '';
    }

    public function getClassVarDeclaration(string $declarationType, string $type, array $varNames): string
    {
        return '';
    }
}