<?php
/**
 * Created by PhpStorm.
 * User: xubowen
 * Date: 2018/1/12
 * Time: 下午7:21
 */
$order=1;
$myfile = fopen("qh.txt", "w") or die("Unable to open file!");
while(true){
    //$dirt='{"code":0,"msg":"成功","data":{"event":{"answerTime":10,"desc":"11.动画片《哆啦a梦》中的胖虎是什么星座？","displayOrder":1,"liveId":98,"options":"[\"双子座\",\"白羊座\",\"狮子座\"]","questionId":1184,"showTime":1515734054248,"status":0,"type":"showQuestion"},"type":"showQuestion"}}';
    $dirt=getQuestion('http://htpmsg.jiecaojingxuan.com/msg/current');
    $json=json_decode($dirt, true);
    $ques_msg=$json['msg'];
    if($ques_msg=='no data'){
        sleep(1);
    }else if($json['data']['event']['displayOrder']==$order){
            $ques_desc=$json['data']['event']['desc'];
            $ques_options=$json['data']['event']['options'];
            fwrite($myfile, formString($ques_desc)."\n");
            foreach (formOptions($ques_options) as &$select){
                fwrite($myfile, $select."\n");
            }
            fwrite($myfile, "\n\n\n");
            $order++;
            sleep(10);
    }else{
        sleep(1);
    }
    if($order==12)break;
}
//删除空格
function trimall($str){
    $oldchar=array(" ","　","\t","\n","\r");
    $newchar=array("","","","","");
    return str_replace($oldchar,$newchar,$str);
}
//格式化options
function formOptions($str){
    if($str=="") return ;
    $result = array();
    preg_match_all("/(?:\[)(.*)(?:\])/i",$str, $result);
    preg_match_all("#\"(.*?)\"#i",$result[1][0], $result);
    return $result[1];
}
//获取问题
function getQuestion($url){
    $ch = curl_init();
    $timeout = 5;
    curl_setopt ($ch, CURLOPT_URL,$url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36');
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $html = curl_exec($ch);
    curl_close ($ch);
    return $html;
}


//格式化问题内容
function formString($str){
    $desc=$str;
    $desc=chop($desc,"？");//去掉？
    $desc=chop($desc,"?");//去掉?
    $index=strpos($desc,".")+1;//获取.的位置
    $res=substr($desc, $index);
    //echo "格式化问题---".$res;
    return $res; //截取.后的内容
}