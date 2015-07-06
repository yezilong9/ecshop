<?php 
/**
 * 天下密保验证类
 *
 * 
 * @author zjh
 *
 */

class txmb
{
    function __construct() {}
	
    /**
     * 验证天下密保
     * @param array $txmb_id    天下密保ID
     * @param array $txmb_pwd   天下密保动态密码
     * @return mix              验证通过 返回true|验证失败 返回“验证失败”
     */
    public static function check($txmb_id, $txmb_pwd)
    {
        return self::check_txmb($txmb_id, $txmb_pwd)?true:false;
    }
    
    /**
     * curl服务器验证天下密保
     * @param array $txmb_id    天下密保ID
     * @param array $txmb_pwd   天下密保动态密码
     * @return boolean          验证通过 返回true/验证失败 返回false
     */
    final static function check_txmb($txmb_id, $txmb_pwd)
    {
        $url = 'http://gz.gotogame.com.cn/cybercafe/interface/oa_txmb_chk.php';         // 服务器URL
        $merchantKey = 'IUfhi$#%-978dR?/1ih+_01jh~1`nnbDFH@iiuih+)(*&^!hbvzEWCs|jhbJ';  // 加密密钥
        $returnKey = 'f2i@#YhjbifSGWQi218&(*@dnvnzCCAd1112~!#@$%cnZBqjfhDFQu+_=91d';    // 返回密钥
        
        $mm = $txmb_id.$txmb_pwd.$merchantKey;
        $sign = md5($mm);
        
        $go = $url.'?id='.$txmb_id.'&pw='.$txmb_pwd.'&sign='.$sign;
        
        $obj_ch = curl_init();
        curl_setopt($obj_ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($obj_ch, CURLOPT_URL, $go);
        curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($obj_ch);
        curl_close($obj_ch);

        $arr_data = explode('&', $data);
        
        $returnId = '';
        $returnResult = '';
        $returnSign = '';
        
        for($i = 0; $i < count($arr_data); $i++)
        {
            $sub_strarray = explode('=', $arr_data[$i]);
            switch ($sub_strarray[0])
            {
                case 'id': $returnId = $sub_strarray[1]; break;
                case 'result': $returnResult = $sub_strarray[1]; break;
                case 'sign': $returnSign = $sub_strarray[1]; break;
            }
        }
        
        if($returnId == '' || $returnResult == '' || $returnSign == '') 
        {
            return false;
        }
        
        $mm2 = $returnResult.$txmb_id.$txmb_pwd.$returnKey;
        $chkReturnSign = md5($mm2);        
        if($chkReturnSign == $returnSign)
        {
            if($returnResult == '1')
            {
                return true;
            } 
            else 
            {
                return false;
            }
        } 
        else 
        {
            return false;
        }
    }
}
