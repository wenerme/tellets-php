<?php

class Config extends ArrayObject 
{
	const NS_CONFIG = 'config';
	const NS_PLUGINS = 'plugins';
	const NS_TEMPLATES = 'templates';

    private $changed = false;
    private $filename = '';
    private $description = array();

    public function __construct($filename)
    {
        $this->filename = $filename;

        $config = array();
        $description = array();
	    $plugins = array();
	    $templates = array();
        
        if(file_exists($filename))
        	include $filename;

	    $config[self::NS_PLUGINS] = $plugins;
	    $config[self::NS_TEMPLATES] = $templates;

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

	    $plugins = $this[self::NS_PLUGINS];
	    $templates = $this[self::NS_TEMPLATES];
	    unset($this[self::NS_PLUGINS]);
	    unset($this[self::NS_TEMPLATES]);
    	// write value
	    fwrite($fp, $this->serializeItem($this,'$config',$this->description));
	    fwrite($fp, $this->serializeItem($plugins,'$plugins',@$this->description[self::NS_PLUGINS]));
	    fwrite($fp, $this->serializeItem($templates,'$templates',@$this->description[self::NS_TEMPLATES]));

	    // restore
	    $this[self::NS_PLUGINS] = $plugins;
	    $this[self::NS_TEMPLATES] = $templates;

    	// save description
    	fwrite($fp, PHP_EOL.'/*-------------------- DO NOT CHANGE --------------------*/'.PHP_EOL);

	    $description = var_export(serialize($this->description), true);
	    //$description = wordwrap($description,60,$description{0}."\n.".$description{0});
    	fwrite($fp,sprintf('$description = %s;', $description));
    	
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
			$desc = @$description[$k];

			if($desc && is_string($desc))
			{
				// 判断注释是否有多行
				if(strstr($desc,"\n"))
					$desc = "/*\n$desc\n*/".PHP_EOL;
				else
					$desc = "/* $desc */".PHP_EOL;
			}

			if(is_array($v))
			{
				if(!is_array($desc))
				{
					$result .= $desc;
					$desc = array();
				}
				$result .= $this->serializeItem($v, $p, $desc);
				continue;
			}
			//
			$content = PHP_EOL;

			if($desc)
				$content .= $desc;

			$content .= sprintf('%s = %s;'.PHP_EOL
				, $p, var_export($v, true));

			$result .= $content;
		}

	    return $result;
    }
    public function isChanged(){return $this->changed;}

    public function addDefault($name, $value, $description = '', $ns = self::NS_CONFIG)
    {
	    $target = $this;
	    if($ns !== self::NS_CONFIG)
		    $target = &$this[$ns];

        if(isset($target[$name]))
            goto ALREADY_SET;

	    $target[$name] = $value;
        // 仅当 描述有效时才保存
        if(!!$description)
            $this->description[$ns][$name] = $description;

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