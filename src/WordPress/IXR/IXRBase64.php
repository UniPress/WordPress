<?php

namespace WordPress\IXR;

/**
 * IXRBase64
 *
 * @package IXR
 * @since 1.5
 */
class IXRBase64
{
    var $data;

    function IXR_Base64($data)
    {
        $this->data = $data;
    }

    function getXml()
    {
        return '<base64>'.base64_encode($this->data).'</base64>';
    }
}