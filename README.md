<!-- title: Tellets README -->
<!-- category: Posting -->
<!-- tag: Project -->
<!-- date: 2013/12/7 -->
<!-- state: published -->
<!-- link: tellets -->

Tellets
========

Tellets 是一个基于Markup的博客平台.

主要特性
-------

* 文章是基于 MarkUp,易于编写
* 可使用github托管文章,管理和编辑方便
* 模板系统简单,易于修改和实现自己的主题
* 无数据库,使用起来轻松方便
* 0 配置
* 简单的Hook系统,编写插件得心应手
* 无繁杂的管理系统,你只需要写就可以了

<!-- more -->

Getting Started
---------------

- [安装](#安装)
- [撰写文章](#撰写文章)
- [改变设置](#改变设置)
- [更换模板](#更换模板)
- [更新Tellets](#更新Tellets)
- [MIT 许可](#许可)

默认安装的插件
-------------

* github_repo 将文章托管到github
* remote_admin 通过一个网络文件对博客进行配置管理

已定义的元信息
--------------

* title
* date
* category
* tag Array
* link string 作为生成链接的依据,默认的链接生成是从 title生成的
* ext string 该文章的扩展名

Tellets 的由来
--------------

最开始我是从[dropplets](https://github.com/circa75/dropplets)
 [fork](https://github.com/WenerLove/dropplets)的,
很喜欢 dropplets 的简单,但是很多达不到我想要的,原项目也不便于修改,
所以我就将根据从 dropplets 受到的启发,从写成了 tellets.

Tellets 这个名字中的 lets 是继承自 dropplets.
而取名 tellets 是意为 let's tell.

安装
-----

下载最新的 zip 包,解压到你想安装的地方.访问解压的位置,设置密码.ok,一切大功告成.

撰写文章
-------

在撰写文章之前,请看以下这个 [README 的前 几行](https://github.com/WenerLove/dropplets/edit/master/README.md).
这个 README 其实就是一篇博文,你会注意到文章开头的 HTML 注释,
例如 `<!-- title: Tellets README -->`.我管这个叫做 `元信息`,主要用于告诉 tellets 这是什么.

发布一篇博文,必要的字段是 `<!-- state: published -->`,
只有状态是 `published` 的博文才会在 tellets 上显示.

同时一篇博文你或许还希望有 title, date,分别表示文章标题和发布时间.
接下来的内容就自己发挥了.

对了, 你可能还会注意到 `<!-- more -->`, 如果你使用过 WP,那你并不会陌生.
在 more 之上的内容为该博文的简介,在文章列表中只会显示该简介.

改变设置
-------

Tellets 的设置会保存在 `data/config.php` 中,
该文件由第一次运行时自动生成, 生成后的配置文件中有对配置项的注释
,你可以很容易的理解并且修改.

更换模板
-------

将模板解压到 `data/templates` 中,在设置中 更改 `$templates['active'] = /*模板目录*/`.

更新Tellets
------------

现在唯一的方法就是覆盖更新了.

许可
----

因为 Tellets 是我完全从写的,直接选择的 MIT 许可, 与 原生的 dropplets 不同.


