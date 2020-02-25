<?php

namespace honray\command;

use honray\FunctionBuilder;
use honray\FunctionSaver;
use honray\FunctionScanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenFunctionData extends Command
{
    protected static $defaultName = 'gen-func';

    protected function configure()
    {
        $this->setDescription('生成指定文件的函数配置数据')
            ->setHelp('此命令可生成指定文件下的惟快函数配置文件，默认问当前目录')
            ->addArgument('path', InputArgument::OPTIONAL, '文件夹路径');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('path');

        if (empty($path)) {
            $path = './';
        }

        if (!is_dir($path)) {
            $io->caution('路径错误');

            return 1;
        }

        $path = realpath($path);

        $functionBuilder = new FunctionBuilder();
        $functionScanner = new FunctionScanner($functionBuilder);
        $result = $functionScanner->scanFolder($path);

        $functionSaver = new FunctionSaver();
        $filePath = $functionSaver->save($result);
        
        $io->writeln('函数保存在: '.$filePath);

        return 0;
    }
}