<?php

namespace Bookingly;

/**
 * Frontend class
 */
class Frontend
{
    /**
     * Initialize class
     */
    public function __construct()
    {
        new Frontend\TemplateHandler();
    }
}
