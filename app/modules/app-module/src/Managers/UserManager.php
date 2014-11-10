<?php
/**
 * This file is part of the vanocni_soutez
 * Copyright (c) 2014
 *
 * @file    UserManager.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Managers;

use AppModule\Entities\QuestionEntity;
use AppModule\Entities\UserEntity;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Facebook\InvalidArgumentException;

class UserManager
{

    /** @var EntityDao|UserEntity */
    private $userDao;

    /** @var EntityDao|QuestionEntity */
    private $questionDao;

    /** @var \Kdyby\Translation\Translator */
    private $translator;


    function __construct(EntityDao $userDao, EntityDao $questionsDao, \Kdyby\Translation\Translator $translator)
    {
        $this->userDao     = $userDao;
        $this->questionDao = $questionsDao;
        $this->translator  = $translator;
    }

    /**
     * @return \Kdyby\Doctrine\EntityDao
     */
    public function getUserDao()
    {
        return $this->userDao;
    }

    /**
     * @return \AppModule\Entities\QuestionEntity|\Kdyby\Doctrine\EntityDao
     */
    public function getQuestionDao()
    {
        return $this->questionDao;
    }

    public function findByLogin($email)
    {
        return $this->userDao->createQueryBuilder('e')
            ->where("e.email = :username")
            ->setParameter('username', $email)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findByFbIdOrEmail($fbId, $email = null)
    {
        return $this->userDao->createQueryBuilder('e')
            ->orWhere("e.fbId = :fbId")
            ->orWhere('e.email = :email')
            ->setParameter('fbId', $fbId)
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }


    /**
     * @param $fbId
     * @param $me
     *
     * @return array
     * @throws \Kdyby\Facebook\InvalidArgumentException
     */
    public function registerFromFacebook($fbId, $me)
    {
        if (!isset($me['email'])) {
            throw new InvalidArgumentException($this->translator->translate("email from facebook account not found"));
        }

        /** @var $entity UserEntity */
        if (!$entity = $this->userDao->findOneBy(array('email' => $me['email']))) {
            $entity = new UserEntity();
        }

        $entity
            ->setFbId($fbId)
            ->setEmail($me['email'])
            ->setFirstname($me['first_name'])
            ->setLastname($me['last_name']);

        return $this->userDao->save($entity);
    }


    /**
     * @param $fbId
     * @param $accessToken
     *
     * @return array
     * @throws \Kdyby\Facebook\InvalidArgumentException
     */
    public function updateFacebookAccessToken($fbId, $accessToken)
    {
        /** @var $entity UserEntity */
        if (!$entity = $this->userDao->findOneBy(array('fbId' => $fbId))) {
            throw new InvalidArgumentException($this->translator->translate("facebook account not found"));
        }

        return $this->userDao->save($entity->setAccessToken($accessToken));
    }

}