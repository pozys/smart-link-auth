<?php

namespace Tests;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected function baseRoute(): string
    {
        return 'api';
    }
}
