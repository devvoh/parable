<?php

namespace Parable\Tests\Components\Console;

class InputTest extends \Parable\Tests\Base
{
    public function testTrueIsTrueTemporarilyToSkipWarningsForEmptyClassesBecauseTheyAreNotHelpful()
    {
        $this->assertTrue(true);
    }
}
