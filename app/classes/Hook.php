<?php

/**
 * 事件处理类
 */
class Hook
{
	/**
	 * 解析文章时触发的事件
	 *
	 * 原型： void callabck(Post, $markdown);
	 */
	const PARSE_POST = 'parse_post';
	/**
	 * 在解析文章时触发的事件
	 *
	 * 原型: void callback(out &Post, $name);
	 *
	 * 如果在 Before 时hook返回hook,则系统不再进行自动查找post.
	 *
	 * 如果在 After 过后 $post 依然为 假值,则判断为 404 not found.
	 */
	const RESOLVE_POST = 'resolve_post';
	/**
	 * 在生成文章列表时触发的事件
	 *
	 * 原型: void callback($postFileList);
	 *
	 * 如果需要添加其他的文章来源,则在这里添加.
	 */
	const FIND_POST_LIST = 'find_post_list';

	/**
	 * 在解析 REQUEST 时触发
	 *
	 * 原型: void callback($request);
	 *
	 * 只会触发 AFTER 事件, 因为 $request 是一次性解析完成的
	 * 可以用来添加一些动作,例如 action/rss
	 */
	const RESOLVE_REQUEST = 'resolve';
	/**
	 * 生成 meta 时触发的事件
	 *
	 * 原型: void callback()
	 *
	 * 如果想要添加内容到 生成过程 中,则在 触发时直接输出即可
	 *
	 */
	const GENERATE_META = 'generate_meta';
	const GENERATE_HEADER = 'generate_header';
	const GENERATE_FOOTER = 'generate_footer';

	/**
	 * 启动时触发的事件
	 *
	 * 原型: void callback()
	 *
	 * 只会触发 AFTER 事件, 触发该事件时,意味着必要的环境以及设置好
	 */
	const BOOTSTRAP = 'bootstrap';

	protected static $afterHook = array();
	protected static $beforeHook = array();

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

	/**
	 * 触发事件之前的挂钩
	 * @param string $event
	 * @param array $args
	 */
	public static function TriggerBeforeEvent($event, $args)
	{
		self::TriggerEvent(self::$beforeHook, $event, $args);
	}

	protected static function TriggerEvent($from, $event, $args)
	{
		if (isset($from[$event]))
			foreach ($from[$event] as $func)
				call_user_func_array($func, $args);
	}

	/**
	 * 触发事件之后的挂钩
	 * @param string $event
	 * @param array $args
	 */
	public static function TriggerAfterEvent($event, $args)
	{
		self::TriggerEvent(self::$afterHook, $event, $args);
	}
}