<?php

namespace Bookingly;

class Installer
{
    /**
     * Initialize class functions
     *
     * @return void
     */
    public function run()
    {
        $this->add_version();
    }

    /**
     * Store plugin information
     *
     * @return void
     */
    public function add_version()
    {
        $installed = get_option('bookingly_installed');

        if (!$installed) {
            update_option('bookingly_installed', time());
        }

        update_option('bookingly_version', BOOKINGLY_VERSION);
    }
}
