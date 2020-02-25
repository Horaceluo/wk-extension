<?php

namespace honray;

class FunctionSaver
{
    private $path;

    public function __construct($path = '.wk')
    {
        $this->path = $path;
    }

    /**
     * 保存函数数据搭配文件夹中
     *
     * @param array $funcData
     * @return boolean
     */
    public function save($funcData)
    {
        $functionPath = implode(DIRECTORY_SEPARATOR, [$this->path, 'functions']);

        if (!is_dir($functionPath)) {
            mkdir($functionPath, 0777, true);
        }

        $fileName = microtime(true) .  '-' . rand() . '.json';
        $fileFullPath = $functionPath . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($fileFullPath, json_encode($funcData));

        return $fileFullPath;
    }
}