<?php

class Config extends ArrayObject 
{
    private $changed = false;
    private $filename = '';
    private $description = array();

    public function __construct($filename)
    {
        $this->filename = $filename;

        $config = array();
        $description = array();
        
        if(file_exists($filename))
        	include $filename;
		
        parent::__construct($config);
        // 如果被设置了，则进行反序列化
        !!$description && $this->description = unserialize($description);
    }


    public function __destruct()
    {
        // if changed, save change to file.
        if($this->changed)
            $this->Save();
    }
	
    /**
     * Save config to file.
     * @param string $filename
     */
    public function Save($filename = null)
    {
    	if ($filename === null)
    		$filename = $this->filename;
 
    	
    	$fp = fopen($filename,'wb');
    	
    	fwrite($fp,'<?php'.PHP_EOL);
    	
    	// write value
	    fwrite($fp, $this->serializeItem($this,'$cinfig',$this->description));

    	// save description
    	fwrite($fp, PHP_EOL.'/*-------------------- DO NOT CHANGE --------------------*/'.PHP_EOL);
    	
    	fwrite($fp,sprintf('$description = %s;',var_export(serialize($this->description), true)));
    	
    	// over
    	fclose($fp);
    	$this->changed = false;
    }
    private function serializeItem($items, $prefix, $description)
    {
	    $result = '';
		foreach($items as $k => $v)
		{
			$p = sprintf('%s[%s]',$prefix, var_export($k,true));
			if(is_array($v))
			{
				$result .= $this->serializeItem($v, $p, isset($description[$k])?$description[$k]:array());
				continue;
			}
			//
			$content = PHP_EOL;

			if(isset($description[$k]) && $description[$k])
			{
				$desc = $description[$k];
				$content .= "/* $desc */".PHP_EOL;
			}

			$content .= sprintf('%s = %s;'.PHP_EOL
				, $p, var_export($v, true));

			$result .= $content;
		}

	    return $result;
    }
    public function isChanged(){return $this->changed;}

    public function addDefault($name, $value, $description = '')
    {
        if(isset($this[$name]))
            goto ALREADY_SET;

        $this[$name] = $value;
        // 仅当 描述有效时才保存
        if(!!$description)
            $this->description[$name] = $description;

        ALREADY_SET:
        return $this;
    }
	/**
	 * 获取关于设置的描述
	 * @param string $name
	 * @return NULL|string 如果不存在，则返回NULL
	 */
    public function getDescription($name)
    {
    	if(false == isset($this->description[$name]))
    		return NULL;
    	return $this->description[$name];
    }
    
    public function offsetSet($index, $newval)
    {
    	if(false == isset($this[$index]) || $this[$index] != $newval)
    	{
    		$this->changed = true;
            return call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    	}
    }
} 