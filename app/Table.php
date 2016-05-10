<?php

namespace App;

use App\Http\Controllers\WarehouseController;
use App\Storage\StorageDriverInterface;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Table
 * @package App
 */
class Table extends Model
{
    /** @var StorageDriverInterface */
    private $StorageDriver;

    public function __construct(StorageDriverInterface $StorageDriver)
    {
        $this->StorageDriver = $StorageDriver;
    }

    /**
     * Return request feita
     * @return array
     */
    public function returnAllTables()
    {
        return $this->StorageDriver->getTables();
    }
}
