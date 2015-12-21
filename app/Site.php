<?php

namespace App;

use App\Http\Controllers\WarehouseController;
use App\Storage\StorageDriverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class Site
 * Used to get method, so it return the filtered query
 * @package App
 */
class Site extends Model
{
    /** @var StorageDriverInterface */
    private $storageDriver;

    /** @var array $query_filters Default filter limit and order */
    private $query_filters = [
        'limit' => 25,
        'order' => 'DESC'
    ];

    /** @var int $response_code http response code */
    private $response_code = 200;

    public function __construct(StorageDriverInterface $driver)
    {
        $this->storageDriver = $driver;
    }

    /**
     * Get Log from source
     * @param string $ecommerce website resource
     * @return mixed|int|array return 404 if not found anything or the result query as array
     */
    public function getLog($ecommerce)
    {
        $this->storageDriver->defineTable($ecommerce);

        $result_query = $this->storageDriver->select($this->query_filters);

        if ($result_query == false) {
            $this->response_code = 404;
        }

        return $result_query;
    }

    /**
     * Get the last http response code set
     * @return int http code
     */
    public function getHttpResponseCode()
    {
        return $this->response_code;
    }

    /**
     * Validate filters pass as query string
     * @return bool return true if filters is valid or false otherwise
     */
    public function validateFilters()
    {
        if (!Request()->getQueryString()) {
            return true;
        }

        $filters = $this->arrayFilter($this->getAssocArray(explode('&', Request()->getQueryString())));

        if (isset($filters['limit'])) {
            if (is_numeric($filters['limit']) == false) {
                return false;
            }

            if ($filters['limit'] > 500) {
                $filters['limit'] = 500;
            }
        }

        if (isset($filters['level'])) {
            if (WarehouseController::validateLevel($filters['level']) === false) {
                return false;
            }
            $filters['level'] = WarehouseController::validateLevel($filters['level']);
        }

        if (empty($filters) == false) {
            $this->query_filters = array_merge($this->query_filters, $filters);
        }

        return true;
    }

    /**
     * Make assoc array
     * @param $parameter
     * @return array
     */
    private function getAssocArray($parameter)
    {
        $master_filter = [];
        foreach ($parameter as $filtros) {
            $filtro = explode('=', $filtros);
            $master_filter[$filtro[0]] = $filtro[1];
        }

        return $master_filter;
    }

    /**
     * Verify if the filters on query string are valid
     * @param $array
     * @return array content valid filters
     */
    private function arrayFilter($array)
    {
        $filters_allowed = array_flip([
            'level',
            'log_name',
            'limit',
            'identifier',
            'order'
        ]);

        return array_intersect_key($array, $filters_allowed);
    }
}
