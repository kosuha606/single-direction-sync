<?php

namespace kosuha606\SingleDirectionSync\Test;

use kosuha606\SingleDirectionSync\SingleDirectionSinchronizatorProviderInterface;
use kosuha606\VirtualModel\Example\MemoryModelProvider;

class SingleDirectionSinchronizatorProvider extends MemoryModelProvider implements SingleDirectionSinchronizatorProviderInterface
{
    public function type()
    {
        return SingleDirectionSinchronizatorProviderInterface::class;
    }

    public function handleUpdate($models)
    {
        // TODO: Implement handleUpdate() method.
    }

    public function handleCreate($models)
    {
        // TODO: Implement handleCreate() method.
    }

    public function handleDelete($models)
    {
        // TODO: Implement handleDelete() method.
    }

}