<?php

/**
 * 事件处理类
 */
class Hook
{
	/**
	 * 解析文章时触发的事件
	 *
	 * void callabck(Post, $markdown);
	 */
	const PARSE_POST = 'parse_post';
	/**
	 * 在查找文章时触发的事件
	 * 该事件过后,如果Post为假值,则判断为 404 not found.
	 *
	 * void callback(out &Post, $keyword);
	 */
	const RESOLVE_POST = 'resolve_post';
	/**
	 * 在生成文章列表时触发的事件
	 * 如果需要添加其他的文章来源,则在这里添加.
	 *
	 * void callback(&$postFileList);
	 */
	const FIND_POST_LIST = 'find_post_list';

	/**
	 * 在解析 REQUEST 时触发
	 *
	 * 原型: void callback($request);
	 *
	 * 可以用来添加一些动作,例如 action/rss
	 */
	const RESOLVE_REQUEST = 'RESOLVE_REQUEST';
	/**
	 * 生成类事件,在调用该类事件时,直接echo出内容即可
	 * 具体在何处调用,由模板决定
	 *
	 * void callback($request);
	 */
	const GENERATE_META = 'generate_meta';
	const GENERATE_HEADER = 'generate_header';
	const GENERATE_FOOTER = 'generate_footer';

	/**
	 * 启动时触发的事件
	 * 意味着必要的环境已经设置好
	 *
	 * void callback()
	 */
	const BOOTSTRAP = 'BOOTSTRAP';

	/**
	 *  在配置加载完成时触发
	 *  此时尚未设置好运行环境
	 *  void callbacl($config)
	 */
	const CONFIG_COMPLETE = 'CONFIG_COMPLETE';

	protected static $hook = array();

	/**
	 * 添加事件之后的挂钩
	 * @param string $event 事件名
	 * @param callable $callback 回调函数
	 */
	public static function AddHook($event, $callback)
	{
		self::$hook[$event][] = $callback;
	}

	public static function TriggerEvent($event, $args)
	{
		if (isset(self::$hook[$event]))
			foreach (self::$hook[$event] as $func)
				if(is_callable($func))
					call_user_func_array($func, $args);
				else
					throw new Exception("uncallable $func");
	}

}