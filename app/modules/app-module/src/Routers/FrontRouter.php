<?php

namespace Flame\CMS\AngularModule\Router;

use Flame\Modules\Providers\IRouterProvider;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette;

class AngularRouter implements IRouterProvider
{

    /**
     * @return array
     */
    public function getRouteList()
    {
        $router   = new RouteList();
        $router[] = new Route('template/[<path .+>]', 'Template:default');
        return $router;
    }

    /**
     * Returns array of ServiceDefinition,
     * that will be appended to setup of router service
     *
     * @example https://github.com/nette/sandbox/blob/master/app/router/RouterFactory.php - createRouter()
     * @return \Nette\Application\IRouter
     */
    public function getRoutesDefinition()
    {
        return array(
            array('Nette\Application\Routers\Route' => array('/', array(
                'module'    => 'Enlan',
                'presenter' => 'Homepage',
                'action'    => 'default'
            ))),
            array('AdamStipak\RestRoute' => array('Api:V1', 'json', true))
        );
    }
}