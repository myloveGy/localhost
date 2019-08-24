<?php

// 文件路径
define('PUBLIC_FILE', './file/image/');

if (!function_exists('getValue')) {
    /**
     * 获取数组的值
     *
     * @param array|mixed                $array
     * @param string|array|Closure|mixed $key
     * @param null                       $default
     *
     * @return mixed|null
     */
    function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = getValue($array, substr($key, 0, $pos), $default);
            $key   = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }
}

/**
 * 上传文件处理
 *
 * @param string $file    上传文件名称
 * @param string $dirName 保存目录
 * @param array  $params  上传其他配置参数
 *                        ```
 *                        [
 *                        size       => 10000000,                      // 允许上传文件大小kb
 *                        allow_type => ['jpg', 'jpeg', 'gif', 'png'], // 允许类型
 *                        prefix     => '',                            // 文件前缀名称
 *                        ]
 *                        ```
 *
 * @return array
 */
function fileUpload($file, $dirName = 'uploads', array $params = [])
{
    $uploadSize = getValue($params, 'size', 10000000);
    $allowType  = getValue($params, 'allow_type', ['jpg', 'jpeg', 'gif', 'png']);
    $prefixName = getValue($params, 'prefix', '');

    // 1、判断是否已经上传
    if (empty($_FILES[$file]) || empty($_FILES[$file]['name'])) {
        return [false, '没有上传文件', 1];
    }

    // 3、判断上传错误信息
    if ($_FILES[$file]['error'] > 0) {
        return [false, '上传出现错误', $_FILES[$file]['error']];
    }

    // 4、判断上传文件大小
    if ($_FILES[$file]['size'] > $uploadSize) {
        return [false, '上传文件过大', 8];
    }

    // 5、判断上传文件类型
    $extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION); // 获取文件后缀名
    $extension = strtolower($extension); // 后缀名转小写
    if (!in_array($extension, $allowType)) {
        return [false, '上传文件类型错误', 9];
    }

    // 6、判断文件是否通过HTTP POST 上传的
    if (!is_uploaded_file($_FILES[$file]['tmp_name'])) {
        return [false, '上传方式错误', 10];
    }

    // 7、创建上传文件目录创建目录
    $dir = trim($dirName, '/');
    if (!file_exists($dir)) {
        @mkdir($dir, 0777, true);
    }

    if (!file_exists($dir)) {
        return [false, '创建上传目录失败', 11];
    }

    // 文件名称
    $randName = empty($prefixName) ? 'tmp_' . uniqid() : $prefixName . '_' . pathinfo($_FILES[$file]['name'], PATHINFO_FILENAME);
    $randName .= '.' . $extension;
    $filePath = $dir . $randName; // 上传文件移动后的完整文件路径

    // 8、移动上传文件到指定上传文件目录
    if (!move_uploaded_file($_FILES[$file]['tmp_name'], $filePath)) {
        return [false, '移动上传文件失败', 11];
    }

    return [true, '上传成功', array_merge($_FILES[$file], [
        'file_name' => $randName,
        'file_path' => $filePath,
        'extension' => $extension,
    ])];
}

class Csv
{
    /**
     * 读取CSV文件
     *
     * @param string $csv_file csv文件路径
     *
     * @return array|bool
     */
    public function read($csv_file = '')
    {
        $data = [];
        if (!$fp = fopen($csv_file, 'r')) {
            return $data;
        }

        while ($info = fgetcsv($fp)) {
            $data[] = $info;
        }

        fclose($fp);

        return $data;
    }

    /**
     * 导出CSV文件
     *
     * @param string $fileName 文件名称
     * @param array  $data     数据
     *
     * @return string
     */
    public function export($fileName, array $data)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $fileName);
        header('Cache-Control: max-age=0');
        $fp  = fopen('php://output', 'a');
        $num = 0;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        //逐行取出数据，不浪费内存
        $count = count($data);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $num++;
                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }
                $row = $data[$i];
                foreach ($row as $key => $value) {
                    $row[$key] = iconv('utf-8', 'gb2312', $value);
                }
                fputcsv($fp, $row);
            }
        }
        fclose($fp);
        exit;
    }
}

if (!empty($_FILES['file'])) {
    list($ok, $message, $data) = fileUpload('file', './', ['allow_type' => ['csv']]);
    if ($ok) {
        $csv      = new Csv();
        $filePath = getValue($data, 'file_path');
        $array    = $csv->read($filePath);
        @unlink($filePath);
        if ($array) {
            foreach ($array as $key => &$value) {
                if ($key == 0) {
                    continue;
                }

                if (!$url = getValue($value, '10')) {
                    continue;
                }

                if (substr($url, 0, 1) == '/') {
                    continue;
                }

                if (!$file = file_get_contents($url)) {
                    continue;
                }

                $fileName = PUBLIC_FILE . date('YmdHis') . '-' . uniqid() . '.jpeg';
                $dirname  = dirname($fileName);
                if (!file_exists($dirname)) {
                    mkdir($dirname, 0777, true);
                }

                file_put_contents($fileName, $file);
                $value[10] = ltrim($fileName, '.');
            }

            $name          = getValue($data, 'name');
            $fileNameArray = explode('.', $name);
            $csv->export(getValue($fileNameArray, 0) . '.csv', $array);
        }
    }
}

?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>
        .content {
            position: absolute;
            width: 600px;
            height: 200px;
            left: 50%;
            top: 20%;
            margin-left: -200px;
            margin-top: -50px;
        }

        form {
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="content">
    <form method="post" enctype="multipart/form-data" class="form-inline">
        <div class="form-group">
            <label for="file">上传文件</label>
            <input type="file" class="form-control" id="file" name="file" placeholder="Jane Doe">
        </div>
        <button type="submit" class="btn btn-success">上传文件</button>
    </form>
</div>
</body>
</html>



