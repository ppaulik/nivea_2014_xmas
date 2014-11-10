<?php
/**
 *
 * This file is part of the nette-vanocni_soutez
 *
 * Copyright (c) 2014
 *
 * @created 7.11.14
 * @package QuestionPresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;

use AppModule\Entities\QuestionEntity;
use AppModule\Forms\IQuizOneFormFactory;
use AppModule\Forms\IQuizTwoFormFactory;
use AppModule\Forms\QuizOneFormFactory;
use AppModule\Managers\UserManager;
use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;
use Tracy\Debugger;

class QuestionPresenter extends BasePresenter
{

     /** @var IQuizOneFormFactory @inject */
    public $quizOneFormFactory;

     /** @var IQuizTwoFormFactory @inject */
    public $quizTwoFormFactory;

    /** @var QuestionEntity @inject */
    public $questionEntity;

    /** @var UserManager @inject */
    public $userManager;



    protected function startup()
    {
        parent::startup();
        if ($this->getUser()->isLoggedIn()) {
            if ($entity = $this->userManager->getQuestionDao()->findOneBy(array('user' => $this->getUser()->id))) {
                $this->questionEntity = $entity;
            }
        }
    }


    public function actionDefault()
    {
        $this->redirect('week1');
    }


    public function actionWeek1()
    {
        /** @var $form QuizOneFormFactory */
        $form = $this['questionOneForm'];

        if (!$this->getUser()->isLoggedIn()) {
            $form->setRedirect('Registration:');

        } else {
            $form->setRedirect('Candles:');
        }
    }


    public function actionWeek2()
    {
        /** @var $form QuizOneFormFactory */
        $form = $this['questionOneForm'];
        $form->setRedirect('Homepage:default');
    }






    protected function createComponentQuestionOneForm()
    {
        $form   = $this->quizOneFormFactory->create();
        $mapper = new \CmsModule\Doctrine\EntityFormMapper($this->em);

        $form->injectEntityMapper($mapper);
        $form->bindEntity($this->questionEntity);
        return $form;
    }

    protected function createComponentQuestionTwoForm()
    {
        $form   = $this->quizTwoFormFactory->create();
        $mapper = new \CmsModule\Doctrine\EntityFormMapper($this->em);

        $form->injectEntityMapper($mapper);
        $form->bindEntity($this->articleEntity);
        return $form;
    }


}
