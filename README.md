# Dead simple object to array transformation library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sportuondo/eralda.svg?style=flat-square)](https://packagist.org/packages/sportuondo/eralda)

The objective of this library is to centralize the logic in charge of transforming objects (usually models) into arrays in order to expose them through JSON. Eralda can transform any type of object (PHP standard objects, Laravel Eloquent models...) into an array by just indicating the desired property mapping.

Eralda also offers the possibility of transforming property values as well as embedding related elements (e.g. author → books).

## Installation

You can install the package via composer:

```bash
composer require sportuondo/eralda
```

## Usage

Given the following objects:

```php
$tolkien = new Author();
$tolkien->id = 1;
$tolkien->name = 'J.R.R. Tolkien';
$tolkien->isFreelance = false;
$tolkien->birthDate = Carbon::createFromFormat('Y-m-d', '1892-01-03');

$hobbit = new Book();
$hobbit->id = 1;
$hobbit->author = $tolkien;
$hobbit->title = 'The Hobbit';
$hobbit->year = 1937;

$lotr = new Book();
$lotr->id = 2;
$lotr->author = $tolkien;
$lotr->title = 'The Lord of the Rings';
$lotr->year = 1968;

$this->tolkien->books = [
    $hobbit,
    $lotr
];
```

We can define the following ***Transformer***:

```php
class AuthorArrayTransformer extends ArrayTransformerAbstract
{
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
}
```

The `$keysMap` property is used to define the mapping between the properties of the source object and the keys of the resulting array.

On the other hand, we have included a ***presenter*** to modify the resulting value of a property of the original object on the fly. The nomenclature to define *presenters* is composed of the keyword **`present`** followed by the property name of the original object in **Camel Case** format.

To execute the transformation we would do the following:

```php
$authorTransformer = new AuthorArrayTransformer();
$authorArray = $authorTransformer->transformItem($tolkien);

json_encode($authorArray)
```

The resulting array would look like this:

```php
{
  "id": 1,
  "name": "J.R.R. Tolkien",
  "is_freelance": false,
  "birth_date": "1892-01-03"
}
``` 

### Transforming collections
In the same way that we transform a single object we can also transform a collection of them. Since we have an array of authors we could do the following:

```php
$authorTransformer = new AuthorArrayTransformer();
$authorsArray = $authorTransformer->transformCollection($authors);

json_encode($authorsArray)
```

To get something like this:

```php
{
  [
    {
      "id": 1,
      "name": "J.R.R. Tolkien",
      "is_freelance": false,
      "birth_date": "1892-01-03"
    },
    {
      "id": 2,
      "name": "Isaac Asimov",
      "is_freelance": false,
      "birth_date": "1920-01-02"
    }
  ]
}
```

### Embedding additional elements
We can indicate the elements that we want to embed making use of the property **`$embeds`**:

Given the following book transformer:

```php
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
```

We could modify our transformer of authors in the following way:

```php
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
```

In this case we want to embed the books belonging to the author. The nomenclature to define *embeds* is composed of the keyword **`embed`** followed by the name of the element we want to embed in **Camel Case** format.

The result would be something like this:

```php
{
  [
    {
      "id": 1,
      "name": "J.R.R. Tolkien",
      "is_freelance": false,
      "birth_date": "1892-01-03",
      "books": [
        {
          "id": 1,
          "title": "The Hobbit",
          "author": "J.R.R. Tolkien",
          "year": 1937
        },
        {
          "id": 2,
          "title": "The Lord of the Rings",
          "author": "J.R.R. Tolkien",
          "year": 1968
        }
      ]
    },
    {
      "id": 2,
      "name": "Isaac Asimov",
      "is_freelance": false,
      "birth_date": "1920-01-02",
      "books": [
        {
          "id": 3,
          "title": "The Caves of Steel",
          "author": "Isaac Asimov",
          "year": 1954
        }
      ]
    }
  ]
}
```

### Testing

``` bash
composer test
```

## Credits

- [Sendoa Portuondo](https://github.com/sportuondo)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.