<?php

namespace AppModule\DI;


use Flame\Modules\Configurators\IPresenterMappingConfig;
use Flame\Modules\Providers\IPresenterMappingProvider;
use Nette\DI\CompilerExtension;


/**
 * @author Pavel Paulík
 */
class AppExtension extends CompilerExtension
    implements
    \Kdyby\Doctrine\DI\IEntityProvider,
//    \CmsModule\System\DI\IPresenterProvider
    IPresenterMappingProvider
//    IRouterProvider
{

    const TAG_ROUTE = 'devrun.route';


    /** @var mixed[] */
    public $defaults = array(
        'session' => array(),
        'website' => array(
            'routePrefix'      => '',
            'defaultPresenter' => 'App:Default',
            'authentication'   => array(
                'autologin'        => null,
                'autoregistration' => null,
            ),
            'theme'            => 'devrun/devrun',
        ),
        'paths'   => array(
            'publicDir' => '%wwwDir%/public',
            'dataDir'   => '%appDir%/data',
            'logDir'    => '%appDir%/../log',
        ),
    );


    /**
     * Processes configuration data. Intended to be overridden by descendant.
     *
     * @return void
     */
    public function loadConfiguration()
    {
        $this->compiler->parseServices(
            $this->getContainerBuilder(),
            $this->loadFromFile(dirname(dirname(__DIR__)) . '/Resources/config/config.neon')
        );

        $container = $this->getContainerBuilder();
        $config    = $this->getConfig($this->defaults);

        $presenter = explode(':', $config['website']['defaultPresenter']);


    }


    /**
     * Setup presenter mapping : ClassNameMask => PresenterNameMask
     *
     * @param IPresenterMappingConfig &$presenterMappingConfig
     *
     * @return void
     */
    public function setupPresenterMapping(IPresenterMappingConfig &$presenterMappingConfig)
    {
        $presenterMappingConfig->setMapping('App', 'AppModule\*Module\Presenters\*Presenter'); // example from cms module
//        $presenterMappingConfig->setMapping('App', 'AppModule\presenters\*Module\*Presenter');
    }

    /**
     * Returns associative array of Namespace => mapping definition
     *
     * @return array
     */
    function getEntityMappings()
    {

        return array(
            'AppModule\Entities' => dirname(__DIR__) . '*Entity.php',
        );
    }
}
