<?php

namespace Parable\Tests\Components\Console;

class CommandTest extends \Parable\Tests\Base
{
    public function testTrueIsTrueTemporarilyToSkipWarningsForEmptyClassesBecauseTheyAreNotHelpful()
    {
        $this->assertTrue(true);
    }
}
