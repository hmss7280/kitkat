<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller{
	function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->_view('main/view_main');

	}
	public function main2(){
		
	}
	function getSns2(){
		
		$medias=new stdClass;
		

		$ch = curl_init();
		$url = 'https://app.taggbox.com/api/apiJson/12373?user_key=b650a26b99cfaa7b243b99882c806224&count=20'; /*URL*/
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		//curl_close($ch);
		//$json=json_decode($response,true);
		//$fbObj=$json['feeds'];
		
		$json=json_decode($response);
		$fbObj=$json->feeds;
		print_r(count($json->feeds));


		$x=0;
		foreach ($fbObj as $key => $data) {
			//$medias->obj=new stdClass;
			//$objs=array();
			$obj[$x]=new stdClass;
			//echo $data->postId;
			$obj[$x]->sns="facebook";
			$obj[$x]->datetime=date("Y-m-d h:i:s", $data->postCreatedAt);
			$obj[$x]->id=$data->postId;
			$obj[$x]->text=$data->postContent;
			$obj[$x]->images=$data->postContentPicture;
			$medias->obj=$obj;

			//array_push($medias,$obj);
			$x++;
		}
		
		$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=4008940128.829fc88.32c6eb28c6b8491eaf29901dc673df75&count=20'; /*URL*/
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		//curl_close($ch);
		$json=json_decode($response);
		$instaObj=$json->data;

		//print_r($instaObj);

		foreach ($instaObj as $key => $data) {
			
			$obj[$x]=new stdClass;
			//$objs=array();
			/*
			$objs['sns']="instagram";
			$objs['datetime']=date("Y-m-d h:i:s", $data['created_time']);//$data['created_time'];
			$objs['id']=$data['id'];
			$objs['text']=$data['caption']['text'];
			$objs['images']=$data['images']['standard_resolution']['url'];
			
			array_push($obj,$objs);
			*/
			$obj[$x]->sns="instagram";
			$obj[$x]->datetime=date("Y-m-d h:i:s", $data->created_time);
			$obj[$x]->id=$data->id;
			$obj[$x]->text=$data->caption->text;
			$obj[$x]->images=$data->images->standard_resolution->url;
			$medias->obj=$obj;

			//array_push($medias,$obj);
			$x++;

		}

		foreach ($medias->obj as $key => $data) {
			
			echo $data->sns."<br/>";	
			echo $data->datetime."<br/>";	
			echo $data->id."<br/>";	
			echo $data->text."<br/>";	
			echo $data->images."<br/>";	
			echo "<br/><br/>";
			//print_r($data[0]->id."d");

		}

		//print_r($medias);
		curl_close($ch);
		exit;


		foreach ($fbObj as $key => $data) {
			$objs=array();
			
			//echo str_replace("\n","<br/>",$data['postContent'])."<br/>";
			//echo date("Y-m-d h:i:s", $data['postCreatedAt']);
			//echo $data['postCreatedAt']."<br/><br/><br/><br/>";
			

			$objs['sns']="facebook";
			$objs['datetime']=date("Y-m-d h:i:s", $data['postCreatedAt']);//$data['postCreatedAt'];
			$objs['id']=$data['postId'];
			$objs['text']=$data['postContent'];
			$objs['images']=$data['postContentPicture'];

			/*
			$objs=new stdClass;	
			$objs->sns="facebook";
			$objs->datetime=$data['postCreatedAt'];
			$objs->id=$data['postId'];
			$objs->text=$data['postContent'];
			$objs->images=$data['postContentPicture'];
			array_push($obj,$objs);
			*/
			array_push($obj,$objs);
		}

		$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=4008940128.829fc88.32c6eb28c6b8491eaf29901dc673df75&count=20'; /*URL*/
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		//curl_close($ch);
		$json=json_decode($response,true);
		$instaObj=$json['data'];

		//print_r($instaObj);

		foreach ($instaObj as $key => $data) {
			$objs=array();
			$objs['sns']="instagram";
			$objs['datetime']=date("Y-m-d h:i:s", $data['created_time']);//$data['created_time'];
			$objs['id']=$data['id'];
			$objs['text']=$data['caption']['text'];
			$objs['images']=$data['images']['standard_resolution']['url'];
			
			array_push($obj,$objs);
		}

		$obj=$this->sortArrayByField($obj,'datetime');
		$obj=$this->arrayToObject($obj);
		//echo count($obj);
		//print_r($obj);

		foreach ($obj as $key => $data) {
			echo $data->id;
		}
	}	
	function getSns3(){

		$obj=array();
		$ch = curl_init();
		
		/*
		$url = 'https://app.taggbox.com/api/apiJson/12373?user_key=b650a26b99cfaa7b243b99882c806224&count=3'; 
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		//curl_close($ch);
		$json=json_decode($response,true);
		$fbObj=$json['feeds'];

		foreach ($fbObj as $key => $data) {
			$objs=array();	

			$objs['sns']="facebook";
			$objs['datetime']=date("Y-m-d h:i:s", $data['postCreatedAt']);//$data['postCreatedAt'];
			$objs['id']=$data['postId'];
			$objs['text']=$data['postContent'];
			$objs['images']=$data['postContentPicture'];

			array_push($obj,$objs);
		}
		*/

		$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=4008940128.829fc88.32c6eb28c6b8491eaf29901dc673df75&count=6'; /*URL*/
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		$json=json_decode($response,true);
		$instaObj=$json['data'];

		//print_r($instaObj);
		//exit;
		foreach ($instaObj as $key => $data) {
			$objs=array();
			$objs['sns']="instagram";
			$objs['datetime']=date("Y-m-d h:i:s", $data['created_time']);//$data['created_time'];
			$objs['id']=$data['id'];
			$objs['text']=str_replace("\n","<br/>",$data['caption']['text']);
			$objs['images']=$data['images']['standard_resolution']['url'];
			
			array_push($obj,$objs);
		}

		$obj=$this->sortArrayByField($obj,'datetime');
			
		//echo count($obj);	
		$result=array();
		$x=0;
		foreach ($obj as $key => $data) {
			//$result[$key]=$data;
			//echo $key;
			$result[$x]['sns']=$data['sns'];
			$result[$x]['datetime']=$data['datetime'];
			$result[$x]['id']=$data['id'];
			$result[$x]['text']=$data['text'];
			$result[$x]['images']=$data['images'];
			$x++;
		}
		echo json_encode($result);	
		exit;
		//echo ($obj);

	}
	function sortArrayByField($original, $field, $descending = true ){
		$sortArr = array();

		foreach ( $original as $key => $value ){
			$sortArr[ $key ] = $value[ $field ];
		}

		if ( $descending ){
			arsort( $sortArr );
		}else{
			asort( $sortArr );
		}

		$resultArr = array();
		foreach ( $sortArr as $key => $value ){
			$resultArr[ $key ] = $original[ $key ];
		}

		return $resultArr;
	}
}
