<?php

namespace App\Storage;

interface StorageDriverInterface
{
    public function defineTable($table_name);

    public function insert(array $content);

    public function select(array $filters, array $dados = null);
}