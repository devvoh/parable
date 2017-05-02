<?php

namespace Parable\Tests\Components\Mail;

class MailerTest extends \Parable\Tests\Base
{
    /** @var \Parable\Mail\Mailer|\PHPUnit_Framework_MockObject_MockObject */
    protected $mailer;

    protected function setUp()
    {
        parent::setUp();

        $this->mailer = $this->createPartialMock(\Parable\Mail\Mailer::class, ['sendMail']);
    }

    public function testSetFrom()
    {
        $this->mailer->setFrom('address@test.dev');
        $this->assertSame('address@test.dev', $this->mailer->getAddresses('from'));

        $this->mailer->resetSender();

        $this->mailer->setFrom('address@test.dev', 'name of user');
        $this->assertSame('name of user <address@test.dev>', $this->mailer->getAddresses('from'));
    }

    public function testAddTo()
    {
        $this->mailer->addTo('address@test.dev');
        $this->assertSame('address@test.dev', $this->mailer->getAddresses('to'));

        $this->mailer->resetRecipients();

        $this->mailer->addTo('address@test.dev', 'name of user');
        $this->assertSame('name of user <address@test.dev>', $this->mailer->getAddresses('to'));
    }

    public function testAddCc()
    {
        $this->mailer->addCc('address@test.dev');
        $this->assertSame('address@test.dev', $this->mailer->getAddresses('cc'));

        $this->mailer->resetRecipients();

        $this->mailer->addCc('address@test.dev', 'name of user');
        $this->assertSame('name of user <address@test.dev>', $this->mailer->getAddresses('cc'));
    }

    public function testAddBcc()
    {
        $this->mailer->addBcc('address@test.dev');
        $this->assertSame('address@test.dev', $this->mailer->getAddresses('bcc'));

        $this->mailer->resetRecipients();

        $this->mailer->addBcc('address@test.dev', 'name of user');
        $this->assertSame('name of user <address@test.dev>', $this->mailer->getAddresses('bcc'));
    }

    public function testSetSubject()
    {
        $this->mailer->setSubject('this is a subject');
        $this->assertSame('this is a subject', $this->mailer->getSubject());
    }

    public function testSetBody()
    {
        $this->mailer->setBody('this is a body');
        $this->assertSame('this is a body', $this->mailer->getBody());
    }

    public function testGetRequiredHeaders()
    {
        $this->assertSame(
            [
                "MIME-Version: 1.0",
                "Content-type: text/html; charset=UTF-8",
            ],
            $this->mailer->getRequiredHeaders()
        );
    }

    public function testGetAddAndResetHeaders()
    {
        $this->assertSame(
            [],
            $this->mailer->getHeaders()
        );

        $this->mailer->addheader("Thing: Something");

        $this->assertSame(
            [
                "Thing: Something"
            ],
            $this->mailer->getHeaders()
        );

        $this->mailer->resetMailData();

        $this->assertSame(
            [],
            $this->mailer->getHeaders()
        );
    }

    public function testReset()
    {
        $this->mailer->setFrom('somebody@test.dev');
        $this->mailer->setSubject('subject');
        $this->mailer->setBody('body');
        $this->mailer->addHeader('Something: Stuff');
        $this->mailer->addTo('to@test.dev');
        $this->mailer->addCc('cc@test.dev');
        $this->mailer->addBcc('bcc@test.dev');

        $this->mailer->reset();
        $this->mailer->resetSender();

        $this->assertEmpty($this->mailer->getSubject());
        $this->assertEmpty($this->mailer->getBody());
        $this->assertEmpty($this->mailer->getAddresses('from'));
        $this->assertEmpty($this->mailer->getAddresses('to'));
        $this->assertEmpty($this->mailer->getAddresses('cc'));
        $this->assertEmpty($this->mailer->getAddresses('bcc'));
        $this->assertEmpty($this->mailer->getHeaders());
        $this->assertEmpty($this->mailer->getHeaders());
    }

    public function testSend()
    {
        $this->mailer
            ->method('sendMail')
            ->withAnyParameters()
            ->willReturnCallback(function () {
                $arguments = func_get_args();

                $this->assertSame("to@test.dev", $arguments[0]);
                $this->assertSame("subject", $arguments[1]);
                $this->assertSame("body", $arguments[2]);

                // Headers contain \r\n line endings for compatibility sake. By manually cutting off the lines, we
                // add \n automatically, so we add \r for matching's sake.
                $this->assertSame(
"MIME-Version: 1.0\r
Content-type: text/html; charset=UTF-8\r
Something: Stuff\r
Cc: cc@test.dev\r
Bcc: bcc@test.dev\r
From: somebody@test.dev",
                    $arguments[3]
                );
            });

        $this->mailer->setFrom('somebody@test.dev');
        $this->mailer->setSubject('subject');
        $this->mailer->setBody('body');
        $this->mailer->addHeader('Something: Stuff');
        $this->mailer->addTo('to@test.dev');
        $this->mailer->addCc('cc@test.dev');
        $this->mailer->addBcc('bcc@test.dev');

        $this->mailer->send();
    }
}
