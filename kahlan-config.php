<?php

use Kahlan\Filter\Filter;
use Kahlan\Reporter\Coverage;

$args = $this->args();
$args->argument('spec', 'default', 'tests/kahlan');
$this->args()->set('exclude', ['Symfony', 'Doctrine']);

/**
 * Initializing a custom coverage reporter
 */
Filter::register('app.coverage', function ($chain) {
    $reporters = $this->reporters();

    if ($this->args()->exists('coverage')) {
        // Limit the Coverage analysis to only a couple of directories only
        $coverage = new Coverage([
            'verbosity' => $this->args()->get('coverage'),
            'driver'    => new Coverage\Driver\Xdebug(),
            'path'      => [
                __DIR__ . '/src',
            ],
            'exclude'   => [
                __DIR__ . '/src/*/Resources',
                __DIR__ . '/src/*/DataFixtures',

            ]
        ]);
        $reporters->add('coverage', $coverage);
    }

    return $reporters;
});

Filter::apply($this, 'coverage', 'app.coverage');
