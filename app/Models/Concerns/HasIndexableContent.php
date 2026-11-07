<?php

namespace App\Models\Concerns;

/**
 * İnce içerik koruması.
 *
 * 81 il ve 973 ilçenin hepsi sayfa olarak yayında, ama şablon metinli binlerce sayfa
 * Google'da sıralanmaz ve sitenin geri kalanını da aşağı çeker. Bir bölge sayfası
 * ancak kendine özgü gövde metni yeterince uzunsa (AI zenginleştirme ya da elle
 * yazım) dizine açılır; o zamana kadar noindex döner ve site haritasına girmez.
 */
trait HasIndexableContent
{
    public const MIN_INDEXABLE_WORDS = 150;

    public function contentWordCount(): int
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $this->content)));

        return $text === '' ? 0 : count(explode(' ', $text));
    }

    public function isIndexable(): bool
    {
        return $this->contentWordCount() >= self::MIN_INDEXABLE_WORDS;
    }

    public function robotsMeta(): string
    {
        return $this->isIndexable() ? 'index, follow' : 'noindex, follow';
    }
}
