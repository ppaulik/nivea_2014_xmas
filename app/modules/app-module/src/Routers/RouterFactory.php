<?php

/**
 *
 * This file is part of the vanocni_soutez
 *
 * Copyright (c) 2014
 *
 * @file RouterFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Routers;

use Nette,
    Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;


/**
 * Router factory.
 */
class RouterFactory
{
    /** @var Nette\DI\Container */
    private $container;

    /** @var Nette\Caching\IStorage */
    private $cache;


    /** @var mixed[] */
    public $defaults = array(
        'website' => array(
            'name' => 'Presentation',
            'title' => '%n %s %t',
            'titleSeparator' => '|',
            'keywords' => '',
            'description' => '',
            'author' => '',
            'robots' => 'index, follow',
            'routePrefix' => '',
            'oneWayRoutePrefix' => '',
            'languages' =>  array('cs', 'en'),
            'defaultLanguage' => 'cs',
            'defaultPresenter' => 'Homepage',
            'errorPresenter' => 'Cms:Error',
            'layout' => '@cms/bootstrap',
            'cacheMode' => '',
            'cacheValue' => '10',
            'theme' => '',
        ),
    );


    function __construct(\Nette\DI\Container $container, \Nette\Caching\IStorage $cache)
    {
        $this->cache     = $cache;
        $this->container = $container;
    }


    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter()
    {
        $router   = new RouteList();
        $router[] = new Route('index.php', 'App:Homepage:default', Route::ONE_WAY);

        $router[] = $adminRouter = new RouteList('Cms');
        $adminRouter[] = new Route('admin/[<locale=cs cs|en>/]<presenter>/<action>[/<id>]', array(
            'presenter' => 'Dashboard',
            'action'    => 'default'
        ));

        $router[] = $frontRouter = new RouteList('App');

        $frontRouter[] = new Route('sitemap.xml', array(
            'presenter' => 'Sitemap',
            'action'    => 'sitemap',
        ));


        // detect prefix
        $prefix = $this->defaults['website']['routePrefix'];
        $languages = $this->defaults['website']['languages'];
        $mask = sprintf("[<locale=%s %s>/]<slug .+>[/<presenter>/<action>[/<id>]]", 'cs', 'cs|en');

        $frontRouter[] = new Route('[<locale=cs cs|en|hu>/]<presenter>/<action>[/<id>]', array(
                'presenter' => array(
                    Route::VALUE        => 'Homepage',
                    Route::FILTER_TABLE => array(
                        'svicky'     => 'Candles',
                        'otazka'     => 'Question',
                        'registrace' => 'Registration',
                    ),
                ),
                'action'    => 'default',
            )
        );

        return $router;
    }

}
