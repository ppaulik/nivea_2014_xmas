<?php
/**
 *
 * This file is part of the vanocni_soutez
 *
 * Copyright (c) 2014
 *
 * @file RegistrationFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;


use AppModule\Entities\QuestionEntity;
use AppModule\Entities\UserEntity;
use Nette\Forms\Form;
use Nette;


interface IRegistrationFormFactory
{
    /** @return RegistrationFormFactory */
    function create();
}


class RegistrationFormFactory extends BasicForm implements IRegistrationFormFactory
{

    protected $redirect = 'send';

    public function __construct()
    {
        parent::__construct();
    }


    /** @return RegistrationFormFactory */
    function create()
    {
        $this->addSelect('gender', 'pohlaví', array(0 => 'Muž)', 1 => 'Žena'))
            ->addRule(Form::FILLED, 'Prosím, zvolte vaše pohlaví.');

        $this->addText('firstname', 'jméno')
            ->addRule(Form::FILLED, 'Prosím, vyplňte vaše křestní jméno.');

        $this->addText('lastname', 'příjmení')
            ->addRule(Form::FILLED, 'Prosím, vyplňte vaše příjmení.');

        $this->addText('email', 'e-mail')
            ->addRule(Form::EMAIL, 'Prosím, vyplňte email v platném formátu.')
            ->addRule(Form::FILLED, 'Vyplňte prosím e-mail');

        $this->addText('street', 'ulice')
            ->addRule(Form::FILLED, 'Prosím, vyplňte vaši ulici.');

        $this->addText('strno', 'č.p.')
            ->addRule(Form::FILLED, 'Prosím, vyplňte vaše číslo popisné.');

        $this->addText('zip', 'PSČ')
            ->addRule(Form::FILLED, 'Prosím, vyplňte vaše PSČ.')
            ->addRule(Form::INTEGER, 'Prosím, vyplňte vaše PSČ v platném formátu.')
            ->addRule(Form::LENGTH, 'Prosím, vyplňte vaše PSČ v platném formátu.', 5);

        $this->addText('city', 'město')
            ->addRule(Form::FILLED, 'Prosím, vyplňte vaše město.');

        $days = array();
        foreach (range( 1, 31 ) as $index) {
            $days[$index] = $index;
        }
        $this->addSelect('day', 'den', $days)
            ->setPrompt('den')
            ->addRule(Form::FILLED, 'Prosím, zvolte den narození.');

        $month = array();
        foreach (range( 1, 12 ) as $index) {
            $month[$index] = $index;
        }
        $this->addSelect('month', 'měsíc', $month)
            ->setPrompt('měsíc')
            ->addRule(Form::FILLED, 'Prosím, zvolte měsíc narození.')
            ->addRule(Form::RANGE, 'Měsíc narození musí být v rozmezí %d', array(1,12));

        $currentYear = intval(date('Y'));
        $years = array();
        foreach (range( 1900, $currentYear ) as $index) {
            $years[$index] = $index;
        }
        $this->addSelect('year', 'rok', $years)
            ->setPrompt('rok')
            ->addRule(Form::FILLED, 'Prosím, zvolte rok narození.')
            ->addRule(Form::RANGE, 'Musíte být starší 16 let.', array(null, $currentYear - 16));

        $this->addCheckbox('privacy', 'Zaškrtnutím tohoto políčka a odesláním formuláře dobrovolně souhlasím, že poskytnuté údaje v rozsahu formuláře budou zpracovány pro účely vyhodnocení soutěže. V případě, že udělím souhlas k zasílání obchodních sdělení – informací e-mailem – viz. druhé políčku souhlasu, tak budou uvedené údaje zpracovány i pro účely stanovené v bodě 2. Politiky ochrany soukromí. Potvrzuji, že jsem si pečlivě pročetl/a podmínky zpracování údajů dle Politiky ochrany soukromí a Pravidla soutěže a souhlasím s nimi. Souhlas uděluji na dobu do jeho odvolání.')
            ->addRule(Form::FILLED, 'Prosím potvrďte souhlas s Politikou ochrany soukromí a Pravidly soutěže.');

        $this->addCheckbox('newsletter', 'Přeji si dostávat e-mailem informace o dalších akcích, soutěžích a novinkách společnosti Beiersdorf spol. s r.o.');

        $this->addSubmit('send', 'Pokračovat')->setAttribute('class', 'btn next');

        $this->onSuccess[] = array($this, 'formSubmitted');
        $this->getElementPrototype()->class = 'registration-form';
    }


    public function formSubmitted(BasicForm $form)
    {
        $presenter = $this->getPresenter();
        $section = $presenter->getSession($this->section);

        /** @var $entity UserEntity */
        $entity = $this->entity;

        if (($questions = $entity->questions) === NULL) {
            $questions = new QuestionEntity();
        }

        foreach ($section as $key => $val) {
            if (isset($questions->$key)) {
                $questions->$key = $val;
            }
        }

        $entity->setQuestions($questions);

        try {

            $em = $this->getEntityMapper()->getEntityManager();
            $em->persist($entity);
            $em->flush();

        } catch (\Kdyby\Doctrine\DuplicateEntryException $exc) {
            if (Nette\Utils\Strings::contains($exc->getMessage(), "1062")) {
                $message = 'Vámi zadaná emailová adresa již existuje';
                $form->getPresenter()->flashMessage($presenter->translator->translate($message));
                return;
            }

            throw new \Kdyby\Doctrine\DuplicateEntryException($exc);
        }

        $form->getPresenter()->redirect($form->getRedirect());
    }


}