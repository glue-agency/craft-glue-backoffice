<?php

namespace GlueAgency\Backoffice\dto\concerns;

/**
 * For a DTO with a `$version`.
 */
trait HasVersion
{

    /**
     * @return string
     */
    public function getFormattedVersion()
    {
        if(! ctype_digit(substr($this->version, 0, 1))) {
            return $this->version;
        }

        return "v{$this->version}";
    }
}
