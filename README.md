# webman 和 ThinkPHP的区别

## 多应用
ThinkPHP 的多应用配置文件 在应用目录里可定义，webman不支持

webman的多应用中间件配置在 根config/middleware.php 中

## 视图

View::assign 用Support/View

view 函数 如果不传模板参数，自动根据2 3规则查找模板文件

## 数据库

用 support/Db 来替换 Think/facade/Db

装 `webman/think-orm`

## session

session 函数 和 tp的不一样

- 获取 `session('key', 'default')`
- 设置 `session(['key'=>'value'])`

## 响应
- redirect()
- success、error、result 的实现 `composer require kingbes/jump`

## Env
`composer require vlucas/phpdotenv`
`getenv` 貌似不支持多维数组

## 常量

PUBLIC_PATH

## hook 改为事件， 发现 webman 不支持动态修改配置 
改为中间件初始化钩子，
事件通过一个Hook类来监听所有钩子事件

## validate 

`taoser/webman-validate`

`use think\Validate;` => `use taoser\Validate;`