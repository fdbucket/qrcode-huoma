<?php
	
  // 微信支付商户平台商户号
  $mchid = 'xxx';

  // 服务号APPID（一定要认证的服务号）
  $appid = 'xxx';

  // 服务号APPSCRECT（一定要认证的服务号）
  $appKey = 'xxx';

  // 微信支付商户平台->帐户设置->安全设置->API安全->API密钥->设置API密钥
  $apiKey = 'xxx';

  // 异步订单通知url，填写的是 addons/ffjq/home/ffq_notify.php 这个文件的线上url
  // 例如你的域名是www.qq.com，活码安装目录在根目录下的huoma目录
  // 那么填写 http://www.qq.com/huoma/addons/ffjq/home/ffq_notify.php 就行了
  // --------------------------------------------------------------------------
  $ffjq_notify_url = "http://www.qq.com/huoma/addons/ffjq/home/ffq_notify.php";

  // 除了以上配置之外还需
  // ---------------------
  // （1）配置网页授权域名
  // （2）配置微信支付商户平台JSAPI支付目录
  
  
  
  // （1）配置网页授权域名方法
  // --------------------
  // 登录服务号->设置与开发->公众号设置->网页授权域名，将你活码的安装目录填进去
  // -----------------------------------------------------------
  // 例如你的域名是www.qq.com，活码安装目录在根目录下的huoma目录
  // --------------------------------------------------
  // 那么网页授权域名填写的就是 www.qq.com/huoma
  
  
  
  // （2）配置微信支付商户平台JSAPI支付目录方法
  // -----------------------------------
  // 打开 https://pay.weixin.qq.com 登录
  // 点击产品中心->开发配置->支付配置->JSAPI支付支付授权目录
  // 将插件的线上地址填写进去 addons/ffjq/home/
  // 例如你的域名是www.qq.com，活码安装目录在根目录下的huoma目录
  // --------------------------------------
  // 那么你的JSAPI支付支付授权目录就可以填写
  // http://www.qq.com/huoma/addons/ffjq/home/
  
  
  // 完成以上配置
  // 你的付费进群就可以正常使用
  // 本文更新时间：2022/11/17
  
?>
