<?php
/**
 *
 * This file is part of the vanocni_soutez
 *
 * Copyright (c) 2014
 *
 * @file LoginFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;

use Nette\Application\UI\Form;
use Nette;


interface ILoginFormFactory
{
    /** @return LoginFormFactory */
    function create();
}


class LoginFormFactory extends Form implements ILoginFormFactory
{

    protected $redirect = 'Homepage:';

    public function __construct()
    {
        parent::__construct();
    }


    /** @return RegistrationFormFactory */
    function create()
    {
        $this->addText('email', 'e-mail')
            ->addRule(Form::EMAIL, 'Prosím, vyplňte email v platném formátu.')
            ->addRule(Form::FILLED, 'Vyplňte prosím e-mail');

        $this->addText('password', 'heslo');
//            ->addRule(Form::FILLED, 'Prosím, vyplňte vaše heslo.');

        $this->addSubmit('send', 'Pokračovat')->setAttribute('class', 'btn next');

        $this->onSuccess[] = array($this, 'formSubmitted');
        $this->getElementPrototype()->class = 'registration-form';
    }


    public function formSubmitted(Form $form)
    {
        try {
            $user = $this->getPresenter()->getUser();
            $user->setExpiration('6 month', TRUE);

            $user = $this->getPresenter()->getUser();
            $user->login($form['email']->value, $form['password']->value);

            $this->presenter->restoreRequest($this->presenter->backlink());
            $form->getPresenter()->redirect($this->redirect);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->getPresenter()->flashMessage($e->getMessage(), 'warning');
            $form->addError($e->getMessage());
        }

    }


}