<?php
/**
* 
*/
class humble_box_bundle
{
	private $title;
	private $globalUrl = "https://www.humblebundle.com/";
	private $errorMsg = "<p>Please get back to kokoro-ko.de for help.</p>";
	
	function __construct()
	{
		$this->title = "";
		$this->init();
	}

	private function init(){

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
		}else if($_GET["type"] == "old_url"){
			$this->printErrorMsg("This bundle is expired.");
			return false;
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

		if($this->type != "games"){
			$this->printErrorMsg("Wrong Type.");
			return false;
		}


		$inc = mb_convert_encoding(file_get_contents($url),'HTML-ENTITIES', "UTF-8");
	
		// $length = stripos($inc, '<div class="hr-main-container">');
		// $inc = substr_replace($inc, "", 0, $length);

		$length = stripos($inc, '</head>');
		$inc = substr_replace($inc, "", 0, $length);

		while (stripos($inc, '<script') > 0) {
			$length = stripos($inc, '</script>');
			$startpos = stripos($inc, '<script');
			$inc = substr_replace($inc, "", $startpos, $length);
		}
		

		// $start = stripos($inc, '<div class="hs-mailing-list-main main-content-row">');
		// $inc = substr_replace($inc, "", $start, strlen($inc));
		echo '<seed>' . $inc . '</div></seed>';
		return true;
	}

	private function buildSmartBox(){
		?>
			<div class="container-fluid">
				<img class="icon img-responsive pull-right" src="hb.ico"></img>
				<div id="header"><?php echo(strtoupper(str_replace("-", " ", $this->title))); ?><small><span id="ctCur"></span> / <span id="ctAll"></span></small></div>
				<div class="row">
					<div class="over">This Bundle is over!</div>
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

	if($(".countdown-widget").find("#header-purchase").length == 0){
		
	
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
				insert += '<div><div class="objImg" style="background-image:url('+picture+');"></div>';
				insert += '<div class="objContent"><small class="price">'+price+'+</small><h4>'+name+'</h4>';
				subtitle = "";
				if($(oEl).find(".subtitle").length > 0){
					subtitle = $(oEl).find(".subtitle").html();
				}else{
					subtitle = "<small><i>No Description.</i></small>";
				}
				insert += subtitle;
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
		$(".over").show();
	}
	
	
</script>