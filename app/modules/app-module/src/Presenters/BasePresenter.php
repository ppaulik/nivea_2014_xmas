<?php

/**
 *
 * This file is part of the vanocni_soutez
 *
 * Copyright (c) 2014
 *
 * @file BasePresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;

use AppModule\Managers\UserManager;
use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;
use Tracy\Debugger;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
//class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @persistent */
    public $locale;

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var \WebLoader\LoaderFactory @injectOFF */
    public $webLoader;

    /** @var UserManager @inject */
    public $userManager;

    /** @var \Kdyby\Translation\Translator */
    protected $translator;

    /** @var \DK\Menu\UI\IControlFactory @inject */
    public $navigationFactory;

    /** @var string */
    protected $description = "";

    /** @var string */
    protected $keywords = "";


    protected function startup()
    {
        parent::startup();

        $user = $this->getUser();

        if (!$user->isAllowed($this->name, $this->action)) {
            // $this->flashMessage($message, 'warning');
            $this->redirect('Homepage:', array('backlink' => $this->storeRequest()));
        }

        $name   = explode(':', $this->getName());
        $action = ($this->action != 'default') ? ' ' . $this->action : null;

        $this->template->robots      = "index, follow";
        $this->template->description = $this->description;
        $this->template->keywords    = $this->keywords;
//        $this->template->title       = "";
        $this->template->name        = Strings::lower($name[1] . $action);

        $this->template->googleAnalytics = (!Debugger::$productionMode)
            ? false
            : true;
    }


    /**
     * @param \Kdyby\Translation\Translator $translator
     *
     * @return void
     */
    public function injectTranslator(\Kdyby\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * @return \Kdyby\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }


    /** @return CssLoader */
    protected function createComponentCss()
    {
        return $this->webLoader->createCssLoader('default');
    }

    /** @return JavaScriptLoader */
    protected function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('default');
    }


    public function actionLogoff()
    {
        $this->getUser()->logout();
        $this->redirect('Homepage:');
    }

    private function _saveUserVisitedInfo()
    {
        if (Debugger::$productionMode && !$this->isAjax()) {
            $this->accessRepository->add();
        }
    }


    /**
     * @return \DK\Menu\UI\Control
     */
    protected function createComponentNavigation()
    {
        return $this->navigationFactory->create();
    }


}
