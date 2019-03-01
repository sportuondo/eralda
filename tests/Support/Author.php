<?php

namespace Sportuondo\Eralda\Tests;

use Carbon\Carbon;

class Author
{

    /** @var int $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var bool $isFreelance */
    public $isFreelance;

    /** @var Carbon $birthDate */
    public $birthDate;

    /** @var array $books */
    public $books;
}