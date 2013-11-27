<?php

namespace WordPress\IXR;

/**
 * IXRClientMulticall
 *
 * @package IXR
 * @since 1.5
 */
class IXRClientMulticall extends IXRClient
{
    var $calls = array();

    function IXR_ClientMulticall($server, $path = false, $port = 80)
    {
        parent::IXR_Client($server, $path, $port);
        $this->useragent = 'The Incutio XML-RPC PHP Library (multicall client)';
    }

    function addCall()
    {
        $args = func_get_args();
        $methodName = array_shift($args);
        $struct = array(
            'methodName' => $methodName,
            'params' => $args
        );
        $this->calls[] = $struct;
    }

    function query()
    {
        // Prepare multicall, then call the parent::query() method
        return parent::query('system.multicall', $this->calls);
    }
}