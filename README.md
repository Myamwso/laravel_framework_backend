# 自助任务系统-PC管理端接口文档

[toc]

## 注意
> 注意每个接口请求的请求头信息（headers）里需要设置
```
headers: {
    Accept: "application/json; charset=utf-8",
    Pauth: "21rvj203f23|123123123|12"，//登录后服务端返回的登录凭证，需要自行保存在客户端
    DeviceId: "12312wqee12" //客户端id，前端开发自行设置，保持唯一性
    AppVersion: "1.0.0" //app接口版本号
}
```

## 接口域名
 - 正式：待发布提供
 - 测试：待开发完毕提供

## 全局服务
### 文件上传
 - 接口路径：/api/uploader
 - 请求方式：POST
 - 请求参数

参数 | 类型 | 备注
---|--- |----
type | int |  上传类型：1：图片
img | image/file | 上传的文件


 - 返回结果
```
    {
        "code":10000,
        "message":"success",
        "data": {
            "pre_url": "*****.png" //预览图片或其他格式文件
        }
    }
```

### 发送验证码
 - 接口路径：/api/sendCode
 - 请求方式：POST
 - 请求参数

参数 | 类型 | 备注
---|--- |----
type | string | 1：短信验证码
action | int | 1：注册；2：找回密码
vcode | string | 图片验证码
phone | string | 手机号

 - 返回结果
```
    {
        "code":10000,
        "message":"success",
        "data":[]
    }
    
    {
        "code":10005,
        "message":"vcode_error",
        "data":[]
    }
    
    {
        "code":10006,
        "message":"{limit}_seconds_can_resend",
        "data":[]
        }
    }
```

### 获取/刷新图片验证码地址
 - 接口路径：/api/vcode/{DeviceId}
 - 请求方式：GET
> {DeviceId} 与 header头的DeviceId一致

## 使用条款
 - 接口路径：/api/agreement
 - 请求方式：POST
 - 返回结果
```
    {
        "code":10000,
        "message":"success",
        "data": {
            "content": "使用条款内容"
        }
    }
```

## 账号 - 注册
 - 接口路径：/api/register
 - 请求方式：POST
 - 请求参数

参数 | 类型 | 备注
---|--- |----
user_name | string | 手机号
password | string | 数字字母下划线，长度8-15位
code | string | 短信验证码

 - 返回结果
```
    {
        "code":10000,
        "message":"success",
        "data":[]
    }
    
    {
        "code":10002,
        "message":"code_error",
        "data":[]
    }
    
    {
        "code":10003,
        "message":"phone_format_error",
        "data":[]
    }
    
    {
        "code":10004,
        "message":"password_format_error",
        "data":[]
    }
     
    {
        "code":10008,
        "message":"phone_exist",
        "data":
    }
```

## 账号 - 登录
 - 接口路径：/api/login
 - 请求方式：POST
 - 请求参数

参数 | 类型 | 备注
---|--- |----
user_name | string | 手机号
password | string | 密码

 - 返回结果
```
    {
        "code":10000,
        "message":"success",
        "data":{
            Pauth:"21rvj203f23|123123123|12" //登录状态校验值
        }
    }
    
    {
        "code":10009,
        "message":"login_error",
        "data":[]
    }
```

## 账号 - 找回密码
 - 接口路径：/api/findPassword
 - 请求方式：POST
 - 请求参数

参数 | 类型 | 备注
---|--- |----
user_name | string | 手机号
password | string | 密码
code | string | 短信验证码

 - 返回结果
```
    {
        "code":10000,
        "message":"success",
        "data":[]
    }
    
    {
        "code":10002,
        "message":"code_error",
        "data":[]
    }
    
    {
        "code":10003,
        "message":"phone_format_error",
        "data":[]
    }
    
    {
        "code":10004,
        "message":"password_format_error",
        "data":[]
    }
     
    {
        "code":10008,
        "message":"phone_exist",
        "data":
    }
```

## 账号 - 退出登录
 - 接口路径：/api/logout
 - 请求方式：GET
 - 返回结果
```
    {
        "code":10000,
        "message":"success",
        "data":[]
    }
```


## code释意

提示码 | 信息
---|---
10000 | 请求成功
10001 | 请求执行失败
10002 | 短信验证码错误
10003 | 手机格式错误
10004 | 密码格式错误
10005 | 图片验证码错误
10006 | 短信验证码每隔60s才能重发
10008 | 手机号码已存在
10009 | 用户名或密码错误
10011 | 支付进行中
10012 | 参数错误
20000 | pauth错误或者已过期，请重新登录