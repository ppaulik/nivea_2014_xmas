<?php
/**
 * This file is part of the nette-vanocni_soutez
 * Copyright (c) 2014
 *
 * @created 7.11.14
 * @file    QuizTwoFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;

use Nette\Forms\Form;

interface IQuizTwoFormFactory
{

    /** @return QuizTwoFormFactory */
    function create();
}

class QuizTwoFormFactory extends Form
{
    public function __construct()
    {
        parent::__construct();
    }


    /** @return QuizTwoFormFactory */
    function create()
    {
        $this->addRadioList('question', null, array(0 => 'a)', 1 => 'b)', 2 => 'c)'));

        $this->addSubmit('send', 'Pokračovat')->setAttribute('class', 'btn next');

        $this->onSuccess[] = array($this, 'formSubmitted');
        $this->getElementPrototype()->class = 'quiz-form';


    }


    public function formSubmitted(Form $form)
    {
        die(dump($form->getValues()));

        $em = $this->getEntityMapper()->getEntityManager();
        $em->persist($form->entity);
        $em->flush();

        $message = $form->entity->id
            ? 'Stránka přidána'
            : 'Stránka upravena';

        $form->getPresenter()->flashMessage($message);
        $form->getPresenter()->redirect($form->getRedirect());

    }

} 