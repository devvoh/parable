<?php

namespace Parable\Tests\Components\Framework;

class ViewTest extends \Parable\Tests\Components\Framework\Base
{
    /** @var \Parable\Framework\View */
    protected $view;

    protected function setUp()
    {
        parent::setUp();

        $this->view = \Parable\DI\Container::create(\Parable\Framework\View::class);
    }

    public function testGettingNonExistingMagicPropertyReturnsNull()
    {
        $this->assertNull($this->view->stuff);
    }

    public function testLoadingNonExistingTemplatePathIsSilent()
    {
        $this->view->setTemplatePath("stuff");
        $this->view->render();

        $this->assertEmpty($this->getActualOutput());
    }
}
