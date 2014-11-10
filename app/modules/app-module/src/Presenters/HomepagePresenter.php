<?php
/**
 *
 * This file is part of the vanocni_soutez
 *
 * Copyright (c) 2014
 *
 * @file HomepagePresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;

use AppModule\Forms\ILoginFormFactory;
use Nette;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    /** @var ILoginFormFactory @inject */
    public $loginFormFactory;

    /** @var \Kdyby\Facebook\Facebook */
    private $facebook;


    public function __construct(\Kdyby\Facebook\Facebook $facebook)
    {
        parent::__construct();
        $this->facebook    = $facebook;
    }


//    public function renderDefault()
//    {
//    }


    protected function createComponentLoginForm()
    {
        $form = $this->loginFormFactory->create();
//        $mapper = new \CmsModule\Doctrine\EntityFormMapper($this->em);

//        $form->injectEntityMapper($mapper);
//        $form->bindEntity($this->userEntity);
        return $form;
    }


    /** @return \Kdyby\Facebook\Dialog\LoginDialog */
    protected function createComponentFbLogin()
    {
        /** @var \Kdyby\Facebook\Dialog\LoginDialog $dialog */
        $dialog = $this->facebook->createDialog('login');

        $dialog->onResponse[] = function (\Kdyby\Facebook\Dialog\LoginDialog $dialog) {
            $fb = $dialog->getFacebook();

            if (!$fb->getUser()) {
                $this->flashMessage($this->translator->translate("facebook authentication failed"));
                return;
            }

            try {
                $me = $fb->api('/me');

                if (!$existing = $this->userManager->findByFbIdOrEmail($fb->getUser(), isset($me['email']) ? $me['email'] : null)) {
                    $existing = $this->userManager->registerFromFacebook($fb->getUser(), $me);
                }

                $this->userManager->updateFacebookAccessToken($fb->getUser(), $fb->getAccessToken());

                /**
                 * Nette\Security\User accepts not only textual credentials,
                 * but even an identity instance!
                 */
                unset($existing['password']);
                $this->user->login(new \Nette\Security\Identity($existing['id'], $existing['role'], $existing));

            } catch (\Kdyby\Facebook\FacebookApiException $e) {
                \Tracy\Debugger::log($e, 'facebook');
                $this->flashMessage($this->translator->translate("facebook authentication failed hard"));

            } catch (\Kdyby\Facebook\InvalidArgumentException $e) {
                \Tracy\Debugger::log($e, 'facebook');
                $this->flashMessage($this->translator->translate($e->getMessage()));
            }

            $this->redirect('this');
        };

        return $dialog;
    }


}
