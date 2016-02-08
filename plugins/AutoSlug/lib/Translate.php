<?php
class DuTranslate
{
    /** 百度翻译 API APPID */
    private $_apiID;
    /** 百度翻译 API 密钥 */
    private $_apiSec;

    /**
     * 构造函数
     *
     * @param string $apiKey 百度翻译 API APPID
     * @param string $apiSec 百度翻译 API 密钥
     * @return void
     */
    public function __construct($apiKey = NULL,$apiSec = NULL)
    {
        /** 获取 API APPID 和 密钥 */
        $this->_apiID = $apiKey;
        $this->_apiSec = $apiSec;
    }
    /**
     * 翻译
     *
     * @access public
     * @param string $word 待翻译的字符串
     * @param string $from 翻译前的语言
     * @param string $to 翻译后的语言
     * @return string
     */
    public function transform($word, $from = 'zh', $to = 'en')
    {
        /** 构建请求地址及参数 */
        $url = 'http://api.fanyi.baidu.com/api/trans/vip/translate';
        $post = array(
            'q' => $word,
            'appid' => $this->_apiID,
            'salt' => rand(10000,99999),
            'from' => $from,
            'to' => $to,
        );
        /** 拼接生成签名 */
        $str = $this->_apiID.$word.$post['salt'].$this->_apiSec;
        $post['sign'] = md5($str);

        /** 配置 cURL 选项 */
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($post),
            CURLOPT_TIMEOUT => 60
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        if (!$result = curl_exec($ch)) {
            return false;
        }
        curl_close($ch);
        $result = json_decode($result, true);

        /** 返回翻译错误 */
        if (isset($result['error_code'])) {
            return false;
        }

        /** 去除标点符号及转换成小写 */
        $result = $result['trans_result'][0]['dst'];
        $result = preg_replace('/[[:punct:]]/', '', $result);
        $result = strtolower(trim($result));

        return $result;
    }

}
