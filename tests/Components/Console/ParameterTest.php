<?php

namespace Parable\Tests\Components\Console;

class ParameterTest extends \Parable\Tests\Base
{
    public function testTrueIsTrueTemporarilyToSkipWarningsForEmptyClassesBecauseTheyAreNotHelpful()
    {
        $this->assertTrue(true);
    }
}
