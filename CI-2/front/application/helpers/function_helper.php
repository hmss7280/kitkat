<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function gnb($depth=""){
	$CI =& get_instance();
	$gnb=$CI->load->view("common/gnb",array("depth"=>$depth),true);

	return $gnb;
}
function _alert($str, $url="") {

	header('Content-Type: text/html; charset=UTF-8');

	$script = "<script type=\"text/javascript\">";
	$script .= "alert('" . $str . "');";
	if(!empty($url)) $script .= "location.href='" . $url . "';";
	$script .= "</script>";

	echo $script;
	return;
}

function _confirm($str, $url="", $elseurl="") {

	header('Content-Type: text/html; charset=UTF-8');

	$script = "<script type=\"text/javascript\">";
	$script .= "
	if(confirm('".$str."')){
		location.href='".$url."';
		}else{
			location.href='".$elseurl."';
		}
		</script>";

		echo $script;
		return;
	}

	function paging($totalCnt,$pageSize,$pageNum,$fn=""){
		
		$pagenumber=PAGENUMBER;

		$total_page=ceil($totalCnt/$pageSize);
		$total_block=ceil($total_page/$pagenumber);
		
		if(($pageNum)% $pagenumber!=0){
			$block=ceil(($pageNum+1)/$pagenumber);
		}else{
			$block=ceil(($pageNum+1)/$pagenumber)-1;
		}
		$first_page=($block-1)*$pagenumber;
		$last_page=$block*$pagenumber;
		
		$prev=$first_page;
		$next=$last_page+1;
		$go_page=$first_page+1;
		
		if($fn==""){
			$fn="getList";
		}

		

		if($total_block<=$block)
			$last_page=$total_page; 

		$page_html="";
		if($totalCnt>0){
			$page_html.="<div class='paging'>";
			
			if($block>1){
				$page_html.="
				<a href='#' item-type='page-move' data-fn='".$fn."' data-page='".$prev."' class='btn-prev'>이전</a>
				";
			}else{
				$page_html.="
				<a  class='btn-prev'>이전</a>
				";      
			}
			
			for($go_page;$go_page<=$last_page;$go_page++){
				if($pageNum==$go_page)
					$page_html.="<a href='#' item-type='page-move' data-fn='".$fn."' data-page='".$go_page."'  class='active'>$go_page</a>";   
				else
					$page_html.="<a href='#' item-type='page-move' data-fn='".$fn."' data-page='".$go_page."'>$go_page</a>";
				
			}
			
			if($block<$total_block){
				$page_html.="
				<a href='#' item-type='page-move' data-fn='".$fn."' data-page='".$next."'  class='btn-next'>다음</a>
				";
			}else{
				$page_html.="
				<a  class='btn-next'>다음</a>
				";      
				
			}
			$page_html.="</div>";
		}
		
		return $page_html;
		
	}
	function escape($string){
		return html_escape($string);
	}
	function dojari($str){
		return sprintf('%02d',$str);
	}
	function formatDate($date,$prefix=null){
		if(strlen($date)==8){
			$date=substr($date,0,4).$prefix.substr($date,4,2).$prefix.substr($date,6,2);
		}else{
			$date=substr($date, 0,10);
			$date=str_replace("-", $prefix, $date);
		}

		return $date;
	}    
	function seg($no){
		$CI =& get_instance();
		return $CI->uri->segment($no);
	}
	function AES_Encode($plain_text){
		$key=CKEY;
		return urlencode(base64_encode(openssl_encrypt($plain_text, "aes-256-cbc", $key, true, str_repeat(chr(0), 16))));
	}

	function AES_Decode($base64_text){
		//echo "<br>".urldecode($base64_text);
		$key=CKEY;
		return openssl_decrypt(base64_decode(urldecode($base64_text)), "aes-256-cbc", $key, true, str_repeat(chr(0), 16));
	}
	function str_empty($str){
		//echo isset($str);
		return $str;
	}
	function getTag($str,$id){

		$str= str_replace("\n","",$str);
		// PHP의 정규표현식은 구분자(delimiters)로 시작해서 구분자로 끝을 내야한다.
		// 구분자는 보통 슬래쉬(/)를 사용하지만 꼭 그래야 하는 것은 아니고 해쉬(#)와 같이 알파벳과 백슬래쉬 그리고 공백이 아닌 문자를 사용하면 된다.
		$pattern = '#<div id="'.$id.'">(.*?)</div>#';

		preg_match($pattern, $str, $matches);
		return $matches[1];

	}