<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;
use phpseclib3\Crypt\RSA as RsaHelper;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\NewException\NoKeyLoadedException;
use phpseclib3\NewException\UnsupportedFormatException;

/**
 * RSA 加密解密助手
 * @example
 * <pre>
 * <code>
 * use app\modules\core\classes\Rsa;
 * $keys = Rsa::create();
 * pr($keys);
 * $data = Rsa::encode(['a'=>1]);
 * pr(Rsa::decode($data));
 * </code> 
 * </pre>
 */
class Rsa
{
    /**
     * 生成 RSA 密钥对
     * @return array 返回包含公钥和私钥的数组，格式为 PKCS8
     * @throws \NewException 如果密钥生成失败
     */
    public static function create()
    {
        try {
            // 创建新的 RSA 私钥
            $privateKey = RsaHelper::createKey(2048); // 指定密钥长度为2048位
            // 从私钥获取公钥
            $publicKey = $privateKey->getPublicKey();
            // 返回 PKCS8 格式的密钥对
            return [
                'privatekey' => $privateKey->toString('PKCS8'),
                'publickey' => $publicKey->toString('PKCS8')
            ];
        } catch (\NewException $e) {
            throw new \NewException('密钥生成失败: ' . $e->getMessage());
        }
    }

    /**
     * 使用公钥加密数据
     * @param string $data 要加密的明文数据
     * @param string $public_key 公钥（PKCS8 格式）
     * @return string 返回 base64 编码的密文
     * @throws \NewException 如果公钥无效或加密失败
     */
    public static function encode($data, $public_key = '')
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        if (!$public_key) {
            $file = Yii::getAlias('@app/data/publickey.txt');
            if (!file_exists($file)) {
                throw new \NewException('公钥文件不存在');
            }
            $public_key = file_get_contents($file);
        }
        try {
            if (empty($public_key)) {
                throw new \NewException('公钥不能为空');
            }
            // 加载公钥
            $key = PublicKeyLoader::loadPublicKey($public_key);
            // 使用公钥加密数据
            $ciphertext = $key->encrypt($data);
            if ($ciphertext === false) {
                throw new \NewException('加密失败');
            }
            // 将密文编码为 base64 格式
            return base64_encode($ciphertext);
        } catch (NoKeyLoadedException $e) {
            throw new \NewException('无效的公钥: ' . $e->getMessage());
        } catch (UnsupportedFormatException $e) {
            throw new \NewException('不支持的公钥格式: ' . $e->getMessage());
        }
    }

    /**
     * 使用私钥解密数据
     * @param string $data base64 编码的密文
     * @param string $private_key 私钥（PKCS8 格式）
     * @return string 返回解密后的明文
     * @throws \NewException 如果私钥无效或解密失败
     */
    public static function decode($data, $private_key = '')
    {
        if (!$private_key) {
            $file = Yii::getAlias('@app/data/privatekey.txt');
            if (!file_exists($file)) {
                throw new \NewException('私钥文件不存在');
            }
            $private_key = file_get_contents($file);
        }
        try {
            if (empty($private_key)) {
                throw new \NewException('私钥不能为空');
            }
            // 解码 base64 编码的密文
            $data = base64_decode($data, true);
            if ($data === false) {
                throw new \NewException('base64 解码失败');
            }
            // 加载私钥
            $key = PublicKeyLoader::loadPrivateKey($private_key);
            // 使用私钥解密数据
            $plaintext = $key->decrypt($data);
            if ($plaintext === false) {
                throw new \NewException('解密失败');
            }
            // 尝试将 JSON 字符串转换为数组
            $jsonData = json_decode($plaintext, true);
            if ($jsonData !== null) {
                return $jsonData;
            }
            return $plaintext;
        } catch (NoKeyLoadedException $e) {
            throw new \NewException('无效的私钥: ' . $e->getMessage());
        } catch (UnsupportedFormatException $e) {
            throw new \NewException('不支持的私钥格式: ' . $e->getMessage());
        }
    }
}
