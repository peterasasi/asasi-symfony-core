<?php

namespace Dbh\SfCoreBundle\Common;

use Symfony\Component\HttpFoundation\Request;

interface GetRequestInterface
{
    public function setRequest(Request $request);
}
