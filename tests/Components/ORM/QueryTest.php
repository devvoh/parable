<?php

namespace Parable\Tests\Components\ORM;

class QueryTest extends \Parable\Tests\Components\ORM\Base
{
    public function testTrueIsTrueTemporarilyToSkipWarningsForEmptyClassesBecauseTheyAreNotHelpful()
    {
        $this->assertTrue(true);
    }
}
