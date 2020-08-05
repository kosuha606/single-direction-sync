<?php

use PHPUnit\Framework\BaseTestListener;

class TestDurationListener extends BaseTestListener
{
    public function endTest(\PHPUnit\Framework\Test $test, $time)
    {
        $testName = str_pad($test->getName(), 40, '.');
        printf("%s took %s seconds.\n", $testName, $time);
    }
}