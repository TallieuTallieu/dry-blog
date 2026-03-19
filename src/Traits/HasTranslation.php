<?php

namespace Tnt\Blog\Traits;

use dry\http\Response;

trait HasTranslation
{
    /**
     * Usage
     * 1. add translatable fields below
     * 2. access data by: model.i18n('title')
     */
    public function i18n(string $field): ?string
    {
        return in_array($field, $this->getTranslationFields(), true)
            ? $this->get_translation($field)
            : null;
    }

    public function get_translation(string $field): ?string
    {
        $language = Response::$language;

        $value = $this->{"{$field}_{$language}"} ?? null;

        if (empty($value) && $language !== 'en') {
            $value = $this->{"{$field}_en"} ?? null;
        }

        return $value;
    }

    private function getTranslationFields(): array
    {
        return property_exists($this, 'translation_fields')
            ? $this->translation_fields
            : [];
    }
}
