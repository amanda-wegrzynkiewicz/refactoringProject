
<?php

use PHPUnit\Framework\TestCase;
use App\Helpers\FileReader;

class FileReaderTest extends TestCase
{
    protected $validTxtFilePath;
    protected $validJsonFilePath;
    protected $invalidFilePath;

    protected function setUp(): void
    {
        $this->validTxtFilePath = __DIR__ . '/test.txt';
        $this->validJsonFilePath = __DIR__ . '/test.json';
        $this->invalidFilePath = __DIR__ . '/test.invalid';

        file_put_contents($this->validTxtFilePath, "Line 1\nLine 2\nLine 3");

        file_put_contents($this->validJsonFilePath, json_encode(['key' => 'value']));
    }

    protected function tearDown(): void
    {
        @unlink($this->validTxtFilePath);
        @unlink($this->validJsonFilePath);
    }

    public function testConstructorThrowsExceptionForInvalidExtension()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File extension not allowed.');

        new FileReader($this->invalidFilePath);
    }

    public function testConstructorThrowsExceptionForUnreadableFile()
    {
        $unreadableFilePath = __DIR__ . '/unreadable.txt';
        file_put_contents($unreadableFilePath, 'content');
        chmod($unreadableFilePath, 0000);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File cannot be read.');

        try {
            new FileReader($unreadableFilePath);
        } finally {
            chmod($unreadableFilePath, 0644);
            unlink($unreadableFilePath);
        }
    }

    public function testConstructorThrowsExceptionForNonExistentFile()
    {
        $nonExistentFilePath = __DIR__ . '/nonexistent.txt';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not found');

        new FileReader($nonExistentFilePath);
    }

    public function testConstructorInitializesWithValidTxtFile()
    {
        $fileReader = new FileReader($this->validTxtFilePath);

        $this->assertInstanceOf(FileReader::class, $fileReader);
        $this->assertEquals($this->validTxtFilePath, $fileReader->filePath);
    }

    public function testConstructorInitializesWithValidJsonFile()
    {
        $fileReader = new FileReader($this->validJsonFilePath);

        $this->assertInstanceOf(FileReader::class, $fileReader);
        $this->assertEquals($this->validJsonFilePath, $fileReader->filePath);
    }
}
