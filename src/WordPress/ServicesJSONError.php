<?php

namespace WordPress;

if (class_exists('PEAR_Error')) {

    class ServicesJSONError extends PEAR_Error
    {
        function Services_JSON_Error($message = 'unknown error', $code = null,
            $mode = null, $options = null, $userinfo = null)
        {
            parent::PEAR_Error($message, $code, $mode, $options, $userinfo);
        }
    }

} else {

    /**
     * @todo Ultimately, this class shall be descended from PEAR_Error
     */
    class ServicesJSONError
    {
        function Services_JSON_Error($message = 'unknown error', $code = null,
            $mode = null, $options = null, $userinfo = null)
        {

        }
    }

}