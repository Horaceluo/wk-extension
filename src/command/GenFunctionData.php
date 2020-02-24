<?php

namespace honray\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenFunctionData extends Command
{
    protected static $defaultName = 'gen-func';

    protected function configure()
    {
        $this->setDescription('生成指定文件的函数配置数据')
            ->setHelp('此命令可生成指定文件下的惟快函数配置文件，默认问当前目录');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...

        return 0;
    }
}