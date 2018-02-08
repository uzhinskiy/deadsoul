<?php
class Cache {
	private $id;
	private $obj;
	private $_keys;

	function __construct($id){
		$this->id = $id;
		$this->obj = new Memcached($id);
	}

	public function connect($host , $port){
		$servers = $this->obj->getServerList();
		if(is_array($servers)) {
			foreach ($servers as $server)
			if($server['host'] == $host and $server['port'] == $port)
			return true;
		}
		return $this->obj->addServer($host , $port);
	}

	public function set($key, $value, $ttl=-1){
		$ttl = ($ttl==-1) ? CACHE_TIME : $ttl;
		$res = $this->obj->set($key, $value, time()+$ttl);
		return $res;
	}

	public function get($key){
		$res = $this->obj->get($key);
		return $res;
	}

	public function listkeys(){
		$this->_keys = $this->obj->getAllKeys();
		return $this->_keys;
	}

	public function getall(){
		$this->_keys = $this->obj->getAllKeys();
		$res = $this->obj->getMulti($this->_keys);
		return $res;
	}

	public function resetServer(){
		$res = $this->obj->resetServerList();
		return $res;
	}

	public function status(){
		$res = $this->obj->getStats();
		return $res;
	}

}
?>
