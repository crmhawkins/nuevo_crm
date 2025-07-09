<?php


if (! function_exists('layoutConfig')) {
    function layoutConfig() {

        if (Request::is('modern-light-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.vlm');

        } else if (Request::is('modern-dark-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.vdm');

        } else if (Request::is('collapsible-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.cm');

        } else if (Request::is('horizontal-light-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.hlm');

        } else if (Request::is('horizontal-dark-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.hlm');

        }

        // RTL

        else if (Request::is('rtl/modern-light-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.vlm-rtl');

        } else if (Request::is('rtl/modern-dark-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.vdm-rtl');

        } else if (Request::is('rtl/collapsible-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.cm-rtl');

        } else if (Request::is('rtl/horizontal-light-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.hlm-rtl');

        } else if (Request::is('rtl/horizontal-dark-menu/*')) {

            $__getConfiguration = Config::get('app-config.layout.hdm-rtl');

        }



        // Login

        else if (Request::is('login')) {

            $__getConfiguration = Config::get('app-config.layout.vlm');

        } else {
            $__getConfiguration = Config::get('barebone-config.layout.bb');
        }

        return $__getConfiguration;
    }
}


if (!function_exists('getRouterValue')) {
    function getRouterValue() {

        if (Request::is('modern-light-menu/*')) {

            $__getRoutingValue = '/modern-light-menu';

        } else if (Request::is('modern-dark-menu/*')) {

            $__getRoutingValue = '/modern-dark-menu';

        } else if (Request::is('collapsible-menu/*')) {

            $__getRoutingValue = '/collapsible-menu';

        } else if (Request::is('horizontal-light-menu/*')) {

            $__getRoutingValue = '/horizontal-light-menu';

        } else if (Request::is('horizontal-dark-menu/*')) {

            $__getRoutingValue = '/horizontal-dark-menu';

        }

        // RTL

        else if (Request::is('rtl/modern-light-menu/*')) {

            $__getRoutingValue = '/rtl/modern-light-menu';

        } else if (Request::is('rtl/modern-dark-menu/*')) {

            $__getRoutingValue = '/rtl/modern-dark-menu';

        } else if (Request::is('rtl/collapsible-menu/*')) {

            $__getRoutingValue = '/rtl/collapsible-menu';

        } else if (Request::is('rtl/horizontal-light-menu/*')) {

            $__getRoutingValue = '/rtl/horizontal-light-menu';

        } else if (Request::is('rtl/horizontal-dark-menu/*')) {

            $__getRoutingValue = '/rtl/horizontal-dark-menu';

        }

        // Login

        else if (Request::is('login')) {

            $__getRoutingValue = '/modern-light-menu';

        } else {
            $__getRoutingValue = '';
        }


        return $__getRoutingValue;
    }
}

if (!function_exists('seconds_to_time')) {
    /**
     * Convierte segundos a formato HH:MM:SS
     */
    function seconds_to_time($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}

if (!function_exists('time_to_seconds')) {
    /**
     * Convierte tiempo en formato HH:MM:SS a segundos
     */
    function time_to_seconds($time)
    {
        if (!$time) return 0;

        $parts = explode(':', $time);
        if (count($parts) >= 3) {
            return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        }
        return 0;
    }
}
