<?php

namespace Sportuondo\Eralda\Tests;

use Sportuondo\Eralda\ArrayTransformerAbstract;

class AuthorArrayTransformer extends ArrayTransformerAbstract
{
    protected $embeds = [
        'books',
    ];

    protected $keysMap = [
        'id'          => 'id',
        'name'        => 'name',
        'isFreelance' => 'is_freelance',
        'birthDate'   => 'birth_date',
    ];

    protected function presentBirthDate($author)
    {
        return $author->birthDate->format('Y-m-d');
    }

    protected function embedBooks($author)
    {
        $bookTransformer = new BookArrayTransformer();
        return $bookTransformer->transformCollection($author->books);
    }
}