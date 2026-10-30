<?php

namespace GlueAgency\Backoffice\dto;

use GlueAgency\Backoffice\dto\concerns\HasVersion;

/**
 * An installed Craft plugin. Treat as read-only.
 */
class Plugin
{

    use HasVersion;

    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $handle = '';

    /**
     * @var string
     */
    public string $version = '';

    /**
     * @var string|null
     */
    public ?string $developer = null;

    /**
     * @var string|null The uid Craft stores the plugin under.
     */
    public ?string $uid = null;

    public function __construct(string $name, string $handle, string $version, ?string $developer, ?string $uid)
    {
        $this->name = $name;
        $this->handle = $handle;
        $this->version = $version;
        $this->developer = $developer;
        $this->uid = $uid;
    }
}
