<?php

use Hwl\KindEditor\KindEditorImageFileManage;

/**
 * KindEditor图片文件管理测试类
 * 现在将include bootstrap.php去掉,所以需要加上参数指定指导文件的值
 * phpunit使用时记得加--bootstrap bootstrap.php
 * Class KindEditorImageFileManageTest
 * @author Hwl<weigewong@gmail.com>
 */
class KindEditorImageFileManageTest extends \PHPUnit_Framework_TestCase{
    private $kem = null;

    public function __construct(){
        //网站根目录,绝对位置,这个绝对位置一定要将windows的路径分隔符转为 /号才能正常使用
        $siteRoot  = str_replace('\\','/',realpath(dirname(__DIR__))) . '/';
        //网站URL根目录,相对于域名,如果没有子目录,则留空
        $urlRoot   = '';
        //设置的文件根目录,比如Image在/upload/images
        $rootPath  = $siteRoot . 'testAssets/images';
        $this->kem = new KindEditorImageFileManage($siteRoot,$urlRoot,$rootPath);
    }

    public function testGetResult(){
        //设置当前浏览的目录路径,相对于文件根目录即$rootPath
        $this->kem->setVisitDirPath('/');
        $result = $this->kem->getResult(true);

        $this->assertEquals($result['current_url'],'/testAssets/images/');
        $this->assertEquals(is_array($result['file_list']),true);
    }
}