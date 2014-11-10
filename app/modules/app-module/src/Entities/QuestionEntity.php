<?php
/**
 * This file is part of the vanocni_soutez
 * Copyright (c) 2014
 *
 * @file    UserEntity.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette\Object;

/**
 * Class UserEntity
 *
 * @ORM\Entity
 * @ORM\Table(name="questions")
 * @package AppModule\Entities
 */
class QuestionEntity extends Object
{
    use \CmsModule\Doctrine\Entities\IdentifiedEntityTrait;

    /**
     * @var UserEntity
     * @ORM\OneToOne(targetEntity="UserEntity", inversedBy="questions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizOne;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizTwo;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizTree;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizFour;



    /**
     * @param string $quizFour
     */
    public function setQuizFour($quizFour)
    {
        $this->quizFour = $quizFour;
    }

    /**
     * @return string
     */
    public function getQuizFour()
    {
        return $this->quizFour;
    }

    /**
     * @param string $quizOne
     */
    public function setQuizOne($quizOne)
    {
        $this->quizOne = $quizOne;
    }

    /**
     * @return string
     */
    public function getQuizOne()
    {
        return $this->quizOne;
    }

    /**
     * @param string $quizTree
     */
    public function setQuizTree($quizTree)
    {
        $this->quizTree = $quizTree;
    }

    /**
     * @return string
     */
    public function getQuizTree()
    {
        return $this->quizTree;
    }

    /**
     * @param string $quizTwo
     */
    public function setQuizTwo($quizTwo)
    {
        $this->quizTwo = $quizTwo;
    }

    /**
     * @return string
     */
    public function getQuizTwo()
    {
        return $this->quizTwo;
    }

    /**
     * @param \AppModule\Entities\UserEntity $user
     */
    public function setUser(UserEntity $user)
    {
        $this->user = $user;
    }

    /**
     * @return \AppModule\Entities\UserEntity
     */
    public function getUser()
    {
        return $this->user;
    }


}