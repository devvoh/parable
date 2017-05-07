<?php

namespace Parable\Tests\Components\Console\Command;

class HelpTest extends \Parable\Tests\Base
{
    public function testTrueIsTrueTemporarilyToSkipWarningsForEmptyClassesBecauseTheyAreNotHelpful()
    {
        $this->assertTrue(true);
    }
}
