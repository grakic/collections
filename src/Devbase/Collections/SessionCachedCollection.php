namespace Devbase\Collections;

class SessionCachedCollection extends CachedCollection
{
    protected $cache_ttl;

    public function __construct(Delayed $collection, $cache_id, $session_id, $cache_ttl = 60)
    {
     	session_id($session_id);
        if(!isset($_SESSION)) {
            session_start();
        }
	$this->cache_ttl = $cache_ttl;
        parent::__construct($collection, $cache_id);
    }

    private function cacheSpaceCreate()
    {
     	if(!isset($_SESSION['cache'])) $_SESSION['cache'] = array();
        if(!isset($_SESSION['cache'][$this->cache_id])
             || !isset($_SESSION['cache'][$this->cache_id]['time'])
             || !isset($_SESSION['cache'][$this->cache_id]['items']))
                $_SESSION['cache'][$this->cache_id] = array('time'=>time(), 'items'=>array());
    }

    private function cacheSpaceGetValid()
    {
     	$this->cacheSpaceCreate();

        if($_SESSION['cache'][$this->cache_id]['time']+$this->cache_ttl < time()) {
            unset($_SESSION['cache'][$this->cache_id]);
            $this->cacheSpaceCreate();
        }
    }

    protected function cacheGet(&$result, $offset, $limit = 1)
    {
     	$this->cacheSpaceGetValid();
        return array_keys_range_exist($_SESSION['cache'][$this->cache_id]['items'], $offset, $limit, $result);
    }

    protected function cacheSet($result)
    {
     	$this->cacheSpaceGetValid();
        $_SESSION['cache'][$this->cache_id]['items'] += $result;
    }
}

