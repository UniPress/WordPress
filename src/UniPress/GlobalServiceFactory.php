<?php

namespace UniPress;

class GlobalServiceFactory {
    public function create($class, $id)
    {
        $instance = new $class;
        $GLOBALS[$id] = $instance;

        return $instance;
    }
}
