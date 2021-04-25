<?php


namespace TaHUoP\CodeGenerator;


interface CodeGeneratorInterface
{
    public function getClassStart(string $className): string;
    public function getClassEnd(string $className): string;
    public function getClassVarDeclaration(string $declarationType, string $type, array $varNames): string;
}