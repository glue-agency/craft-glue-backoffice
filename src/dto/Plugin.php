<?php

namespace GlueAgency\Backoffice\dto;

/**
 * An installed Craft plugin. Treat as read-only.
 */
class Plugin
{

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $handle = '';

    /**
     * @var string
     */
    public $version = '';

    /**
     * @var string|null
     */
    public $developer;

    /**
     * @var string|null The uid Craft stores the plugin under.
     */
    public $uid;

    /**
     * @param string      $name
     * @param string      $handle
     * @param string      $version
     * @param string|null $developer
     * @param string|null $uid
     */
    public function __construct($name, $handle, $version, $developer, $uid)
    {
        $this->name = $name;
        $this->handle = $handle;
        $this->version = $version;
        $this->developer = $developer;
        $this->uid = $uid;
    }
}
