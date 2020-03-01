<?php

namespace honray;

/**
 * 扫描文件夹下的文件
 */
class FunctionScanner
{
    /**
     * 函数构建器
     *
     * @var FunctionBuilder
     */
    private $builder;

    private $functionData = [];

    public function __construct(FunctionBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function scanFolder($folderPath)
    {
        if (is_dir($folderPath)) {
            $allFiles = scandir($folderPath);

            foreach ($allFiles as $filePath) {
                if (!$this->isValidFile($filePath)) {
                    continue ;
                }

                $fullPath = $folderPath . DIRECTORY_SEPARATOR . $filePath;

                if (is_dir($fullPath)) {
                    $this->scanFolder($fullPath);
                    continue ;
                }

                $this->scanFile($fullPath);
            }

            return $this->functionData;
        } else {
            throw new \Exception('无法生成函数，目录不存在：.'.$folderPath);
        }
    }
    
    public function scanFile($filePath)
    {
        $functionParser = $this->builder->build($filePath);
        $funcData = $functionParser->getFunctionData();

        if ($funcData !== null) {
            $this->functionData[] = $funcData;   
        }

        return $this->functionData;
    }

    /**
     * 检测是否是合法的文件
     *
     * @param string $filePath 文件路径
     * @return boolean
     */
    private function isValidFile($filePath)
    {
        if (in_array($filePath, ['.', '..', 'vendor'])) {
            return false;
        }

        return true;
    }
}