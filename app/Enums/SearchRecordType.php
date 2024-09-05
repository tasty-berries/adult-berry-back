<?php

namespace App\Enums;

use App\Models\Author;
use App\Models\Character;
use App\Models\Comic;
use App\Models\Tag;
use App\Models\Title;

enum SearchRecordType: string
{
    case Comic     = 'comic';
    case Character = 'character';
    case Tag       = 'tag';
    case Title     = 'title';
    case Author    = 'author';

    public static function fromClass(string $class): SearchRecordType
    {
        return match ($class) {
            Comic::class     => self::Comic,
            Character::class => self::Character,
            Tag::class       => self::Tag,
            Title::class     => self::Title,
            Author::class    => self::Author
        };
    }
}
