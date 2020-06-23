<?php

use kosuha606\SingleDirectionSync\SingleDirectionSinchronizator;
use kosuha606\SingleDirectionSync\Test\SingleDirectionSinchronizatorProvider;
use kosuha606\VirtualModel\VirtualModelManager;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    public $data = [];

    /**
     * @throws Exception
     */
    public function setUp()
    {
        $this->data = require_once('data.php');
        VirtualModelManager::getInstance()->setProvider(new SingleDirectionSinchronizatorProvider());
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testFirst()
    {
        $synchronizator = new SingleDirectionSinchronizator(
            $this->data['existed'],
            $this->data['import'],
            'import_id',
            'checksum',
            ['id']
        );
        $synchronizator->run();
        $this->assertTrue(true);
    }
}