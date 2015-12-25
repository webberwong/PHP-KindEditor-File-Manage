PHP KindEditor 编辑器文件管理类
====================================
用于编辑器浏览文件/图片的功能;管理后台上传文件,如图片;

### 程序使用
代码示例:(可查看tests/testAssets)<br>
```php
<?php
    //网站根目录,绝对位置,这个绝对位置一定要将windows的路径分隔符 '\' 转为 '/' 才能正常使用
    $siteRoot  = str_replace('\\','/',realpath(dirname(__DIR__))) . '/';
    //网站URL根目录,相对于域名,如果没有子目录,则留空
    $urlRoot   = '';
    //设置的文件根目录,比如Image在/upload/images
    $rootPath  = $siteRoot . 'testAssets/images';
    $kem = new KindEditorImageFileManage($siteRoot,$urlRoot,$rootPath);
    
    //设置浏览路径,即前端浏览到的目录路径,相对于设置文件根目录
    $kem->setVisitDirPath('/');
    //获取前端需要的数据,数组格式
    $result = $kem->getResult(true)
    //前端需要的数据为json的字符串,由使用该类的进行转换并输出
    //echo json_encode($result); 
?>
```
### 单元测试
```sh
#cmd run
cd tests
phpunit --bootstrap bootstrap.php targetTestFile.php
```