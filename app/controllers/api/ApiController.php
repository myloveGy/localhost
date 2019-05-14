<?php
/**
 *
 * ApiController.php
 *
 * Author: jinxing.liu@verystar.cn
 * Create: 2019-05-13 11:34
 * Editor: created by PhpStorm
 */

namespace app\controllers\api;

/**
 * Class ApiController 文档信息
 * @package app\controllers\api
 */
class ApiController
{
    private $template = <<<HTML
    ## {title}

### 请求地址

```
{url}
```

### 请求方式

```
{method}
```

### 请求参数

| 参数名                | 参数类型  | 是否必填  | 说明                      |
| :-------------        |:--------: | :-------: | :----------------------   |
{params}


### 返回实列

```
{
    "code": 0,
    "msg": "success",
    "data": null
}
```

### 返回参数

| 参数名        | 参数类型  | 说明                        |
| :-------------|:---------:| :---------------            |
| code          | int       | 错误码(0 表示没有错误)      |
| msg           | string    | 错误提示(success 成功)      |
| data          | mixed     | 返回其他数据                |

HTML;

    /**
     * 获取参数信息
     */
    public function actionIndex()
    {
        success([
            'method_list' => [
                'GET 请求'     => 'GET',
                'POST 请求'    => 'POST',
                'PUT 请求'     => 'PUT',
                'DELETE 请求'  => 'DELETE',
                'HEADER 请求'  => 'HEADER',
                'OPTIONS 请求' => 'OPTIONS',
            ],
            'type_list'   => [
                'int 类型'    => 'int',
                'string 类型' => 'string',
                'float 类型'  => 'float',
                'double 类型' => 'double',
                'mixed 类型'  => 'mixed',
            ],
        ]);
    }

    /**
     * 生成文档信息
     */
    public function actionCreate()
    {
        // 判断请求参数
        if (!$post_data = getValue($_POST, 'api')) {
            error(401, '请求参数存在问题');
        }

        $url = getValue($post_data, 'url');
        if (empty($url)) {
            error(401, 'url为空');
        }

        // 处理参数
        $params = getValue($post_data, 'params');
        if ($params) {
            $str = '';
            foreach ($params as $value) {
                $str .= '| ' . implode(' | ', $value) . ' | ' . "\n";
            }

            $post_data['params'] = $str;
        }

        success(str_replace(['{title}', '{url}', '{method}', '{params}'], [
            getValue($post_data, 'title', ''),
            getValue($post_data, 'url', ''),
            getValue($post_data, 'method', ''),
            getValue($post_data, 'params', ''),
        ], $this->template));
    }
}