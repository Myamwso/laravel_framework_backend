<?php

namespace App\Services;

use App\Config;
use JohnLui\AliyunOSS;
use Exception;
use DateTime;

class OSS {

    private $ossClient;

    /**
     * 私有初始化 API，非 API，不用关注
     * @param boolean 是否使用内网
     */
    public function __construct($isInternal = false)
    {
        $city = Config::where('key','city')->first()['value'];
        $networkType = Config::where('key','networkType')->first()['value'];
        $AccessKeyId = Config::where('key','AccessKeyId')->first()['value'];
        $AccessKeySecret = Config::where('key','AccessKeySecret')->first()['value'];
        $this->bucketName = Config::where('key','bucketName')->first()['value'];
        if ($networkType == 'VPC' && !$isInternal) {
            throw new Exception("VPC 网络下不提供外网上传、下载等功能");
        }
        $this->ossClient = AliyunOSS::boot(
            $city,
            $networkType,
            $isInternal,
            $AccessKeyId,
            $AccessKeySecret

        );
    }


    /**
     * 使用外网上传文件
     * @param  string bucket名称
     * @param  string 上传之后的 OSS object 名称
     * @param  string 删除文件路径
     * @return boolean 上传是否成功
     */
    public static function publicUpload($ossKey, $filePath, $options = [])
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->uploadFile($ossKey, $filePath, $options);
    }

    /**
     * 使用阿里云内网上传文件
     * @param  string bucket名称
     * @param  string 上传之后的 OSS object 名称
     * @param  string 删除文件路径
     * @return boolean 上传是否成功
     */
    public static function privateUpload($ossKey, $filePath, $options = [])
    {
        $oss = new OSS(true);
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->uploadFile($ossKey, $filePath, $options);
    }


    /**
     * 使用外网直接上传变量内容
     * @param  string bucket名称
     * @param  string 上传之后的 OSS object 名称
     * @param  string 删除传的变量
     * @return boolean 上传是否成功
     */
    public static function publicUploadContent($ossKey, $content, $options = [])
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->uploadContent($ossKey, $content, $options);
    }

    /**
     * 使用阿里云内网直接上传变量内容
     * @param  string bucket名称
     * @param  string 上传之后的 OSS object 名称
     * @param  string 删除传的变量
     * @return boolean 上传是否成功
     */
    public static function privateUploadContent($ossKey, $content, $options = [])
    {
        $oss = new OSS(true);
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->uploadContent($ossKey, $content, $options);
    }


    /**
     * 使用外网删除文件
     * @param  string bucket名称
     * @param  string 目标 OSS object 名称
     * @return boolean 删除是否成功
     */
    public static function publicDeleteObject($ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->deleteObject($ossKey);
    }

    /**
     * 使用阿里云内网删除文件
     * @param  string bucket名称
     * @param  string 目标 OSS object 名称
     * @return boolean 删除是否成功
     */
    public static function privateDeleteObject($ossKey)
    {
        $oss = new OSS(true);
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->deleteObject($ossKey);
    }


    /**
     * -------------------------------------------------
     *
     *
     *  下面不再分公网内网出 API，也不注释了，大家自行体会吧。。。
     *
     *
     * -------------------------------------------------
     */

    public function copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS();
        return $oss->ossClient->copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }

    public function moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS();
        return $oss->ossClient->moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }

    // 获取公开文件的 URL
    public static function getPublicObjectURL($ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->getPublicUrl($ossKey);
    }
    // 获取私有文件的URL，并设定过期时间，如 \DateTime('+1 day')
    public static function getPrivateObjectURLWithExpireTime($ossKey, DateTime $expire_time)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($oss->bucketName);
        return $oss->ossClient->getUrl($ossKey, $expire_time);
    }

    public static function createBucket($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->createBucket($bucketName);
    }

    public static function getAllObjectKey($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->getAllObjectKey($bucketName);
    }

    public static function getObjectMeta($ossKey)
    {
        $oss = new OSS();
        return $oss->ossClient->getObjectMeta($ossKey);
    }


}
