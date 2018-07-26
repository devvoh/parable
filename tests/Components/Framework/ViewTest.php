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

    public function testGettingNonExistingMagicPropertyThrowsException()
    {
        $this->expectException(\Parable\Framework\Exception::class);
        $this->expectExceptionMessage(
            "Could not find property 'stuff'. Make sure it was registered with the View."
        );

        $this->assertNull($this->view->stuff);
    }

    public function testLoadingNonExistingTemplatePathThrowsException()
    {
        // Due to the try/catch, if it succeeds, it will simply not test anything. Hence we use $asserted
        $asserted = false;

        try {
            $this->view->setTemplatePath('stuff/noexist.phtml');
            $this->view->render();
        } catch (\Parable\Framework\Exception $exception) {
            self::assertContains('Template file could not be loaded', $exception->getMessage());
            self::assertContains('stuff/noexist.phtml', $exception->getMessage());

            $asserted = true;
        }

        self::assertTrue($asserted, "The test case was not asserted, expected exception not thrown");
    }

    public function testAccessingExistingMagicPropertyWorks()
    {
        $this->assertInstanceOf(
            \Parable\Framework\Toolkit::class,
            $this->view->toolkit
        );
    }

    public function testRegisterCustomClassesWorks()
    {
        $this->view->registerClass("basic_test_class", \Parable\Tests\TestClasses\Basic::class);

        $this->assertInstanceOf(
            \Parable\Tests\TestClasses\Basic::class,
            $this->view->basic_test_class
        );
    }
}
