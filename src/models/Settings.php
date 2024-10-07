<?php

namespace GlueAgency\Backoffice\models;

use craft\base\Model;

/**
 * Glue Backoffice settings
 */
class Settings extends Model
{

    public ?string $url = null;
    public ?string $token = null;
}
