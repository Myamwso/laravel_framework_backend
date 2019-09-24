<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2018-07-30
 * Time: 11:17
 */

namespace App\Common;


use Illuminate\Support\Facades\Storage;

class UploadImg
{
    public function uploadImg($avatar)
    {

        if (!empty($avatar)) {
            if (!$avatar->isValid()) {
                return false;
            }else{
                //获取文件的扩展名
                $kuoname=$avatar->getClientOriginalExtension();
                //获取文件的绝对路径，但是获取到的在本地不能打开
                $path=$avatar->getRealPath();
                //要保存的文件名 时间+扩展名
                $filename=date('Y-m-d')  .'/'. uniqid() .'.'.$kuoname;
                //保存文件          配置文件存放文件的名字  ，文件名，路径
                $bool= Storage::disk('upimg')->put($filename,file_get_contents($path));
                if($bool){
                    return 'http://'.$_SERVER["SERVER_NAME"].config('filesystems.disks.upimg.url').'/'.$filename;
                }
            }
        }else{
//            return false;
            return 'sdfdsafsdaf.png';
        }

    }
}