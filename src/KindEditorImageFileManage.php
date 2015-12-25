<?php
namespace Hwl\KindEditor;
/**
 * 适用于KindEditor的图片管理器
 * 测试版本KindEditor v4.10
 * 待完善的功能,文件排序
 * @author Hwl<weigewong@gmail.com>
 */
class KindEditorImageFileManage{

    /**
     * 网站根目录绝对地址
     * @var string
     */
    private $OS_SITE_ROOT_PATH;
    /**
     * 网站根目录url路径
     * 有时候你的站点不是在根目录,而是在子目录的情况下
     * @var string
     */
    private $SITE_ROOT_URL;

    /**
     * 文件系统里的文件根目录,绝对路径
     * @var string
     */
    private $OS_FILE_ROOT_PATH;


    /**
     * 访问的文件目录路径,由前端传来的路径,相对于文件根目录
     * @var string
     */
    private $VISIT_FILE_DIR_PATH;

    /**
     * 当前目录的URL地址,相对于网站根目录
     * @var string
     */
    private $CURRENT_IMAGE_DIR_URL;
    /**
     * 文件格式,这里默认为图片格式
     * @var array
     */
    private $filterExt = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

    /**
     * 初始化KindEditor后台图片管理器类
     * @param string $os_site_root_path  文件系统里的网站根目录,绝对路径
     * @param string $site_root_url      网站根目录url路径
     * @param string $os_file_root_path  文件系统里的文件根目录,绝对路径
     */
    public function __construct($os_site_root_path,$site_root_url,$os_file_root_path){
        $this->OS_SITE_ROOT_PATH  = $os_site_root_path;
        $this->SITE_ROOT_URL      = $site_root_url;
        $this->OS_FILE_ROOT_PATH  = $os_file_root_path;
    }

    /**
     * 获取上一级目录的相对于图片根目录的路径
     * 当该目录上一级超过图片根目录,会直接返回根目录地址,不允许获取上一级
     * @return string
     */
    protected function getParentFolderPathBaseImageRootPath(){
        return preg_replace('/(.*?)[^\/]+\/$/', '$1', $this->VISIT_FILE_DIR_PATH);
    }

    /**
     * 设置文件当前的目录URL地址,这个好像不需要用到
     */
    public function setCurrentFileDirUrl(){
        $visit_real_path = str_replace('\\' ,'/' ,realpath($this->OS_FILE_ROOT_PATH . $this->VISIT_FILE_DIR_PATH));
        $this->CURRENT_IMAGE_DIR_URL =  $this->SITE_ROOT_URL . '/' . str_replace($this->OS_SITE_ROOT_PATH,'',$visit_real_path) . '/';
    }

    /**
     * 获取文件夹里的文件和目录列表
     * 该方法只用来获取目录名和文件名,严格模式只显示过滤的文件名
     * @param string $folderPath    文件系统的文件夹路径,绝对路径
     * @param array  $filterImgExt  图片格式
     * @return array
     */
    public function getFolderFileList($folderPath,$filterImgExt,$isStrict = false){
        //直接使用示例代码
        $file_list = array ();
        if ($handle = opendir ( $folderPath )) {
            $i = 0;
            while ( false !== ($filename = readdir ( $handle )) ) {
                if ($filename {0} == '.')
                    continue;
                $file = $folderPath . $filename;
                if (is_dir ( $file )) {
                    $file_list [$i] ['is_dir'] = true; // 是否文件夹
                    $file_list [$i] ['has_file'] = (count ( scandir ( $file ) ) > 2); // 文件夹是否包含文件
                    $file_list [$i] ['filesize'] = 0; // 文件大小
                    $file_list [$i] ['is_photo'] = false; // 是否图片
                    $file_list [$i] ['filetype'] = ''; // 文件类别，用扩展名判断
                } else {
                    if($isStrict){
                        //如果不在过滤的列表格式中,则忽略
                        if(!in_array(strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) ), $filterImgExt)){
                            continue;
                        }
                    }
                    $file_list [$i] ['is_dir'] = false;
                    $file_list [$i] ['has_file'] = false;
                    $file_list [$i] ['filesize'] = filesize ( $file );
                    $file_list [$i] ['dir_path'] = '';
                    $file_ext = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );
                    $file_list [$i] ['is_photo'] = in_array ( $file_ext, $filterImgExt );
                    $file_list [$i] ['filetype'] = $file_ext;
                }
                $file_list [$i] ['filename'] = $filename; // 文件名，包含扩展名
                $file_list [$i] ['datetime'] = date ( 'Y-m-d H:i:s', filemtime ( $file ) ); // 文件最后修改时间
                $i ++;
            }
        }
        closedir ( $handle );
        return $file_list;
    }

    /**
     * 获取可以直接返回到前端的数组
     * @param bool $isStrictExt 是否严格控制文件扩展名,即只显示过滤的文件扩展名文件
     * @return array;
     */
    public function getResult($isStrictExt = false){
        $this->setCurrentFileDirUrl();
        $result = array ();
        // 相对于根目录的上一级目录
        $result ['moveup_dir_path']  =  $this->getParentFolderPathBaseImageRootPath();
        // 相对于根目录的当前目录
        $result ['current_dir_path'] =  $this->VISIT_FILE_DIR_PATH;
        // 当前目录的URL
        $result ['current_url'] = $this->CURRENT_IMAGE_DIR_URL;
        // 文件列表数组
        $result ['file_list']   = $this->getFolderFileList($this->OS_FILE_ROOT_PATH . $this->VISIT_FILE_DIR_PATH, $this->filterExt ,$isStrictExt);
        // 文件数
        $result ['total_count'] = count ( $result ['file_list'] );

        return $result;
    }

    /**
     * 设置图片格式
     * @param array $filterExt 图片格式数组array('jpg','png',...)
     */
    public function setFilterExt($filterExt){
        $this->filterExt = $filterExt;
    }
    /**
     * 设置访问的图片目录路径,由前端传来的路径
     * 该路径需要相对于图片目录路径
     * @param string $path
     */
    public function setVisitDirPath($path){
        $this->VISIT_FILE_DIR_PATH = $path;
    }
}