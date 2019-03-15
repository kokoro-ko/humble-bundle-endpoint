<?php
require_once 'CURLManager.php';

/**
* 
*/
class humble_box_bundle
{
	private $title;
	private $globalUrl = "https://www.humblebundle.com/";
	private $errorMsg = "<p>Please get back to kokoro-ko.de for help.</p>";
	private $supportedType = ["games","books","software","comics","store"];
	private $cm;
	private $neverCacheAgain = false;
	private $cachetime = 604800;
	
	function __construct()
	{
		$this->title = "";
		$this->init();
	}

	private function init(){

		$this->cm = new CURLManager();
		$this->printHeader();
		if($this->filterData()){
			$this->buildSmartBox();
		}
		$this->printFooter();
	}

	private function printHeader(){
		?>
			<html>
			<head>
				<title><?php echo $this->title; ?></title>
				<script
				  src="https://code.jquery.com/jquery-3.2.1.js"
				  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
				  crossorigin="anonymous"></script>
				<link href="https://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet">
				<link href="style.css" rel="stylesheet">
			</head>
			<body>
		<?php
	}

	private function filterData(){
		if(!isset($_GET["type"])){
			$this->printErrorMsg("Type-Parameter missing.");
			return false;
		}else if(!isset($_GET["urlCode"])){
			$this->printErrorMsg("urlCode-Parameter missing.");
			return false;
		}else if($_GET["isOld"] == "old_url"){
			$neverCacheAgain = false;
		}else if($_GET["type"] == "invalid_type"){
			$this->printErrorMsg("This type is not supported.");
			return false;
		}

		$this->title = $_GET["urlCode"];
		$this->type = $_GET["type"];

		$url = $this->globalUrl.$this->type."/".$this->title;
		if($this->get_http_response_code($url) != "200"){
			$this->printErrorMsg("Wrong urlCode. ");
			return false;
		}

		if (!in_array($this->type,$this->supportedType) && $_GET["isOld"] != "old_url")  {
			$this->printErrorMsg("Wrong Type.");
			return false;
		}
		
		if($this->type == "store"){
			$this->cachetime = 4800;
		}else{
			$this->cachetime = 604800;
		}
		$inc = mb_convert_encoding($this->cm->getContents($url,$this->neverCacheAgain,false,$this->cachetime),'HTML-ENTITIES', "UTF-8");
		if($this->type == "store"){
			$storeObj = array(0 => null);
			if(stripos($inc, 'freebie-landing-page') > 0){
				$sub = $this->getBetween($inc, '"productInfoString":', '"carousel_content"');
				if(isset($sub[0])){
					$myObj = $sub[0];
					$myObj = rtrim(ltrim($myObj));
					$storeObj = substr($myObj, 0, strlen($myObj)-1 ) . "}";
				}
			}else{	
				$sub = $this->getBetween($inc, '"products_json":', '"viewing_wishlist"');
				if(isset($sub[0])){
					$myObj = $sub[0];
					$myObj = rtrim(ltrim($myObj));
					$storeObj = substr($myObj, 0, strlen($myObj)-1 );
				}
			}
			echo "<script>var storeObj = ".$storeObj.";</script>";
		}else{
			$length = stripos($inc, '</head>');
			$inc = substr_replace($inc, "", 0, $length);

			while (stripos($inc, '<script') > 0) {
				$length = stripos($inc, '</script>');
				$startpos = stripos($inc, '<script');
				$inc = substr_replace($inc, "", $startpos, $length);
			}
			$inc.="</div>";
			echo '<seed>' . $inc . '</seed>';
		}		
		return true;
	}

	function getBetween($content, $start, $end) {
		$n = explode($start, $content);
		$result = Array();
		foreach ($n as $val) {
			$pos = strpos($val, $end);
			if ($pos !== false) {
				$result[] = substr($val, 0, $pos);
			}
		}
		return $result;
	}

	private function buildSmartBox(){
		?>
			<div class="container-fluid">
				<img class="icon img-responsive pull-right" src="hb.ico"></img>
				<div id="header"><a href="https://www.humblebundle.com/<?php echo $this->type; ?>/<?php echo $this->title; ?>/" target="_blank">[<?php echo strtoupper($this->type); ?>] <?php echo(strtoupper(str_replace("-", " ", $this->title))); ?></a><small><span id="ctCur"></span> / <span id="ctAll"></span></small></div>
				<div class="row">
					
				</div>
			</div>

		<?php

	}

	private function get_http_response_code($url) {
		$headers = get_headers($url);
		return substr($headers[0], 9, 3);
	}

	private function printFooter(){
		?>
		</body>
			<footer>
			</footer>
		</html>
		<?php
	}
	
	private function printErrorMsg($msg){
		echo "<div class='error_msg'><h1>".$msg."</h1>".$this->errorMsg."</div>";
	}
}

$box = new humble_box_bundle();
?>



<script type="text/javascript">

	if($(".over").length == 0){
		if(typeof storeObj == 'undefined'){
			//normal bundles
			$(".hr-main-container").remove();
			var insert = "";
			var moneyRegExp = /\$(.*?)\ /;
			$(".dd-game-row").each(function(i, el){
				var header = $(el).find(".dd-header-headline").text();
				var priceExp = moneyRegExp.exec(header.replace(/  /g,""));
				var price = '';
				if(priceExp === null){ price = '$1.00'; }else{price = priceExp[0]; }
				$(el).find(".game-boxes").each(function(oI, oEl){
					var name = $(oEl).find(".dd-image-box-caption.dd-image-box-text").text();
					var picture = $(oEl).find(".dd-image-box-figure-img").data("retina-src");
					name = name.replace(/  /g,"").replace(/(?:\r\n|\u00A0|\r|\n)/g,"");
					insert += '<div class="gObj"><div class="objImg" style="background-image:url('+picture+');"></div>';
					insert += '<div class="objContent"><small class="price">'+price+'+</small><h4>'+name+'</h4>';
					insert += '</div></div>';
				});
			})

			$(".row").html(insert);

			function fadeDiv(elem) {
				elem.delay().fadeIn().delay(1500).fadeOut(500, function () {
					if (elem.next().length > 0) {
						$("#ctCur").text(elem.next().index()+1);
						fadeDiv(elem.next());
					} else {
						$("#ctCur").text(1);
						fadeDiv(elem.siblings(':first'));
					}
				});
			}

			function initFade(){
				$('.container-fluid > .row > div').hide();
				fadeDiv($('.container-fluid > .row > div:first'));
				$("#ctAll").text($('.container-fluid > .row > div').length);
				$("#ctCur").text(1);
			}

			$(document).ready(function(){
				initFade();
			});
		}else{
			//store item
			var mObj = storeObj;
			if(typeof storeObj == "array"){
				mObj = storeObj[0];
			}
			var name = mObj.human_name;
			var picture = mObj.large_capsule;
			var fPrice = mObj.full_price[0]+" "+mObj.full_price[1];
			var cPrice = mObj.current_price[0]+" "+mObj.current_price[1];
			var description = "";
			if(mObj.platforms != null){
				description += "Platforms: "
				mObj.platforms.forEach(function(el, i){
					description += "["+el+"] ";
				})
			}
			
			var insert = '<div class="gObj"><div class="objImg" style="background-image:url('+picture+');"></div>';
				insert += '<div class="objContent"><small class="price">'+cPrice+'</small>';
				insert += (fPrice != cPrice) ? '<small class="price price-strike">'+fPrice+'</small>' : '';
				insert += '<h4>'+name+'</h4>';
				insert += '<div class="objDescription">'+description.replace("<br>")+'</div>';
				insert += '</div></div>';

			$(".row").html(insert);
			$("#header > small").hide();
		}
	}else{
		$(".row").html("<div class='over'><img src='"+$("img.promo-logo").attr("src")+"'>"+$(".over-information").html()+"</div>");
		$("#header > small").hide();
		
	}
	
	
</script>