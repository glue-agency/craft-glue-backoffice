<?php

namespace GlueAgency\Backoffice\dto;

/**
 * A Composer package the project requires directly. Treat as read-only.
 */
class Package
{

    protected const DESCRIPTION_MAX_LENGTH = 255;

    /**
     * @var string The package name, vendor included.
     */
    public string $name = '';

    /**
     * @var string The version installed.
     */
    public string $version = '';

    /**
     * @var string The package's own description.
     */
    public string $description = '';

    public function __construct(string $name, string $version, string $description)
    {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
    }

    public function getTruncatedDescription(): string
    {
        return mb_substr($this->description, 0, static::DESCRIPTION_MAX_LENGTH);
    }
}
