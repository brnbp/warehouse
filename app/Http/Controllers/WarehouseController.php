<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Log;
use App\Site;
use App\Storage\StorageDriverInterface;
use App\Storage\MysqlStorage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


/**
 * Class WarehouseController
 * Determine with action will be executed from route requested
 * @package App\Http\Controllers
 */
class WarehouseController extends Controller
{
    /** @var array $levels level map */
    static public $levels = [
        'critical' => 1,
        'warning' => 2,
        'info' => 3
    ];

    /**
     * Make the authentication, the most simple possible
     * @return bool return true if allow or false if deny
     */
    protected function authenticate()
    {
        //$pass = md5(preg_replace("/[^a-z]+/", " ", $pass));
        return preg_replace("/[^a-z]+/", " ", Request()->header('auth', false)) == 'abs' ? true : false;
    }

    /**
     * Method responsible to obtain log
     * @param $ecommerce string website main resource
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Laravel\Lumen\Http\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function site($ecommerce)
    {
        if ($this->authenticate() === false) {
            return response('', 401);
        }

        $Site = App::make('StorageServiceSite');

        if ($Site->validateFilters() === false) {
            return response('', 400);
        }

        return response($Site->getLog($ecommerce), $Site->getHttpResponseCode(), [
           'Content-Type: application/json'
        ]);
    }

    /**
     * Method responsible to create log
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Laravel\Lumen\Http\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function log(Request $request)
    {
        if ($this->authenticate() === false) {
            return response('', 401);
        }

        $Log = App::make('StorageServiceLog');

        if ($Log->validate($request->all()) === false) {
            return response('', 400);
        }

        $Log->make();

        return response($Log->returnRequest(), 201, [
            'Content-Type: application/json'
        ]);
    }

    /**
     * Determina o level do log de acordo com a string fornecida
     * @param  string $level string contendo nivel do log
     */
    public static function validateLevel($level)
    {
        $level = trim(strtolower($level));

        if (array_key_exists($level, static::$levels)) {
            return static::$levels[$level];
        }

        return false;
    }
}
