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
        if(is_array($object)
            return new \ArrayIterator($collection);
        elseif($object instanceof \Traversable)
            return $object;
        else
            throw new \DomainException('No known iterator for the collection');
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
