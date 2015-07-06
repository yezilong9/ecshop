<?php
/*短信发送类*/
class MobileNotice 
{
    private $project_id     = '108';//账号
    private $password       = 'TaVfJKW3FK6tDFkLfEOSSNH6';  //密码
    private $url          = 'http://smsapi.ba.com/send/submit'; //访问链接·
    
    //private $mch_id         = '7';//账号
    //private $password       = '5d3ptbpvrucrn8a4vd7ue4vf';  //密码
    //private $url            = 'http://smsapi.gotogame.com.cn/send/submit'; //·访问链接
    
    //发送短信
    public function send($mobil, $code)
    {
        $method = 'post';
        $str_qstring = $str_string = $sign = '';
        //$content_ms = $this->iconv_charset("欢迎通过手机号码注册天下店用户,你的验证码:",'GBK','UTF-8');
        $content_ms = "欢迎通过手机号码注册天下店用户,你的验证码:";
        $content_base = $content_ms.$code;
        $content      = urlencode($content_ms.$code); 
        
        // 传了三个参数：账号， 手机号码，发送的内容
        $str_qstring = 'project_id='.$this->project_id.'&phone='.$mobil.'&content='.$content;
        //$str_qstring = 'mch_id='.$this->mch_id.'&phone='.$mobil.'&content='.$content;
        //$str_qstring = $this->iconv_charset($str_qstring,'GBK','UTF-8');
        
        $str_string_md = $content_base.$mobil.$this->project_id;
        //$str_string_md = $content.$this->mch_id.$mobil;
        //$str_string_md = $this->iconv_charset($str_string_md,'GBK','UTF-8');

        
        $sign = md5($str_string_md.$this->password);
        
        $str_qstring = $str_qstring.'&sign='.$sign;
        //echo $str_qstring;exit;
        
        $result = $this->inc_fun_sendRequest($this->url,$str_qstring, $method);
        //var_dump($result)  ; exit;
        switch($result){
            case 0000: $return = '000'; break;
            case 1000: $return = '001'; break;
            case 1001: $return = '002'; break;
            case 1002: $return = '003'; break;
            case 1003: $return = '004'; break;
            case 1004: $return = '005'; break;
            case 1005: $return = '006'; break;
        }
        return $return;        
        
    }
    
    public function inc_fun_sendRequest($str_bgUrl,$str_qstring='',$method='post')
    {
        $ch = curl_init();
        //echo $str_bgUrl; echo "\n"; echo $str_qstring; echo "\n";  echo $method; echo "\n";exit;
        if(strtolower($method) == 'get')
        {
            curl_setopt($ch, CURLOPT_URL, $str_bgUrl.'?'.$str_qstring);
        }
        else
        {
            curl_setopt($ch, CURLOPT_URL, $str_bgUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_qstring);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);       
        curl_setopt($ch, CURLOPT_HEADER, 0);          
        $data = curl_exec($ch);
        //var_dump($data);
        if(curl_errno($ch) != 0)
        {
            $int_err_code = '9999';
            $str_err_msg = '接口通知失败:网络错误.'.curl_error($ch);
        }
        curl_close($ch);
        if($int_err_code) return false;
        return $data;
    }
    
    /*************************************************************
     * 参数说明
     * fContents：需要转换编码的数据源
     * from：数据源内容编码
     * to:   转换后的数据内容编码
     *************************************************************/
    public function iconv_charset ($fContents, $from, $to) 
    {
        $from = strtoupper($from);
        $to   = strtoupper($to);
        if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) 
        {
            //如果编码相同或者非字符串标量则
            return $fContents;
        }
        if (is_string($fContents)) 
        {
            if (function_exists('mb_convert_encoding')) 
            {
                return @mb_convert_encoding($fContents, $to, $from);
            } 
            elseif (function_exists('iconv')) 
            {
                return iconv($from, $to, $fContents);
            } 
            else 
            {
                return $fContents;
            }
        } 
        elseif (is_array($fContents)) 
        {
            foreach ($fContents as $key => $val) {
                $_key = iconv_charset($key, $from, $to);
                $fContents[$_key] = iconv_charset($val, $from, $to);
                if ($key != $_key)
                    unset($fContents[$key]);
            }
            return $fContents;
        }  
        else 
        {
            return $fContents;
        }
    }
   

}

?>