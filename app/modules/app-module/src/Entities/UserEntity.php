<?php
/**
 * This file is part of the vanocni_soutez
 * Copyright (c) 2014
 *
 * @file    UserEntity.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Entities;

use CmsModule\Doctrine\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\BigIntType;
use Nette\Object;
use Nette\Utils\DateTime;

/**
 * Class UserEntity
 *
 * @ORM\Entity
 * @ORM\Table(name="user", indexes={
 * @ORM\Index(name="fbId_idx", columns={"fb_id"}),
 * })
 * @package AppModule\Entities
 */
class UserEntity extends Object
{
    use \CmsModule\Doctrine\Entities\IdentifiedEntityTrait;

    /**
     * @var QuestionEntity
     * @ORM\OneToOne(targetEntity="QuestionEntity", mappedBy="user", cascade={"persist"})
     */
    protected $questions;

    /**
     * @var BigIntType
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $fbId;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=64)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $firstname;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $lastname;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $gender;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $street = '';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $strno;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $zip = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $city = '';

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $day;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $month;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $year;

    /**
     * @var DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthday;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $accessToken;

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    protected $role = 'member';


    function __construct()
    {
        $this->birthday = new DateTime();
    }


    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param \Doctrine\DBAL\Types\BigIntType $fbId
     *
     * @return $this
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;
        return $this;
    }

    /**
     * @return \Doctrine\DBAL\Types\BigIntType
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param QuestionEntity $questions
     */
    public function setQuestions(QuestionEntity $questions)
    {
        $this->questions = $questions;
        $questions->setUser($this);

    }

    /**
     * @param $quiz
     * @param $answer
     *
     * @throws \CmsModule\Doctrine\InvalidArgumentException
     */
    public function addQuestion($quiz, $answer)
    {
        if (!isset($this->questions->$quiz)) {
            throw new InvalidArgumentException($quiz);
        }
        $this->questions->$quiz = $answer;
    }

    /**
     * @return \AppModule\Entities\QuestionEntity
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param \Nette\Utils\DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return \Nette\Utils\DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param int $day
     */
    public function setDay($day)
    {
        $this->day = $day;
        if (!$this->birthday) {
            $this->birthday = new DateTime();
        }

        $this->birthday->setDate(intval($this->year), intval($this->month), intval($this->day));
    }

    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param int $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
        if (!$this->birthday) {
            $this->birthday = new DateTime();
        }

        $this->birthday->setDate(intval($this->year), intval($this->month), intval($this->day));
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $strno
     */
    public function setStrno($strno)
    {
        $this->strno = $strno;
    }

    /**
     * @return string
     */
    public function getStrno()
    {
        return $this->strno;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
        if (!$this->birthday) {
            $this->birthday = new DateTime();
        }

        $this->birthday->setDate(intval($this->year), intval($this->month), intval($this->day));
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $accessToken
     *
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

}