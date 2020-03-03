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
     * @param string $name
     * @return boolean
     */
    public function save($funcData, $fileName = null)
    {
        $functionPath = implode(DIRECTORY_SEPARATOR, [$this->path, 'functions']);

        if (!is_dir($functionPath)) {
            mkdir($functionPath, 0777, true);
        }

        if (!$fileName) {
            $fileName = microtime(true) .  '-' . rand() . '.json';;
        } else {
            $fileName = $fileName . '.json';
        }
        
        $fileFullPath = $functionPath . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($fileFullPath, json_encode($funcData));

        return $fileFullPath;
    }
}