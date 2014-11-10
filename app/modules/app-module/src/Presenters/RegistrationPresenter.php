<?php

/**
 * This file is part of the vanocni_soutez
 * Copyright (c) 2014
 *
 * @file    RegistrationPresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;


use AppModule\Entities\UserEntity;
use AppModule\Forms\IRegistrationFormFactory;
use AppModule\Forms\RegistrationFormFactory;

class RegistrationPresenter extends BasePresenter
{

    /** @var IRegistrationFormFactory @inject */
    public $registrationFormFactory;

    /** @var UserEntity @inject */
    public $userEntity;


    public function actionDefault()
    {
        if ($this->getUser()->isLoggedIn()) {
            if ($user = $this->userManager->getUserDao()->find($this->getUser()->id)) {
                $this->userEntity = $user;
            }
        }

        /** @var $form RegistrationFormFactory */
        $form = $this['registrationForm'];

        if ($form->isSuccess()) {
            $this->userEntity = $this->userManager->getUserDao()->findOneBy(array('email' => $form->getValues()->email));
        }
    }


    protected function createComponentRegistrationForm()
    {
        $form   = $this->registrationFormFactory->create();
        $mapper = new \CmsModule\Doctrine\EntityFormMapper($this->em);

        $form->injectEntityMapper($mapper);
        $form->bindEntity($this->userEntity);
        return $form;
    }

}
