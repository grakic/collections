<?php

namespace Devbase\Collections\Tests;

use Devbase\Collections\PaginatedCollection;

/**
 * Test for Devbase::Collections::PaginatedCollection
 */
class PaginatedCollectionTest extends \PHPUnit_Framework_TestCase
{
    private $countableCollectionCount = 100;
    private $countableCollection;

    protected function setUp()
    {
        $this->countableCollection = array();
        for($i=0;$i<$this->countableCollectionCount;$i++)
            $this->countableCollection[] = "Item $i";
    }

    public function testCountablePageUnderflow()
    {
        $paginated = new PaginatedCollection($this->countableCollection, -1, 10);
        print_r(count($paginated));
        print_r($paginated);
    }

    public function testCountablePageZero()
    {
        $paginated = new PaginatedCollection($this->countableCollection, 0, 10);
        print_r(count($paginated));
        print_r($paginated);
    }

    public function testCountablePageOne()
    {
        $paginated = new PaginatedCollection($this->countableCollection, 1, 10);
        print_r(count($paginated));
        print_r($paginated);

    }

    public function testCountablePageSecondToLast()
    {
        $paginated = new PaginatedCollection($this->countableCollection, $this->countableCollectionCount/10 - 1, 10);
        print_r(count($paginated));
        print_r($paginated);
    }

    public function testCountablePageLast()
    {
        $paginated = new PaginatedCollection($this->countableCollection, $this->countableCollectionCount/10, 10);
        print_r(count($paginated));
        print_r($paginated);
    }

    public function testCountablePageOverflow()
    {
        $paginated = new PaginatedCollection($this->countableCollection, $this->countableCollectionCount/10 + 1, 10);
        print_r(count($paginated));
        print_r($paginated->count());
        print_r($paginated->countOnPage());
        print_r($paginated->countPages());
        print_r($paginated);
    }
}
