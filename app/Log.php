<?php

namespace App;

use App\Http\Controllers\WarehouseController;
use App\Storage\StorageDriverInterface;
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
    /** @var StorageDriverInterface */
    private $StorageDriver;

    /** $container array contains all data from request */
    public $container;

    /** $site */
    private $site;

    /** $allowed_fields array  */
    protected $allowed_fields = [
        'identifier' => 0,
        'log_name' => 1,
        'level' => 2,
        'content' => 3,
        'site' => 4
    ];

    public function __construct(StorageDriverInterface $StorageDriver)
    {
        $this->StorageDriver = $StorageDriver;
    }

    /**
     * @param $request
     * @return bool
     */
    public function validate($request)
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

        $this->container = $request;

        if ($this->validateAllowedFields() === false) {
            return false;
        }

        $this->StorageDriver->defineTable($this->container['site']);
        $this->site = $this->container['site'];

        $data_created = new \DateTime();
        $this->container = [
            'data_created' => $data_created->format('Y-m-d H:i:s'),
            'level' => WarehouseController::validateLevel($this->container['level']),
            'log_name' => $this->container['log_name'],
            'identifier' => isset($this->container['identifier']) ? $this->container['identifier'] : 'none',
            'content' => $this->container['content']
        ];

        return true;
    }

    /**
     * Validade if has only allowed fields
     * @return bool
     */
    private function validateAllowedFields()
    {
        if (count(array_diff_key($this->container, $this->allowed_fields)) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Create log on database
     * @return bool
     */
    public function make()
    {
        if ($this->verifyExistence() === true) {
            return false;
        }

        Notification::Slack(array_merge($this->container, ['site' => $this->site]));

        $this->StorageDriver->insert($this->container);
    }

    /**
     * Verify Existence of content on database
     * @return bool return true if exists and false otherwise
     */
    private function verifyExistence()
    {
        if ($this->container['level'] == 3) {
            return false;
        }

        $filters = [
            'log_name' => $this->container['log_name'],
            'content' => $this->container['content'],
            'level' => $this->container['level']
        ];

        if (isset($this->container['identifier'])) {
            $filters['identifier'] = $this->container['identifier'];
        }

        $result_query = $this->StorageDriver->select($filters);

        if ($result_query == false) {
            return false;
        }

        $result_query = reset($result_query);

        $affected_rows = $this->StorageDriver->updateData(
            ['incidents' => ++$result_query->incidents],
            ['id' => $result_query->id]
        );

        return ($affected_rows > 0) ? true: false;
    }

    /**
     * Return request feita
     * @return array
     */
    public function returnRequest()
    {
        return $this->container;
    }
}
