<?php

namespace HeurekaDeps;

use HeurekaDeps\Wpify\CustomFields\CustomFields;
if (!\function_exists('HeurekaDeps\\wpify_custom_fields')) {
    /**
     * Gets an instance of the WCF plugin
     *
     * @return CustomFields
     */
    function wpify_custom_fields() : CustomFields
    {
        static $plugin;
        if (empty($plugin)) {
            $plugin = new CustomFields();
        }
        return $plugin;
    }
}
