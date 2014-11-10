<?php
/**
 *
 * This file is part of the vanocni_soutez
 *
 * Copyright (c) 2014
 *
 * @file QuizOneFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;

use Nette;


interface IQuizOneFormFactory
{
    /** @return QuizOneFormFactory */
    function create();
}


class QuizOneFormFactory extends BasicForm implements IQuizOneFormFactory
{

    public function __construct()
    {
        parent::__construct();
    }


    /** @return QuizOneFormFactory */
    function create()
    {
        $this->addRadioList('quizOne', '1', array(1 => 'a)', 2 => 'b)', 3 => 'c)'));

        $this->addSubmit('send', 'Pokračovat')->setAttribute('class', 'btn next');

        $this->onSuccess[] = array($this, 'formSubmitted');
        $this->getElementPrototype()->class = 'quiz-form';
    }


    /**
     * @param BasicForm $form
     */
    public function formSubmitted(BasicForm $form)
    {
        $presenter = $this->getPresenter();
        $values = $form->getValues();
        if (!$presenter->getUser()->isLoggedIn()) {
            $section = $presenter->getSession($this->section);
            $section->quizOne = $values['quizOne'];

        } else {
            $em = $this->getEntityMapper()->getEntityManager();
            $em->persist($form->entity);
            $em->flush();
        }

        $message = 'Thanks';
        $form->getPresenter()->flashMessage($presenter->translator->translate($message));
        $form->getPresenter()->redirect($form->getRedirect());
    }


}