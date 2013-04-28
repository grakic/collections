<?php

namespace Devbase\Collections;

class Utils
{
    /**
     * Osigurava da kolekcija ima iterator.
     *
     * Za niz vraća novi \ArrayIterator, dok se objekat koji implementira makar \Traversable
     * interfejs vraća nepromenjen.
     *
     * @var array $collection
     *
     * @return \ArrayIterator
     *
     * @throws DomainException
     *  Izuzetak ako kolekcija nema iterator
     */
    public static function getIterator($collection)
    {
        if(is_array($collection))
            return new \ArrayIterator($collection);
        elseif($collection instanceof \Traversable)
            return $collection;
        else
            throw new \DomainException('No known iterator for the collection of type '.get_class($collection));
    }

    /**
     * @overload \Traversable getIterator(\Traversable $collection)
     */

    /**
     * Proverava da li je objekat kolekcija.
     *
     * Kolekcija je niz ili objekat koji implementira makar \Traversable interfejs.
     *
     * @var mixed $object
     *
     * @return bool
     */
    public static function isCollection($object)
    {
        return is_array($object) || ($object instanceof \Traversable);
    }
}
