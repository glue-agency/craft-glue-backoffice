<?php

namespace GlueAgency\Backoffice\dto;

/**
 * A Composer package the project requires directly. Treat as read-only.
 */
class Package
{

    const DESCRIPTION_MAX_LENGTH = 255;

    /**
     * @var string The package name, vendor included.
     */
    public $name = '';

    /**
     * @var string The version installed.
     */
    public $version = '';

    /**
     * @var string The package's own description.
     */
    public $description = '';

    /**
     * @param string $name
     * @param string $version
     * @param string $description
     */
    public function __construct($name, $version, $description)
    {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTruncatedDescription()
    {
        return mb_substr($this->description, 0, static::DESCRIPTION_MAX_LENGTH);
    }
}
