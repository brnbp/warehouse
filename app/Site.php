<?php

namespace App;

use App\Http\Controllers\WarehouseController;
use ConnectionsBaseDir\MysqlDB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class Site
 * Used to get method, so it return the filtered query
 * @package App
 */
class Site extends Model
{
    use MysqlDB;

    /** @var array $query_filters Default filter limit and order */
    private static $query_filters = [
        'limit' => 25,
        'order' => 'DESC'
    ];

    /** @var int $response_code http response code */
    private static $response_code = 200;

    /**
     * Get Log from source
     * @param string $ecommerce website resource
     * @return mixed|int|array return 404 if not found anything or the result query as array
     */
    public static function getLog($ecommerce)
    {
        self::defineTable($ecommerce);

        $result_query = self::select(self::$query_filters);

        if ($result_query == false) {
            self::$response_code = 404;
        }

        return $result_query;
    }

    /**
     * Get the last http response code set
     * @return int http code
     */
    public static function getHttpResponseCode()
    {
        return self::$response_code;
    }

    /**
     * Validate filters pass as query string
     * @return bool return true if filters is valid or false otherwise
     */
    public static function validateFilters()
    {
        if (!Request()->getQueryString()) {
            return true;
        }

        $filters = self::arrayFilter(self::getAssocArray(explode('&', Request()->getQueryString())));

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
            self::$query_filters = array_merge(self::$query_filters, $filters);
        }

        return true;
    }

    /**
     * Make assoc array
     * @param $parameter
     * @return array
     */
    private static function getAssocArray($parameter)
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
    private static function arrayFilter($array)
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
