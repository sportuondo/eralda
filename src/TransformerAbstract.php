<?php

namespace Sportuondo\Eralda;


abstract class TransformerAbstract
{
    protected $itemKeysMap = [];
    protected $embeds = [];

    abstract protected function itemKeysMap(): array;

    abstract public function transformItem($item): array;

    abstract public function transformCollection($items): array;
}