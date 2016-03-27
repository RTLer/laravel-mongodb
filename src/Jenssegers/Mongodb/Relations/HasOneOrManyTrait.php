<?php

namespace Jenssegers\Mongodb\Relations;

use Illuminate\Database\Eloquent\Collection;
use MongoDB\BSON\ObjectID;

/**
 * Class HasOneOrManyTrait
 * @package Jenssegers\Mongodb\Relations
 *
 * @property $this parent Jenssegers\Mongodb\Relations
 * @property $this localKey
 */
trait HasOneOrManyTrait
{
    /**
     * Get all of the primary keys for an array of models.
     *
     * @param  array $models
     * @param  string $key
     * @return array
     */
    protected function getKeys(array $models, $key = null)
    {
        return array_unique(array_values(array_map(function ($value) use ($key) {
            $id = $key ? $value->getAttribute($key) : $value->getKey();
            if (is_string($id) and strlen($id) === 24 and ctype_xdigit($id)) {
                return new ObjectID($id);
            }
            return $id;

        }, $models)));

    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  \Illuminate\Database\Eloquent\Collection $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        $dictionary = [];

        $foreign = $this->getPlainForeignKey();

        // First we will create a dictionary of models keyed by the foreign key of the
        // relationship as this will allow us to quickly access all of the related
        // models without having to do nested looping which will be quite slow.
        foreach ($results as $result) {
            $dictionary[(string)$result->{$foreign}][] = $result;
        }

        return $dictionary;
    }

    /**
     * Get the key value of the parent's local key.
     *
     * @return mixed
     */
    public function getParentKey()
    {
        $id = $this->parent->getAttribute($this->localKey);
        if (is_string($id) and strlen($id) === 24 and ctype_xdigit($id)) {
            return new ObjectID($id);
        }

        return $id;
    }

}