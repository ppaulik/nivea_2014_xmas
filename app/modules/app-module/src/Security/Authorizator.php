<?php
/**
 * This file is part of the vanocni_soutez
 * Copyright (c) 2014
 *
 * @file    Authorizator.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Security;


use Nette\Security\Permission;

class Authorizator extends Permission
{


    public function __construct()
    {
        // roles
        $this->addRole('guest');
        $this->addRole('member');
        $this->addRole('admin');

        // resources
        $this->addResource('App:Homepage');
        $this->addResource('App:Error');
        $this->addResource('App:Question');
        $this->addResource('App:Candles');
        $this->addResource('App:Registration');
        $this->addResource('Cms:User');

        // privileges quest
        $this->deny('guest', Permission::ALL);
        $this->allow('guest', 'App:Error', Permission::ALL);
        $this->allow('guest', 'App:Homepage', Permission::ALL);
        $this->allow('guest', 'App:Candles', Permission::ALL);
        $this->allow('guest', 'App:Registration', Permission::ALL);
        $this->allow('guest', 'App:Question', array('default', 'week1'));
        $this->allow('guest', 'Cms:User', 'login');

        $this->allow('member', Permission::ALL);

        // privileges admin
        $this->allow('admin', Permission::ALL, Permission::ALL);

    }
}

