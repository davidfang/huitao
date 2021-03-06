<?php
/**
 * Redis操作
 */
class redisModel
{
	public static $handle = null;
	public static $conn = null;
	public function __construct()
	{
		if(!self::$handle)
			self::$handle = new Redis();
		if(!self::$conn)
			self::$conn = self::$handle->connect(C('REDIS_DNS'),C('REDIS_PORT'));
	}

	public function addListAll($key,$list)
	{
		self::$handle->multi();
		foreach($list as $k => $v)
		{
			if(!self::$handle->lpush($key,json_encode($v)))
			{
				self::$handle->discard();
				info('addredis失败',-1);
			}
		}
		self::$handle->exec();

	}

	public function addHashAll($key,$list)
	{
		self::$handle->multi();
		foreach($list as $k => $v)
		{
			if(!self::$handle->hset($key,$k,json_encode($v)))
			{
				self::$handle->discard();
				info('addredis失败',-1);
			}
		}
		self::$handle->exec();
	}

	public function getHashSingle($key,$field){
        $value = self::$handle->hget($key,$field);
        // D($value);die;
        return json_decode($value,true);
    }

	public function getListPage($key,$current=0,$len=10)
	{
		$list = self::$handle->lrange($key,$current,$len);
		if(empty($list)) return $list;
		foreach ($list as $k => $v) {
			$_list[] = json_decode($v,true);
		}
		return $_list;
	}

	/**
	 * [exisit 检查是否存在]
	 */
	public function exisit($key)
	{
		return self::$handle->exists($key);
	}

	/**
	 * [setExpire 设置key的过期时间]
	 */
	public function setExpire($key,$time)
	{
		if($this->exisit($key))
		{
			self::$handle->expire($key,$time);  # 设置多少秒后过期
		}
	}

	/**
	 * [hashFeildExisit 判断key中field是否存在]
	 */
	public function hashFeildExisit($feild)
	{
		return self::$handle->hexisit($field);
	}

	/**
	 * [hsetnx 将哈希表key中的field设置值，当且仅当域field不存在]
	 */
	public function hsetnx($field)
	{
		return self::$handle->hsetnx($field);
	}

	/**
	 * [delFeild 删除某个key]
	 */
	public function delFeild($field)
	{
		self::$handle->del($field);
	}

	public function setKV($field,$value)
	{
		self::$handle->set($field,$value);
	}

	public function getKV($field)
	{
		return self::$handle->get($field);
	}

	/**
	 * [getLen 返回名称为key的list有多少个元素]
	 */
	public function getLen($field){
		return self::$handle->lSize($field);
	}
}