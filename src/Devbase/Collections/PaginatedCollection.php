<?php

namespace Devbase\Collections;

/**
 * Paginira proizvoljnu kolekciju.
 *
 * Kolekcija može biti običan PHP niz ili objekat koji implementira Iterator interfejs.
 *
 * @code
 *   // niz kao kolekcija
 *   $collection = range(1, 100);
 *
 *   // paginiraj po parametru iz GET, 15 po stranici
 *   $current_page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
 *   $paginated = new PaginatedCollection($collection, $current_page, 15);
 *
 *   // ispisuje sadržaj strane, najviše 15 stavki iako izgleda kao da prolazimo celu kolekciju
 *   foreach($paginated as $item)
 *   {
 *       print_r($item);
 *   }
 * @endcode
 *
 * Ako je kolekcija niz ili objekat koji implementira ::Countable interfejs, na raspolaganju su nam metode:
 * - Broj stavki u kolekciji: PaginatedCollection::count() ili preko ugrađene funkcije ::count()
 * - Broj stranica: PaginatedCollection::countPages()
 * - Broj stavki na trenutnoj strani: PaginatedCollection::countOnPage()
 *
 * Poslednje bi trebalo da bude jednako broju stavki po stranici iz konstruktora, osim na poslednjoj stranici
 * koja može da ima manje stavki.
 *
 * @throws OutOfBoundException
 *  \warning Ukoliko je kolekcija prazan niz, na PHP 5.2 iterator iz ArrayIterator baca OutOfBoundException.
 *  Ova PHP/SPL greška <a href="https://bugs.php.net/bug.php?id=49723">#49723</a> je ispravljena u
 *  PHP >= 5.3.3. Kao zaobilazno rešenje, svaki ulazak u foreach ili drugu iteraciju treba obmotati try/catch
 *  blokom ili eksplicitno pitati PaginatedCollection::valid() za odgovor da li je iteracija dopuštena.
 */
class PaginatedCollection implements \OuterIterator, Collection
{
    public static $CLASS = __CLASS__;

    protected $iterator;
    protected $offset;

    /**
     * @param Collection $collection
     *  Kolekcija ili niz
     *
     * @param int $page
     *  Broj stranice, pocev od jedan
     *
     * @param int $items_per_page
     *  Broj stavki po stranici
     */
    public function __construct($collection, $page = 1, $items_per_page = 10)
    {
        $this->collection = Utils::getIterator($collection);
        $this->items_per_page = $items_per_page;

        $this->setPage($page);
    }

    public function getInnerIterator()
    {
        return $this->iterator;
    }

    private function setPageInBounds($page)
    {
        if(($pages = $this->countPages()) >= 0) {
            $page = min($page, $pages);
        }

        $page = max($page, 1);

        $this->offset = ($page-1)*$this->items_per_page;
        $this->page = $page;
    }

    public function setPage($page)
    {
        $this->setPageInBounds($page);

        /* HACK: Ensure the current page items are loaded */
        if($this->collection instanceof LazyCollection) {

            $count = $this->count();
            $this->collection->fetch($this->offset, $this->items_per_page);

            /* We may know a new count now, adjust */
            if($this->count() != $count) {
                $this->setPageInBounds($page);
            }
        }

        $this->iterator = new \LimitIterator($this->collection, $this->offset, $this->items_per_page);
    }

    public function current() { return $this->iterator->current(); }
    public function key()     { return $this->iterator->key();     }
    public function next()    { return $this->iterator->next();    }
    public function rewind()  { return $this->iterator->rewind();  }
    public function valid()   { return $this->iterator->valid();   }

    public function count()
    {
        return count($this->collection);
    }

    public function countOnPage()
    {
        $count = $this->count();
        if($count < 0) {
            /* Total unknown, we probaly do have a page */
            return $this->items_per_page;
        } else {
            $offset = $this->getPageOffset();
            return min($count-$offset, $this->items_per_page);
        }
    }

    public function countPages()
    {
        if(($count = $this->count()) >= 0) {
            return ceil($count/$this->items_per_page);
        }
        else return -1;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getPageOffset()
    {
        return $this->offset;
    }

    public function offsetSet($index, $value)
    {
        $index += ($this->page-1)*$this->items_per_page;
        $this->collection[$index] = $value;
    }

    public function offsetUnset($index)
    {
        $index += ($this->page-1)*$this->items_per_page;
        unset($this->collection[$index]);
    }

    public function offsetGet($index)
    {
        $index += ($this->page-1)*$this->items_per_page;
        return isset($this->collection[$index]) ? $this->collection[$index] : null;
    }

    public function offsetExists($index)
    {
        $index += ($this->page-1)*$this->items_per_page;
        return isset($this->collection[$index]);
    }
}

