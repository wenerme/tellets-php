
### 修改 配置设置到 dropplets/config.php
### 将 Post 作为一个类
### dropplets 类，包括大部分系统操作。

### 修改 dropplets 的内容元字段

···
<!-- title: this is the post title -->
<!-- author: wener -->

<!-- category: this is the post title -->
<!-- tag: story -->

<!-- date: posting -->

<!-- key: value -->
···

作为一种可选方式，该元字段只能出现在文章的开头。

在缓存文章的时候需要把文章的元字段给存储起来，这样方便与检索和分类信息的显示。

元字段作为 key/value 的数据信息，考虑在使用其它扩展的时候也会使用到该特性，所以相对来说比较有必要性。

元字段的名值会经过如下处理

1. trim key 和 value
2. 将key中多个空格替换为单个空格

### 修改以支持 <!-- more -->

···
description here.

<!-- more -->

the content.
···

处理的时候需要把描述之前的提取出来，也需要使用 markdown 格式化，然后作为元字段

### 使用插件实现 与 github 整合

将 github 作为文件源

### 修改以支持 i18n 的文章

以文件名作为标示

比如可以有以下文章

* this-is-first-post-zh.md 
* this-is-first-post-en.md 

等，选择如果不存在 ·this-is-first-post.md ·，则先选择默认语言的文章，默认语言在配置里有。否则选择第一个获取到的文章。

### 内容模板修改为只包含主要的 body

	这样只需要替换body就好了。而不是缓存整个页面，这个到时候再考虑。

### 发布文章

只有 state 为 publish 的时候才会显示文章. 如果 没有 state字段,则默认为 draft
	

Dropplets 运行流程
------------------

* 先检测是否存在 config.php, 作为是否是第一次运行的判断
* 获取路径中的文件名和分类参数
	* 如果不存在 filename == null,则为主页
	* 如果 filename 为 rss 或 atom,则生成相应输出
	* 否则则以单页来处理,eg. youre-ready-to-go
	
国家号
------

参见 https://github.com/umpirsky/country-list