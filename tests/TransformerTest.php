<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Sportuondo\Eralda\Tests\Author;
use Sportuondo\Eralda\Tests\AuthorArrayTransformer;
use Sportuondo\Eralda\Tests\Book;
use Sportuondo\Eralda\Tests\BookArrayTransformer;

final class TransformerTest extends TestCase
{
    /** @var Author $tolkien */
    protected $tolkien;

    protected function setUp()
    {
        parent::setUp();

        $this->tolkien = new Author();
        $this->tolkien->id = 1;
        $this->tolkien->name = 'J.R.R. Tolkien';
        $this->tolkien->isFreelance = false;
        $this->tolkien->birthDate = Carbon::createFromFormat('Y-m-d', '1892-01-03');

        $hobbit = new Book();
        $hobbit->id = 1;
        $hobbit->author = $this->tolkien;
        $hobbit->title = 'The Hobbit';
        $hobbit->year = 1937;

        $lotr = new Book();
        $lotr->id = 2;
        $lotr->author = $this->tolkien;
        $lotr->title = 'The Lord of the Rings';
        $lotr->year = 1968;

        $this->tolkien->books = [
            $hobbit,
            $lotr
        ];
    }

    public function testGeneratesKeyMappedArray(): void
    {
        $authorTransformer = new AuthorArrayTransformer();
        $authorArray = $authorTransformer->transformItem($this->tolkien);

        $this->assertArrayHasKey('is_freelance', $authorArray);
    }

    public function testPresentsFormattedKey(): void
    {
        $authorTransformer = new AuthorArrayTransformer();
        $authorArray = $authorTransformer->transformItem($this->tolkien);

        $this->assertEquals('1892-01-03', $authorArray['birth_date']);
    }

    public function testEmbedsRelatedItems(): void
    {
        $authorTransformer = new AuthorArrayTransformer();
        $authorArray = $authorTransformer->transformItem($this->tolkien);

        $this->assertIsArray($authorArray['books']);
        $this->assertArrayHasKey('books', $authorArray);
        $this->assertArrayHasKey('id', $authorArray['books'][0]);
        $this->assertArrayHasKey('title', $authorArray['books'][0]);
    }

    public function testGeneratesArrayFromCollection(): void
    {
        $bookArrayTransformer = new BookArrayTransformer();
        $booksArray = $bookArrayTransformer->transformCollection($this->tolkien->books);

        $this->assertIsArray($booksArray);
        $this->assertEquals(2, count($booksArray));
        $this->assertArrayHasKey('id', $booksArray[0]);
        $this->assertArrayHasKey('title', $booksArray[0]);
    }

    public function testTransformItemThrowsWhenNullParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $authorTransformer = new AuthorArrayTransformer();
        $authorArray = $authorTransformer->transformItem(null);
    }
}