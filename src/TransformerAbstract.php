<?php

namespace Sportuondo\Eralda;


abstract class TransformerAbstract
{
    protected $keysMap = [];
    protected $embeds = [];

    abstract public function transformItem($item): array;

    abstract public function transformCollection(iterable $items): array;
}