<?php

namespace App;

use App\Http\Controllers\WarehouseController;
use ConnectionsBaseDir\MysqlDB;
use Cron\Tests\HoursFieldTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class Log
 * Create log from request
 * @package App
 */
class Log extends Model
{
    use MysqlDB;

    /** $container array contains all data from request */
    public static $container;

    /** $site */
    private static $site;

    /** $allowed_fields array  */
    protected static $allowed_fields = [
        'identifier' => 0,
        'log_name' => 1,
        'level' => 2,
        'content' => 3,
        'site' => 4
    ];

    /**
     * @param $request
     * @return bool
     */
    public static function validate($request)
    {
        $validate = Validator::make($request, [
            'log_name' => 'required|max:50',
            'level' => 'required|min:3|max:10',
            'content' => 'required',
            'site' => 'required|max:50'
        ]);

        if ($validate->fails()) {
            return false;
        }

        self::$container = $request;

        if (self::validateAllowedFields() === false) {
            return false;
        }

        self::defineTable(self::$container['site']);
        self::$site = self::$container['site'];

        $data_created = new \DateTime();
        self::$container = [
            'data_created' => $data_created->format('Y-m-d H:i:s'),
            'level' => WarehouseController::validateLevel(self::$container['level']),
            'log_name' => self::$container['log_name'],
            'identifier' => isset(self::$container['identifier']) ? self::$container['identifier'] : 'none',
            'content' => self::$container['content']
        ];

        return true;
    }

    /**
     * Validade if has only allowed fields
     * @return bool
     */
    private static function validateAllowedFields()
    {
        if (count(array_diff_key(self::$container, self::$allowed_fields)) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Create log on database
     * @return bool
     */
    public static function make()
    {
        if (self::verifyExistence() === true) {
            return false;
        }

        Notification::Slack(array_merge(self::$container, ['site' => self::$site]));

        self::insert(self::$container);
    }

    /**
     * Verify Existence of content on database
     * @return bool return true if exists and false otherwise
     */
    private static function verifyExistence()
    {
        if (self::$container['level'] == 3) {
            return false;
        }

        $filters = [
            'log_name' => self::$container['log_name'],
            'content' => self::$container['content'],
            'level' => self::$container['level']
        ];

        if (isset(self::$container['identifier'])) {
            $filters['identifier'] = self::$container['identifier'];
        }

        $result_query = self::select($filters);

        if ($result_query == false) {
            return false;
        }

        $result_query = reset($result_query);

        $affcted_rows = self::updateData(['incidents' => ++$result_query->incidents], ['id' => $result_query->id]);

        return ($affcted_rows > 0) ? true: false;
    }

    /**
     * Return request feita
     * @return array
     */
    public static function returnRequest()
    {
        return self::$container;
    }
}
