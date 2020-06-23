<?php

use kosuha606\SingleDirectionSync\SingleDirectionSinchronizator;
use kosuha606\SingleDirectionSync\Test\SingleDirectionSinchronizatorProvider;
use kosuha606\VirtualModel\VirtualModelManager;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    /** @var SingleDirectionSinchronizatorProvider */
    public $syncProvider;

    /**
     * @throws Exception
     */
    public function setUp()
    {
        $this->syncProvider = new SingleDirectionSinchronizatorProvider();
        VirtualModelManager::getInstance()->setProvider($this->syncProvider);
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testFirst()
    {
        $data = require('data.php');
        $synchronizator = new SingleDirectionSinchronizator(
            $data['first']['existed'],
            $data['first']['import'],
            ['product_id', 'size_value'],
            'checksum',
            ['product_id', 'size_value', 'comment']
        );
        $synchronizator->run();

        $this->assertEquals(1, $this->syncProvider->statistic['update']);
        $this->assertEquals(5, $this->syncProvider->statistic['create']);
        $this->assertEquals(0, $this->syncProvider->statistic['delete']);
    }

    /**
     * @throws Exception
     */
    public function testDelete()
    {
        $data = require('data.php');
        $synchronizator = new SingleDirectionSinchronizator(
            $data['delete']['existed'],
            $data['delete']['import'],
            ['product_id', 'size_value'],
            'checksum',
            ['product_id', 'size_value', 'comment']
        );
        $synchronizator->run();

        $this->assertEquals(1, $this->syncProvider->statistic['update']);
        $this->assertEquals(5, $this->syncProvider->statistic['create']);
        $this->assertEquals(1, $this->syncProvider->statistic['delete']);
    }

    /**
     * @throws Exception
     */
    public function testSkip()
    {
        $data = require('data.php');
        $synchronizator = new SingleDirectionSinchronizator(
            $data['skip']['existed'],
            $data['skip']['import'],
            ['product_id', 'size_value'],
            'checksum',
            ['product_id', 'size_value', 'comment']
        );
        $synchronizator->run();

        $this->assertEquals(0, $this->syncProvider->statistic['update']);
        $this->assertEquals(0, $this->syncProvider->statistic['create']);
        $this->assertEquals(0, $this->syncProvider->statistic['delete']);
    }
}