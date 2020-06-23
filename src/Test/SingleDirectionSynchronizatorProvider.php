<?php

namespace kosuha606\SingleDirectionSync\Test;

use kosuha606\SingleDirectionSync\SingleDirectionSynchronizatorProviderInterface;
use kosuha606\VirtualModel\Example\MemoryModelProvider;

class SingleDirectionSynchronizatorProvider extends MemoryModelProvider implements SingleDirectionSynchronizatorProviderInterface
{
    public $statistic = [];

    public function type()
    {
        return SingleDirectionSynchronizatorProviderInterface::class;
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