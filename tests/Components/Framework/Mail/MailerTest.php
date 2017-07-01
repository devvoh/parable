<?php

namespace Parable\Tests\Components\Framework\Mail;

class MailerTest extends \Parable\Tests\Components\Framework\Base
{
    /** @var \Parable\Framework\Mail\Mailer */
    protected $mailer;

    protected function setUp()
    {
        parent::setUp();

        $this->mailer = \Parable\DI\Container::create(\Parable\Framework\Mail\Mailer::class);
    }

    public function testSetGetTemplateVariable()
    {
        $this->mailer->setTemplateVariable('3', 'three');
        $this->assertSame('three', $this->mailer->getTemplateVariable('3'));
    }

    public function testSetGetTemplateVariablesAsArray()
    {
        $this->mailer->setTemplateVariables([
            '1' => 'one',
            '2' => 'two',
        ]);
        $this->assertSame(
            [
                '1' => 'one',
                '2' => 'two',
            ],
            $this->mailer->getTemplateVariables()
        );
    }

    public function testResetAndResetMailDataRemovesTemplateVariables()
    {
        $this->mailer->setTemplateVariable('3', 'three');
        $this->assertSame('three', $this->mailer->getTemplateVariable('3'));

        $this->mailer->resetMailData();
        $this->assertNull($this->mailer->getTemplateVariable('3'));

        $this->mailer->setTemplateVariable('3', 'three');
        $this->assertSame('three', $this->mailer->getTemplateVariable('3'));

        $this->mailer->reset();
        $this->assertNull($this->mailer->getTemplateVariable('3'));
    }

    public function testLoadTemplate()
    {
        $this->mailer->loadTemplate("tests/TestTemplates/mailer_template.phtml");
        $this->assertSame("Mailer template is here!\n\nTemplateVariable:", $this->mailer->getBody());
    }
    
    public function testLoadTemplateThrowsExceptionWhenInvalidTemplatePassed()
    {
        $this->expectException(\Parable\Framework\Exception::class);
        $this->expectExceptionMessage("Email template '");

        $this->mailer->loadTemplate("tests/TestTemplates/mailer_template_noperz.phtml");
    }

    public function testLoadTemplateWithTemplateVariable()
    {
        $this->mailer->setTemplateVariable('key', 'set!');
        $this->mailer->loadTemplate("tests/TestTemplates/mailer_template.phtml");
        $this->assertSame("Mailer template is here!\n\nTemplateVariable: set!", $this->mailer->getBody());
    }
}
