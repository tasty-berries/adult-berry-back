<?php

namespace App\Services\Html;

class HtmlParser
{
    public function parse(string $content): HtmlElement
    {
        $chars = mb_str_split($content);

        $documentElement          = new HtmlElement();
        $documentElement->tagName = 'document';
        $currentElement           = $documentElement;

        $context = 'text';

        $tagName    = '';
        $tagOpened  = false;
        $tagClosing = false;

        $attribute            = '';
        $attributeOpened      = false;
        $attributeValueOpened = false;
        $attributeValueQuotes = '';
        $attributeValue       = '';
        $attributes           = [];

        $textContent = '';

        $singleTags = ['DOCTYPE', 'doctype', 'img', 'input', 'meta', 'link', 'br'];

        $isComment = false;

        foreach ($chars as $char) {
            if (!$tagOpened && $char == '<') {
                if (trim($textContent)) {
                    $element                    = new HtmlElement();
                    $element->textContent       = trim($textContent);
                    $currentElement->children[] = $element;
                    $textContent                = '';
                }

                $tagOpened       = true;
                $attributeOpened = false;
                $tagName         = '';
                $context         = 'tag';
                continue;
            }

            if ($tagOpened && !$attributeValueOpened && in_array($char, [' ', "\n", "\r", '>', '='])) {
                if (($attributeOpened || $attributeValue) && $attribute)
                    $attributes[$attribute] = $attributeValue;

                if (!in_array($char, ['=', '>'])) {
                    $attributeOpened = true;
                    $attribute       = '';
                    $attributeValue  = '';
                } else {
                    $attributeOpened = false;
                    $attributeValue  = '';

                    if ($char == '=') {
                        $attributeValueOpened = true;
                        continue;
                    }
                }
            }

            if ($tagOpened && $attributeValueOpened && !$attributeValueQuotes && in_array($char, ["'", "\""])) {
                $attributeValueQuotes = $char;
                continue;
            }

            if ($tagOpened && $attributeValueOpened && $attributeValueQuotes && $char == $attributeValueQuotes) {
                $attributeValueOpened = false;
                $attributeValueQuotes = '';
                continue;
            }

            if ($tagOpened && $attributeValueOpened && $attributeValueQuotes) {
                $attributeValue .= $char;
            }

            if (
                $tagOpened && $attributeOpened &&
                in_array($char, [
                    ...range('a', 'z'),
                    ...range('A', 'Z'),
                    ...range(0, 9),
                    '-', '_'
                ])
            ) {
                $attribute .= $char;
            }

            if ($tagOpened && !$tagClosing && !$attributeOpened && !$attributeValueOpened && $char == '/') {
                $tagClosing = true;
                continue;
            }

            if (
                $tagOpened && !$attributeOpened && !$attributeValueOpened &&
                in_array($char, [
                    ...range('a', 'z'),
                    ...range('A', 'Z'),
                    ...range('0', '9'),
                    '-',
                    '!'
                ])
            ) {
                $tagName .= $char;
                continue;
            }

            if ($tagOpened && $char == '>') {
                $element          = new HtmlElement();
                $element->tagName = $tagName;
                $element->opened  = !in_array($tagName, $singleTags);
                $element->parent  = $currentElement;

                if ($attributes) {
                    $element->attributes = $attributes;
                    $attributes          = [];
                }

                if ($currentElement->opened && $tagClosing) {
                    $currentElement->opened = false;
                    $currentElement         = $currentElement->parent;
                    $tagClosing             = false;
                } else {
                    $currentElement->children[] = $element;

                    if (!in_array($element->tagName, $singleTags))
                        $currentElement = $element;
                }

                $tagOpened = false;
                $context   = 'text';
                continue;
            }

            if ($context == 'text' && !in_array($char, ["\n", "\r"])) {
                $textContent .= $char;
            }
        }

        if ($context == 'text') {
            $element                    = new HtmlElement();
            $element->textContent       = $textContent;
            $currentElement->children[] = $element;
        }

        return $documentElement;
    }
}
