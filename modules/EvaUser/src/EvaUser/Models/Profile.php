<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Profile extends Entities\Profiles
{
    public function beforeSave()
    {
        if(!$this->birthday) {
            $this->skipAttributes(array('birthday'));
        }
    }
}