<?php

class Config extends ArrayObject 
{
	/**
	 * 一般配置信息
	 */
	const NS_CONFIG = 'config';
	/**
	 * 用于配置插件的设置
	 */
	const NS_PLUGINS = 'plugins';
	/**
	 * 用于配置 模板的显示信息
	 */
	const NS_TEMPLATE = 'template';

    private $changed = false;
    private $filename = '';
    private $description = array();

    public function __construct($filename)
    {
        $this->filename = $filename;

        $config = array();
        $description = array();
	    $plugins = array();
	    $template = array();
        
        if(file_exists($filename))
        	include $filename;

	    $config[self::NS_PLUGINS] = $plugins;
	    $config[self::NS_TEMPLATE] = $template;

        parent::__construct($config);
        // 如果被设置了，则进行反序列化
        !!$description && $this->description = json_decode(base64_decode($description));
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
	    $template = $this[self::NS_TEMPLATE];
	    unset($this[self::NS_PLUGINS]);
	    unset($this[self::NS_TEMPLATE]);
    	// write value
	    fwrite($fp, $this->serializeItem($this,'$config',@$this->description[self::NS_CONFIG]));
	    fwrite($fp, $this->serializeItem($plugins,'$plugins',@$this->description[self::NS_PLUGINS]));
	    fwrite($fp, $this->serializeItem($template,'$template',@$this->description[self::NS_TEMPLATE]));

	    // restore
	    $this[self::NS_PLUGINS] = $plugins;
	    $this[self::NS_TEMPLATE] = $template;

    	// save description
    	fwrite($fp, PHP_EOL.'/*-------------------- DO NOT CHANGE --------------------*/'.PHP_EOL);

	    $description = var_export(base64_encode(json_encode($this->description)), true);
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
					$desc = "/*\n$desc\n*/";
				else
					$desc = "/* $desc */";
				$desc = PHP_EOL.$desc.PHP_EOL;
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
	    // 当 target 不为 $this 时,不会触发offsetSet里的改变设置
	    $this->changed = true;
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