<?php

namespace AppModule\Security;

use AppModule\Managers\UserManager;
use CmsModule\Entities\UserEntity;
use Nette,
    Nette\Security\Passwords;


/**
 * Users management.
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
    const
        TABLE_NAME           = 'user',
        COLUMN_ID            = 'id',
        COLUMN_NAME          = 'email',
        COLUMN_ROLE          = 'role',
        COLUMN_PASSWORD_HASH = 'password';


    /** @var UserManager */
    private $userManager;


    function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }


    /**
     * Performs an authentication.
     *
     * @param array $credentials
     *
     * @throws \Nette\Security\AuthenticationException
     * @return Nette\Security\Identity
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        /** @var $row UserManager */
        $row = $this->userManager->findByLogin($username);

        if (!$row) {
            throw new Nette\Security\AuthenticationException('Neplatné přihlašovací údaje', self::IDENTITY_NOT_FOUND);

        } elseif ($username !== $row[self::COLUMN_NAME]) {
            throw new Nette\Security\AuthenticationException('Neplatné přihlašovací údaje', self::INVALID_CREDENTIAL);

//        } elseif (sha1($username . $password) !== $row[self::COLUMN_PASSWORD_HASH]) {
//            throw new Nette\Security\AuthenticationException('Neplatné přihlašovací údaje', self::INVALID_CREDENTIAL);
        }

        $arr = $row;
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
    }


    /**
     * Adds new user.
     *
     * @param  string
     * @param  string
     *
     * @return void
     */
    public function add($username, $password)
    {
        $this->database->table(self::TABLE_NAME)->insert(array(
            self::COLUMN_NAME          => $username,
            self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
        ));
    }


}
