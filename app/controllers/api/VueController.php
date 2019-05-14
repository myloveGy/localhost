<?php
/**
 *
 * WebController.php
 *
 * Author: jinxing.liu@verystar.cn
 * Create: 2019-05-13 09:37
 * Editor: created by PhpStorm
 */

namespace app\controllers\api;

class VueController
{
    // hosts PHP_OS 文件地址
    const HOST_PATH = [
        'Darwin' => '/usr/local/etc/nginx/servers',
        'Linux'  => '/www/docker/vhost',
    ];

    // 资源目录
    const RESOURCE_DIR = '/resource/';

    // 处理目录
    const HANDLE_DIR_PATH = './';

    // 定义需要隐藏的文件
    const NEED_HIDE_FILES = [
        '.', '..', 'index.php',
        'index.html', '.git', '.gitignore',
        'ace.zip', 'vue-api.php', 'vhost.php',
        '.idea', '.DS_Store', 'css',
        'js', 'static', 'php'
    ];

    // 允许显示的文件类型
    const FILE_EXTENSIONS = ['.html', '.php'];

    /**
     * 站点信息
     */
    public function actionIndex()
    {
        // 定义站点信息 域名 => 说明
        $arrWebs = [
            'title' => '本地站点',
            'lists' => [
                [
                    'name' => 'localhost',
                    'href' => 'http://localhost',
                ],
            ],
        ];

        // 存放虚拟目录路径
        $dirHost = getValue(static::HOST_PATH, PHP_OS);
        $hosts   = getPathFileName($dirHost);
        if ($hosts) {
            foreach ($hosts as $value) {
                $arrWebs['lists'][] = [
                    'href' => 'http://' . $value,
                    'name' => $value,
                ];
            }
        }

        $arrDirs = [
            'title' => '项目目录',
            'lists' => [],
        ];

        $arrFiles = [
            'title' => '文件',
            'lists' => [],
        ];

        // 目录操作
        $resource = opendir(static::HANDLE_DIR_PATH);
        // 循环处理
        while (!!$filename = readdir($resource)) {
            // 去掉隐藏文件
            if (in_array($filename, static::NEED_HIDE_FILES)) {
                continue;
            }

            // 文件和目录区分
            $arrTmp = [
                'href' => $filename,
                'name' => $filename === 'test.php' ? '测试脚本' : $filename,
            ];

            if (strpos($filename, '.')) {
                if (in_array(strrchr($filename, '.'), static::FILE_EXTENSIONS)) {
                    $arrFiles['lists'][] = $arrTmp;
                }
            } else {
                $arrDirs['lists'][] = $arrTmp;
            }
        }

        success([$arrWebs, $arrDirs, $arrFiles]);
    }

    /**
     * phpinfo 信息
     */
    public function actionInfo()
    {
        phpinfo();
    }

    /**
     * php 版本信息
     */
    public function actionPhp()
    {
        success([
            'version' => 'PHP ' . PHP_VERSION,
            'os'      => PHP_OS,
        ]);
    }
}