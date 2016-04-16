<?php


/**
 * class RijndaelHex
 * Clase encriptadora Rijndael.
 */
class MemoryCache {
	var $_object = null;
	var $str = 'ADNBPCACHE';
	
	function MemoryCache($str='') {
		if(strlen(trim($str))) {
		    $this->str .= '_'.trim($str);
        } else {
            $this->str .= '_'.$_SERVER['HTTP_HOST'];
        }
		$this->_object =  new Memcache;
	}
	
	function reset() {
		$this -> data = array();
		$this -> save();		
	}
	function set($str,$data) {
		if(!strlen(trim($str))) return false;
		$info['_microtime_']=microtime(true);
		$info['_data_']=gzcompress(serialize($data));
		$this -> _object ->set($this->str.'-'.$str,serialize($info));
		return true;
	}
	function delete($str) {
		if(!strlen(trim($str))) return false;
		$this -> _object ->delete($this->str.'-'.$str);
		return true;
	}

	function get($str,$expireTime=-1) {
		if(!strlen(trim($str))) return false;
		$info = $this -> _object ->get($this->str.'-'.$str);
		if(strlen($info) && $info!==null) {
			$info = unserialize($info);
			// Expire Caché
			if($expireTime >=0 && microtime(true)-$info['_microtime_'] >= $expireTime) {
				$this -> _object ->delete($this->str.'-'.$str);
				return null;
			} else {
				return(unserialize(gzuncompress($info['_data_'])));
			}
		} else {
			return null;
		}
	}
	function getTime($str,$expireTime=-1) {
		if(!strlen(trim($str))) return false;
		$info = $this -> _object ->get($this->str.'-'.$str);
		if(strlen($info) && $info!==null) {
			$info = unserialize($info);
			return(microtime(true)-$info['_microtime_']);
		} else {
			return null;
		}
	}
	
	function _save() {
		$this -> _object ->set($this->str,gzcompress(serialize($this->data)));
		$this -> save();		
	}
}