# 微信支付平台证书

~~~
java -jar CertificateDownloader.jar -k ${apiV3key} -m ${mchId} -f ${mchPrivateKeyFilePath} -s ${mchSerialNo} -o ${outputFilePath}

~~~


必需参数有：

-f  商户API私钥文件路径 对应是的 证书KEY，也就是app.key 

-k ，证书解密的密钥 

-m ，商户号 

-o ，保存证书的路径 

-s ，商户API证书的序列号

## 查看平台证书序列号

~~~
openssl x509 -in 平台证书.pem -noout -serial
~~~




# 生成php文档

从 https://phpdoc.org 下载 phpDocumentor.phar 

生成php文档

~~~
php phpDocumentor.phar run -d $(pwd)/../modules -t ./phpdoc -v
~~~
 

