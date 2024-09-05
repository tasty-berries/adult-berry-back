<?php

namespace App\Services\Html;

class HtmlElement
{
    /**
     * @var HtmlElement|null Parent element.
     */
    public ?HtmlElement $parent = null;

    /**
     * @var HtmlElement[] Children elements.
     */
    public array $children = [];

    /**
     * @var array Attributes.
     */
    public array $attributes = [];

    /**
     * @var string Text content.
     */
    public string $textContent = '';

    /**
     * @var string Tag name.
     */
    public string $tagName = '';

    /**
     * @var bool Indicates, if element was opened.
     */
    public bool $opened = false;

    public function firstText(): ?string
    {
        if (!isset($this->children[0]))
            return null;

        return $this->children[0]?->textContent;
    }

    public function deepText(): ?string
    {
        return $this->scan($this);
    }

    private function scan(HtmlElement $element): ?string
    {
        if ($element->textContent)
            return $element->textContent;

        $text = '';

        foreach ($element->children as $child) {
            $scanned = $this->scan($child);
            if (!$scanned)
                continue;

            $text .= $scanned . "\n";
        }

        if (!$text)
            return null;

        return $text;
    }

    /**
     * Get filtered children.
     * @param array $filter
     * @return HtmlElement[]
     */
    protected function filterChildren(array $filter): array
    {
        $id        = $filter['id'] ?? null;
        $fullClass = $filter['class'] ?? null;
        $classes   = $filter['classes'] ?? [];
        $data      = $filter['data'] ?? null;
        $itemprop  = $filter['itemprop'] ?? null;
        $tag       = $filter['tag'] ?? null;

        $children = [];

        foreach ($this->children as $child) {
            if ($fullClass && isset($child->attributes['class']) && $child->attributes['class'] == $fullClass) {
                $children[] = $child;
                continue;
            }

            if ($classes && isset($child->attributes['class'])) {
                $_classes = explode(' ', $child->attributes['class']);
                if (count(array_diff($classes, $_classes)) == 0) {
                    $children[] = $child;
                    continue;
                }
            }

            if ($id && isset($child->attributes['id']) && $child->attributes['id'] == $id) {
                $children[] = $child;
                continue;
            }

            if ($itemprop && isset($child->attributes['itemprop']) && $child->attributes['itemprop'] == $itemprop) {
                $children[] = $child;
                continue;
            }

            if ($tag && $child->tagName == $tag) {
                $children[] = $child;
                continue;
            }

            if ($data) {
                foreach ($data as $key => $value) {
                    if (isset($child->attributes['data-' . $key]) && $child->attributes['data-' . $key] == $value) {
                        $children[] = $child;
                        continue;
                    }
                }
            }
        }

        return $children;
    }

    /**
     * Find elements in children.
     * @param array $filter
     * @return HtmlElement|null
     */
    public function find(array $filter = []): ?HtmlElement
    {
        $children = $this->filterChildren($filter);
        if ($children)
            return $children[0];

        foreach ($this->children as $child) {
            if ($child->children && ($result = $child->find($filter)))
                return $result;
        }

        return null;
    }

    /**
     * Find all elements in children.
     * @return HtmlElement[]
     */
    public function findAll(array $filter = []): array
    {
        $children = $this->filterChildren($filter);
        if ($children)
            return $children;

        foreach ($this->children as $child) {
            if ($child->children && ($result = $child->findAll($filter)))
                return $result;
        }

        return [];
    }

    /**
     * Find all elements in children, but recursive.
     * @return HtmlElement[]
     */
    public function findAllRecursive(array $filter = []): array
    {
        $children = $this->filterChildren($filter);

        foreach ($this->children as $child) {
            if ($child->children && ($result = $child->findAllRecursive($filter)))
                $children = [...$children, ...$result];
        }

        return $children;
    }
}
