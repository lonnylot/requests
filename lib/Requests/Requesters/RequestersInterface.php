<?php

namespace Requests\Requesters;

interface RequestersInterface
{
    public static function isAvailable($method, $scheme);
    public function setMethod($method);
    public function setParams($preparedParams);
}