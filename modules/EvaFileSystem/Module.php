<?php

namespace Eva\EvaFileSystem;

use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Eva\EvaEngine\Module\StandardInterface;

class Module implements ModuleDefinitionInterface, StandardInterface
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'Eva\EvaFileSystem' => __DIR__ . '/src/EvaFileSystem',
        );
    }

    public static function registerGlobalEventListeners()
    {
    }

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {
        $dispatcher = $di->getDispatcher();
        $dispatcher->setDefaultNamespace('Eva\EvaFileSystem\Controllers');
    }

}
