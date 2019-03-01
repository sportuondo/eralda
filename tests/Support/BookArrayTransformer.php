<?php

namespace Sportuondo\Eralda\Tests;


use Sportuondo\Eralda\ArrayTransformerAbstract;

class BookArrayTransformer extends ArrayTransformerAbstract
{
    protected $keysMap = [
        'id'     => 'id',
        'title'  => 'title',
        'author' => 'author',
        'year'   => 'year',
    ];

    protected function presentAuthor($book)
    {
        return $book->author->name;
    }
}