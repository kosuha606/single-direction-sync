<?php

namespace kosuha606\SingleDirectionSync\Test;

use kosuha606\SingleDirectionSync\SingleDirectionSinchronizatorProviderInterface;
use kosuha606\VirtualModel\Example\MemoryModelProvider;

class SingleDirectionSinchronizatorProvider extends MemoryModelProvider implements SingleDirectionSinchronizatorProviderInterface
{
    public $statistic = [];

    public function type()
    {
        return SingleDirectionSinchronizatorProviderInterface::class;
    }

    public function handleUpdate($models)
    {
        $this->statistic['update'] = count($models);
    }

    public function handleCreate($models)
    {
        $this->statistic['create'] = count($models);
    }

    public function handleDelete($models)
    {
        $this->statistic['delete'] = count($models);
    }

}