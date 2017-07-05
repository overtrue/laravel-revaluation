<?php

namespace Overtrue\LaravelRevaluation\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Overtrue\LaravelRevaluation\RevaluationServiceProvider;
use Overtrue\LaravelRevaluation\Valuators\RmbCent;

/**
 * Class HasRevaluableAttributesTest
 *
 * @author overtrue <i@overtrue.me>
 */
class HasRevaluableAttributesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $path = __DIR__.'/stubs/migrations';
        $this->app->afterResolving('migrator', function ($migrator) use ($path) {
            $migrator->path($path);
        });

        $this->artisan('migrate');
        $this->app[ConsoleKernel::class]->setArtisan(null);
    }

    protected function getPackageProviders($app)
    {
        return [RevaluationServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']['revaluation.default_valuator'] = RmbCent::class;
    }

    public function testBasicFeatures()
    {
        // database order
        $order = Order::find(1);

        $this->assertEquals(12345, $order->total);
        $this->assertEquals(1234500, $order->getOriginal('total'));

        // mutator getter
        $this->assertEquals(1234500, $order->raw_total);
        $this->assertInstanceOf(RmbCent::class, $order->revaluated_total);

        // new order
        $order = new Order([
            'total' => 100,
            'title' => 'test order.',
            'postage' => 20,
            'paid_in' => 120,
        ]);
        $order->save();

        $this->assertEquals(100, $order->total);
        $this->assertEquals(20, $order->postage);
        $this->assertEquals(120, $order->paid_in);

        // toArray
        $array = $order->toArray();

        $this->assertArrayHasKey('revaluated_total', $array);
        $this->assertArrayHasKey('revaluated_postage', $array);
        $this->assertArrayHasKey('revaluated_paid_in', $array);

        $this->assertEquals(100, $array['revaluated_total']);
        $this->assertEquals(20, $array['revaluated_postage']);
        $this->assertEquals(120, $array['revaluated_paid_in']);
    }

    public function testCustomPrefix()
    {
        $order = OrderWithCustomPrefix::find(1);

        $this->assertInstanceOf(RmbCent::class, $order->display_total);

        $array = $order->toArray();
        $this->assertArrayHasKey('display_total', $array);
        $this->assertEquals(12345, $array['display_total']);
    }

    public function testToArray()
    {
        $order = OrderWithToArrayFalse::find(1);

        $this->assertEquals(12345, $order->total);
        $this->assertInstanceOf(RmbCent::class, $order->revaluated_total);

        $array = $order->toArray();
        $this->assertArrayNotHasKey('revaluated_total', $array);
        $this->assertEquals(1234500, $array['total']);
        $this->assertEquals(1234500, $order->raw_total);
    }

    public function testReplaceToArray()
    {
        $order = OrderWithReplaceToArray::find(1);

        $this->assertEquals(12345, $order->total);
        $this->assertInstanceOf(RmbCent::class, $order->revaluated_total);

        $array = $order->toArray();
        $this->assertArrayNotHasKey('revaluated_total', $array);
        $this->assertEquals(12345, $array['total']);
    }
}