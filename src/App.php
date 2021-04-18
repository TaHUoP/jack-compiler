<?php

namespace TaHUoP;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class App extends SingleCommandApplication
{
    public function __construct(
        private Parser $parser
    ) {
        parent::__construct();
        $this
            ->addArgument('inputPath', InputArgument::REQUIRED, 'Path to .jack file or directory with .jack files')
            ->addArgument('outputFilePath', InputArgument::OPTIONAL, 'Path to destination file or directory (.xml or .vm)')
            ->addArgument('memoryLimit', InputArgument::OPTIONAL, 'PHP memory limit. Unlimited by default')
            ->setCode([$this, 'main']);
    }

    public function main(InputInterface $input, OutputInterface $output): void
    {
        try {
            ini_set('memory_limit', $input->getArgument('memoryLimit') ?? -1);

            $inputPath = $input->getArgument('inputPath');

            if(!is_readable($inputPath))
                throw new InvalidArgumentException("Unable to read from $inputPath");

            if (is_dir($inputPath)) {
                $files = array_filter(
                    array_map(
                        fn(string $file): string => $inputPath . DIRECTORY_SEPARATOR . $file,
                        scandir($inputPath)
                    ),
                    fn(string $file): bool => is_file($file) && str_ends_with($file, '.jack')
                );

                if (!$files)
                    throw new InvalidArgumentException('Directory doesn\'t contain readable .jack files');

                foreach ($files as $file) {
                    $this->writeFile($file, $input, $output);
                }
            } else {
                if(!str_ends_with($inputPath, '.jack'))
                    throw new InvalidArgumentException('Only .jack extension is supported');

                $this->writeFile($inputPath, $input, $output);
            }

        } catch (Exception $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</>');
        }
    }

    private function writeFile(string $inputPath, InputInterface $input, OutputInterface $output): void
    {
        $outputFileContent = $this->parser->parseFile($inputPath);

        $outputFilePath = $input->getArgument('outputFilePath') ?? $this->getOutputFilePath($inputPath);
        if (file_put_contents($outputFilePath, $outputFileContent) !== false) {
            $output->writeln("File $outputFilePath was successfully built.");
        } else {
            $output->writeln("<fg=red>Unable to write to file $outputFilePath.</>");
        }
    }

    private function getOutputFilePath(string $inputPath): string
    {
        $pathInfo = pathinfo($inputPath);

        return $pathInfo['dirname'] . DIRECTORY_SEPARATOR . "{$pathInfo['filename']}.xml";
    }
}
