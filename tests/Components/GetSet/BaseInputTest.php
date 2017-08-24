<?php

namespace Parable\Tests\Components\GetSet;

class BaseInputTest extends \Parable\Tests\Base
{
    public function testRawDataParsedProperly()
    {
        $put = $this->setRawSource();
        $this->assertSame("value-from-raw", $put->get("test"));
    }

    public function testJsonDataParsedProperly()
    {
        $put = $this->setJsonSource();
        $this->assertSame("value-from-json", $put->get("test"));
    }

    protected function setRawSource()
    {
        return $this->setSource(__DIR__ . "/Files/InputSourceRaw.txt");
    }

    protected function setJsonSource()
    {
        return $this->setSource(__DIR__ . "/Files/InputSourceJson.txt");
    }

    protected function setSource($path)
    {
        $put = new \Parable\GetSet\Put();
        $this->mockProperty($put, "inputSource", $path);
        $put->__construct();

        return $put;
    }
}
