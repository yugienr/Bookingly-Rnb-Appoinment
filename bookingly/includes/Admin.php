<?php

namespace Bookingly;

class Admin
{
    /**
     * Class initialize
     */
    function __construct()
    {
        new Admin\Menu();
        new Admin\GlobalSettings();
        new Admin\LocalSettings();
    }
}
