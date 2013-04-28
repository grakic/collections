namespace Devbase\Collections;

interface LazyCollection extends Collection
{
    public function fetch($offset, $limit = null);
}
