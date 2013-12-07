<?php
require_once APP_DIR.'./library/phpass.php';

class Dropplets
{

	/**
	 * 解析文章时触发的事件
	 * 
	 * 原型： void callabck(Post, $markdown);
	 */
    const PARSE_POST_EVENT = 'parse_post';

    /**
     * 在解析文章时触发的事件
     *
     * 原型: void callback(out &Post, $name);
     *
     * 如果在 Before 时hook返回hook,则系统不再进行自动查找post.
     *
     * 如果在 After 过后 $post 依然为 假值,则判断为 404 not found.
     */
    const RESOLVE_POST_EVENT = 'resolve_post';

    /**
     * 在生成文章列表时触发的事件
     *
     * 原型: void callback($postFileList);
     *
     * 如果需要添加其他的文章来源,则在这里添加.
     */
    const FIND_POST_LIST_EVENT = 'find_post_list';

    private static $afterHook = array();
    private static $beforeHook = array();
	
    /**
     * 添加事件之前的挂钩
     * @param string $event 事件名
     * @param callable $callback 回调函数
     */
    public static function AddBeforeHook($event, callable $callback)
    {
        self::$beforeHook[$event][] = $callback;
    }
    /**
     * 添加事件之后的挂钩
     * @param string $event 事件名
     * @param callable $callback 回调函数
     */
    public static function AddAfterHook($event, callable $callback)
    {
        self::$afterHook[$event][] = $callback;
    }
    
    private static function TriggerEvent($from, $event, $args)
    {
        if(isset($from[$event]))
            foreach($from[$event] as $func)
                call_user_func_array($func, $args);
    }
    /**
     * 触发事件之前的挂钩
     * @param string $event
     * @param array $args
     */
    public static function TriggerBeforeEvent($event, $args)
    {
        self::TriggerEvent(self::$beforeHook,$event,$args);
    }
    /**
     * 触发事件之后的挂钩
     * @param string $event
     * @param array $args
     */
    public static function TriggerAfterEvent($event, $args)
    {
        self::TriggerEvent(self::$afterHook,$event,$args);
    }


    /**
     * @var Config
     */
    public $config ;
    /**
     * @var IPostHelper
     */
    public $postHelper;

    public function __construct()
    {}

    /**
     * 删除所有缓存文件
     * 
     * 只有在登录后才有效
     */
	public function Invalidate()
	{
		
	}

    /**
     * 删除缓存,从新生成所有文章
     *
     * 只有在登录后才有效
     */
    public function Update()
    {

    }

    public function SetUp()
    {
        $hasher  = new PasswordHash(8,FALSE);
        $this->config['password'] = $hasher->HashPassword($_POST["password"]);
    }

	public function Login($password)
	{
		
	}
	public function Logout()
	{
		
	}
	public function isLogin()
	{
		
	}

    /**
     * 获取文章文件列表
     */
    public function getPostFileList()
    {

    }

    public function resolvePost($name)
    {}
}