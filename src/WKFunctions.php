<?php

namespace honray;

use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Name;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Comment;

/**
 * 惟快自定义函数
 */
class WKFunctions
{
    /**
     * 文件路径
     *
     * @var string
     */
    private $path;

    /**
     * 标志符号
     *
     * @var string 解析标志符号
     */
    private $symbol;

    /**
     * 类名
     *
     * @var string
     */
    private $className = '';

    /**
     * 类标题(注释)
     *
     * @var string
     */
    private $classTile = '';

    /**
     * 命名空间
     *
     * @var string
     */
    private $namespace = '';

    /**
     * 方法
     *
     * @var array
     */
    private $methods = [];

    private $isEmpty = true;

    private $finishParse = false;

    public function __construct($filePath, $symbol = '@wkuai')
    {
        $this->path = $filePath;
        $this->symbol = $symbol;
    }

    /**
     * 从文件中提取函数数据
     *
     * @return array | null
     */
    public function getFunctionData()
    {
        if (!$this->finishParse) {
            $this->parseDataFromFile();
        }

        if ($this->isEmpty) {
            return null;
        }

        return [
            'title' => $this->classTitle,
            'pakege_name' => $this->namespace . "\\" . $this->className,
            'functions' => $this->methods
        ];
    }

    /**
     * 解析文件中解析出函数的配置
     *
     * @return void
     */
    private function parseDataFromFile()
    {
        $fileContent = file_get_contents($this->path);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $stmts = $parser->parse($fileContent);

        foreach ($stmts as $stmt) {
            $type = $stmt->getType();
            if ($type == 'Stmt_Namespace') {
                $this->namespace = $this->getNamespaceFromName($stmt->name);
                $this->parseNamespaceStmt($stmt->stmts);
            }
        }

        if (!empty($this->methods)) {
            $this->isEmpty = false;
        }

        $this->finishParse = true;
    }

    /**
     * 解析命名空间类的stmts
     *
     * @param \PhpParser\Node\Stmt[] $stmts
     * @return void
     */
    private function parseNamespaceStmt($stmts)
    {
        foreach ($stmts as $stmt) {
            $type = $stmt->getType();

            if ($type == 'Stmt_Class') {
                $this->className = $this->getClassNameFromIdentifier($stmt->name);
                $classComments = $stmt->getComments();
                
                if (empty($classComments)) {
                    $this->classTitle = $this->className;
                } else {
                    $this->classTitle = $this->getClassTitleFromComment($classComments[0]);
                }

                $this->methods = array_merge($this->methods, $this->getMethods($stmt->stmts));
            }
        }
    }

    /**
     * 获取命名空间
     *
     * @param Name $name
     * @return str
     */
    private function getNamespaceFromName(Name $name)
    {
        return implode("\\", $name->parts); 
    }

    /**
     * 获取类名
     *
     * @param Identifier $id
     * @return str
     */
    private function getClassNameFromIdentifier(Identifier $id)
    {
        return $id->name;
    }

    /**
     * 获取类名中的注释信息
     *
     * @param Comment $comment
     * @return str
     */
    private function getClassTitleFromComment(Comment $comment)
    {
        $commentText = $comment->getText();

        $line = $this->parseDoc($commentText);
        $items = $this->parseLineBySpace($line[0]);
        return $items[1];
    }

    /**
     * 获取函数列表
     *
     * @param \PhpParser\Node\Stmt[] $stmts
     * @return ClassMethod[]
     */
    private function getMethods($stmts)
    {
        $methods = [];
        /** @var ClassMethod  */
        foreach ($stmts as $stmt) {
            $type = $stmt->getType();

            if ($type === 'Stmt_ClassMethod') {
                if (!$stmt->isPublic() || $stmt->isMagic()) {
                    continue ;
                } else {
                    if ($method = $this->parserMethod($stmt)) {
                        $methods[] = $method;
                    }
                }
            }
        }

        return $methods;
    }

    /**
     * 解析函数
     *
     * @param ClassMethod $methods
     * @return array|null
     */
    private function parserMethod(ClassMethod $method)
    {
        $comments = $method->getComments();

        if (empty($comments)) {
            return null;
        }

        // 在没有含标识的情况下，函数无效
        $commentText = $comments[0]->getText();
        if (!$this->hasSymbol($commentText)) {
            return null;
        }

        $docArr = $this->parseDoc($commentText);
        $methodName = $method->name->name;

        $paramsDocMap = [];
        foreach ($docArr as $doc) {
            $items = $this->parseLineBySpace($doc);
            if ($items[0] == '@param') {
                if (mb_strpos($items[2], '$') == 0) {
                    $name = substr($items[2], 1);
                } else {
                    $name = $items[2];
                }

                $paramsDocMap[$name] = [
                    'type' => $items[1] ?? '',
                    'title' => $items[3] ?? ''
                ];
            } elseif($items[0] == '@return') {
                $returnParams[] = [
                    'type' =>$items[1] ?? '',
                    'name' =>  $items[2] ?? '',
                    'title' => $items[3] ?? ''
                ];
            } elseif ($items[0] == '@title') {
                $title = $items[1] ?? $methodName;
            } else {
                continue ;
            }
        }

        $params = [];
        foreach ($method->params as $param) {
            $paramName = $param->var->name;
            if (isset($paramsDocMap[$paramName])) {
                $params[] = array_merge(['name' => $paramName], $paramsDocMap[$paramName]);
            } else {
                $params[] = [
                    'name' => $paramName,
                    'type' => null,
                    'title' => $paramName
                ];
            }
        }

        return [
            'title' => $title ?? $methodName,
            'name' => $methodName,
            'param' => $params,
            'return_param' => $returnParams
        ];
    }

    /**
     * 是否函数指定标志符号
     *
     * @param string $commentText 注释
     * @return boolean
     */
    private function hasSymbol($commentText)
    {
        if (mb_strpos($commentText, $this->symbol) !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 解析单行注释
     *
     * @author Hongrui Luo <luohongrui@honraytech.com>
     * @param string $line 单行注释
     * @return void
     */
    private function parseLineBySpace($line)
    {
        $items = explode(' ', $line);

        return $this->trimArray($items);                
    }

    /**
     * 将注释分解为单行信息
     *
     * @param string $docStr 
     * @return string[]
     */
    private function parseDoc($docStr)
    {
        $replace = ['/*', '/'];
        $doc = str_replace($replace, '', $docStr);
        $pattern = '/(?<=\*)[\s\S]+?(?=\*)/';
        preg_match_all($pattern, $doc, $matches);

        $lines = $this->trimArray($matches[0]);
        return $lines;
    }

    /**
     * 过滤数据中的空元素
     *
     * @author Hongrui Luo <luohongrui@honraytech.com>
     * @param array $arr 原数据组
     * @return array 处理过后的数组
     */
    private function trimArray($arr)
    {
        $result = [];
        foreach($arr as $value) {
            $value = trim($value);
            if ($value != '') {
                $result[] = $value;
            }
        }

        return $result;
    }
}