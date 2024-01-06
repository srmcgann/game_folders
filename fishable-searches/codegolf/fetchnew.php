<?php
	require("functions.php");
	
	function drawNewApplet(){
		global $link,$appletURL;
		$code="c.width=1920 //clear
sd=6
for(i=0; i<sd; i++){
  p=Math.PI*2/sd*i+t, X=S(p)*200, Y=C(p)*200
  x.fillRect(c.width/2+X,c.height/2+Y,10,10)
}";
		$id=0;
		?>
		<div class="appletDiv" id="appletDiv<?php echo $id?>" style="margin-top:30px;">
			<iframe src="<?php echo $appletURL?>/blank_template.php" sandbox="allow-same-origin allow-scripts" class="appletIframe" id="iframe<?php echo $id?>"></iframe>
			<script>
				var h=$(".appletIframe").width()/1.777777777777777777777777777778;
				$(".appletIframe").css("height",h+"px");
				var h=Math.min($(".appletDiv").width()/38,24);
				$(".appletName").css("font-size",h/1.1+"px");
				var h=Math.min($(".appletDiv").width()/10.5,80);
				$(".appletAvatar").css("width",h+"px");
				var h=Math.min($(".appletDiv").width()/45,20);
				$(".userInfoTable").css("font-size",h+"px");
				if(!mobile){
					var h=$(".appletIframe").height();
					$(".code-input").css("height",h+"px");
					$(".appletCode").css("height",h/1.85+"px");
					$(".appletCode").css("max-height",h/1.85+"px");
				}
				$("#iframe<?php echo $id?>").load(function(){
					var i=$("#iframe<?php echo $id?>")[0].contentWindow;
					document.querySelector("#iframe<?php echo $id?>").loaded=1;
					i.postMessage("start:false:"+`<?php echo $code?>`, "<?php echo $appletURL?>");
					startStopApplets();
				});
				
				$('#toggle_fullscreen<?php echo $id?>').on('click', function(){
					i=$("#iframe<?php echo $id?>")[0];
					if (i.requestFullscreen) {
						i.requestFullscreen();
					} else if (i.webkitRequestFullscreen) {
						i.webkitRequestFullscreen();
					} else if (i.mozRequestFullScreen) {
						i.mozRequestFullScreen();
					} else if (i.msRequestFullscreen) {
						i.msRequestFullscreen();
					}
					i=$(iframe<?php echo $id?>)[0];
					if (i.requestFullscreen) {
						i.requestFullscreen();
					} else if (i.webkitRequestFullscreen) {
						i.webkitRequestFullscreen();
					} else if (i.mozRequestFullScreen) {
						i.mozRequestFullScreen();
					} else if (i.msRequestFullscreen) {
						i.msRequestFullscreen();
					}
				});
                                startStopApplets();
			</script>
			<div class="code-input"><span class="function-wrap">function u(t){</span><input id="webglCheckbox<?php echo $id?>" style="margin-left:5%;float:left;" type="checkbox"></input><span style="float:left;margin-left:5px;font-size:.7em;" id="webglCheckboxLabel<?php echo $id?>">webgl</span>
				<a href="javascript:deleteApplet(<?php echo $id?>)" id="deleteButton<?php echo $id?>" style="color:red;font-size:.8em;float:right;display:none;margin-top:-30px">Delete Applet</a>
				<textarea class="appletCode" id="textArea<?php echo $id?>" autocorrect="off" autocapitalize="off" spellcheck="false" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px';"><?php echo $code?></textarea><div class="function-wrap" id="count<?php echo $id?>"></div>
				<?php
				if(isset($_COOKIE['id']) && isset($_COOKIE['session'])){
					?>
					<button id="postRemix<?php echo $id?>" onclick="saveApplet(<?php echo $id?>,0,0)" class="postButton">Post Demo</button>
					<?php
				}else{
					?>
					<span id="postRemix<?php echo $id?>" style="display:none;font-size:.8em;margin-left:20px;color:red;background:#222a;">Please login or register to post your work.</span>
					<?php
				}
				?>
				<script>
					$('#webglCheckbox<?php echo $id?>').change(function() {
						var webgl=this.checked;
						var text=editor<?php echo $id?>.getValue();
						document.querySelector("#iframe<?php echo $id?>").contentWindow.postMessage("start:"+webgl+":"+text, "<?php echo $appletURL?>");
						$('#webglCheckboxLabel<?php echo $id?>').val(this.checked);
					});
					var editor<?php echo $id?> = CodeMirror.fromTextArea($("#textArea<?php echo $id?>")[0],{theme:"blackboard",lineWrapping:true,autofocus:true});
					if(!($(window).width()<$(window).height())){
						var h=$(".appletIframe").height();
						$(".CodeMirror").height((h-65)+"px");
						$(".CodeMirror").css("margin-top","-25px");
					}else{
						var h=$(".appletIframe").height();
						$(".CodeMirror").height("145px");
						$(".CodeMirror").css("margin-top","-25px");						
					}
				</script>
			</div>
			<div class="clear"></div>
			<div class="toolbar">
				<div class="toolbarText">
					<span><a href="javascript:;" id="toggle_fullscreen<?php echo $id?>">Fullscreen</a></span>
				</div>
			</div>
		</div>
		<div class="clear"></clear>
		<div class="appletLegend">
			<code class="legendText">u(t) is called 60 times per second.
				t: Elapsed time in seconds.
				S: Shorthand for Math.sin.
				C: Shorthand for Math.cos.
				T: Shorthand for Math.tan.
				c: A 1920x1080 canvas.
				x: A context for that canvas.
				There are 3 applet categories based on number of characters: 140, 512, or 1024. The category is determined automatically.
			</code>
		</div>
		<script>
			function saveApplet<?php echo $id?>(){
				$.post('/codegolf/saveApplet.php',{ code:$("#textArea<?php echo $id?>").val(), formerUserID: 0, formerAppletID:0 },
				function(data) {
					if(data=="fail"){
						alert("Applet could not be saved!\n\nEither it is too long, or you are not logged in...")
					}else{
						location.href="/codegolf/";
					}
				});
				
			}
			var oldcode=editor<?php echo $id?>.getValue();
			editor<?php echo $id?>.on("keyup",function(){
				if(editor<?php echo $id?>.getValue()!=oldcode){
					$("#postRemix<?php echo $id?>").show();
					oldcode=editor<?php echo $id?>.getValue();
					var len=editor<?php echo $id?>.getValue().length;
					var text=editor<?php echo $id?>.getValue();
					if(len<1025)var max=1024;
					if(len<513)var max=512;
					if(len<141)var max=140;
					$("#count<?php echo $id?>").html("}//<span style='color:"+(len>1024?"red":"#888")+"'>"+len+"</span>/"+max);
					var webgl=$("#webglCheckbox<?php echo $id?>").is(":checked");
					document.querySelector("#iframe<?php echo $id?>").contentWindow.postMessage("start:"+webgl+":"+text, "<?php echo $appletURL?>");
				}
			});
			var len=editor<?php echo $id?>.getValue().length;
			if(len<1025)var max=1024;
			if(len<513)var max=512;
			if(len<141)var max=140;
			$("#count<?php echo $id?>").html("}//<span style='color:"+(len>512?"red":"#888")+"'>"+len+"</span>/"+max);
		</script>
		<?php	
	}

	drawNewApplet();
?>
