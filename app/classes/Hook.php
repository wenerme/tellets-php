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
	/**
	 * 生成 meta 时触发的事件
	 *
	 * 原型: void callback()
	 *
	 * 如果想要添加内容到 生成过程 中,则在 触发时直接输出即可
	 *
	 */
	const GENERATE_META_EVENT = 'generate_meta';
	const GENERATE_HEADER_EVENT = 'generate_header';
	const GENERATE_FOOTER_EVENT = 'generate_footer';

	/**
	 * 启动时触发的事件,只会触发 after 事件.
	 *
	 * 原型: void callback()
	 */
	const BOOTSTRAP_EVENT = 'bootstrap';

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