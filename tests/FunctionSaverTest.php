<?php

use honray\FunctionSaver;
use PHPUnit\Framework\TestCase;

class FunctionSaveTest extends TestCase
{
    private $folder = '.wktest';

    protected function tearDown(): void
    {
        if (is_dir($this->folder)) {
            $this->deleteFolder($this->folder);
        }
    }

    private function deleteFolder($folder)
    {
        if(is_dir($folder)){
            $files = glob($folder . '*', GLOB_MARK);
    
            foreach( $files as $file ){
                $this->deleteFolder($file);      
            }
    
            if (is_dir($folder)) {
                rmdir($folder);
            }
        } elseif(is_file($folder)) {
            unlink($folder);  
        }
    }

    public function testSave()
    {
        $data = [
            'title' => 'testFile'
        ];

        $saver = new FunctionSaver($this->folder);
        $path = $saver->save($data);

        $this->assertTrue(file_exists($path));
    }
}