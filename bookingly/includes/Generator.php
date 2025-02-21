<?php

namespace  Bookingly;


class Generator
{
    /**
     * Class initialize
     */
    function __construct()
    {
        new Admin\AdminGenerator();
        new Frontend\RnBHandler();
    }
}
