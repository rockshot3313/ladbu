<?php

namespace Ladbu\LaravelLadwireModule\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        $this->app['config']['app']['name'] = 'Test App';
        $this->app['config']['mail.from.address'] = 'test@example.com';
    }
}
