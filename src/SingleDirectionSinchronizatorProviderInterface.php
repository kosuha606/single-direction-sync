<?php

namespace kosuha606\SingleDirectionSync;

/**
 * This interface must be implemented by your concrete provider.
 * For storing data in right way.
 */
interface SingleDirectionSinchronizatorProviderInterface
{
    public function handleUpdate($models);

    public function handleCreate($models);

    public function handleDelete($models);
}