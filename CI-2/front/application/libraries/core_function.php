<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Core_function {
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
			$fn="page_go";
		}
	
		
	
		if($total_block<=$block)
			$last_page=$total_page;	
	
		$page_html="";
		if($totalCnt>0){
			$page_html.="<div class='paging'>";
			
			if($block>1){
				$page_html.="
					 <span class='prev'>	
					 <a href='javascript:".$fn."(1);'><img src='/images/btn_page_prev02.gif' alt='처음' /></a><a href=javascript:".$fn."($prev);> <img src='/images/btn_page_prev01.gif' alt='이전' /> </a>
					 </span>
				";
			}else{
				$page_html.="
					 <span class='prev'>	
					 <a href='javascript:".$fn."(1);'><img src='/images/btn_page_prev02.gif' alt='처음' /></a><a href='#'><img src='/images/btn_page_prev01.gif' alt='이전' /></a>
					 </span>
				";		
			}
			
			for($go_page;$go_page<=$last_page;$go_page++){
				if($pageNum==$go_page)
					$page_html.="<a href=javascript:".$fn."($go_page);  class='on'>$go_page</a>";	
				else
					$page_html.="<a href=javascript:".$fn."($go_page);>$go_page</a>";
				
			}
			
			if($block<$total_block){
				$page_html.="
					 <span class='next'>
					 <a href=javascript:".$fn."($next);> <img src=/images/btn_page_next01.gif alt='다음' /> </a><a href='javascript:".$fn."($total_page);'> <img src='/images/btn_page_next02.gif' alt='마지막' /> </a>
					 </span>
					";
			}else{
				$page_html.="
					 <span class='next'>
					 <a href='#'><img src='/images/btn_page_next01.gif' alt='다음' /></a><a href='javascript:".$fn."($total_page);'> <img src='/images/btn_page_next02.gif' alt='마지막' /> </a>
					 </span>
					";		
				
			}
			$page_html.="</div>";
		}
		
		return $page_html;
		
	}
}
?>