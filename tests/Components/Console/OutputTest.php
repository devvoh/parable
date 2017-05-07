<?php

namespace Parable\Tests\Components\Console;

class OutputTest extends \Parable\Tests\Base
{
    public function testTrueIsTrueTemporarilyToSkipWarningsForEmptyClassesBecauseTheyAreNotHelpful()
    {
        $this->assertTrue(true);
    }
}
