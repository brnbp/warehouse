<?php

use App\Http\Controllers\WarehouseController as Warehouse;

class WarehouseTest extends TestCase
{
    public function testSiteExists()
    {
        $Warehouse = new Warehouse();
        $this->assertTrue(method_exists($Warehouse, 'site'), 'Class does not have method site');
    }

    public function testReturn200OnSiteResource()
    {
        $response = $this->call('GET', '/v1/site');
        $this->assertEquals(200, $response->status());
    }

    public function testReturn200onSiteResourceWithParameter()
    {
        $response = $this->call('GET', '/v1/site/titanis');
        $this->assertEquals(200, $response->status());
    }
}
