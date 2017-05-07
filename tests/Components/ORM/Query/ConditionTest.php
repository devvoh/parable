<?php

namespace Parable\Tests\Components\ORM\Query;

class ConditionTest extends \Parable\Tests\Components\ORM\Base
{
    public function testTrueIsTrueTemporarilyToSkipWarningsForEmptyClassesBecauseTheyAreNotHelpful()
    {
        $this->assertTrue(true);
    }
}
