<?php require('db.php'); ?> <!DOCTYPE html><html lang=en><head><base href=/codegolf/ ><meta charset=utf-8><meta http-equiv=X-UA-Compatible content="IE=edge"><meta name=viewport content="width=device-width,initial-scale=1"><link rel=icon href=favicon.png><title>codegolf</title><script src=js/jquery-2.0.0.min.js></script><script src=js/codemirror/lib/codemirror.js></script><script src=js/codemirror/mode/javascript/javascript.js></script><script src=js/linkify/linkify.min.js></script><script src=js/linkify/linkify-jquery.min.js></script><script src=js.php></script><script src=js/cropit/dist/jquery.cropit.js></script><script data-cfasync=false>mobile=$(window).width()<$(window).height()</script> <?php
        $params = explode('/',  $_SERVER['REQUEST_URI']);
        if(sizeof($params)>3){
          for($i=3;$i--;)array_shift($params);
        }
        if ($params[0] === 'a') {
          ?> <meta property=og:image content="https://<?=$baseDomain?>/thumbs/<?=$params[1]?>.png"><meta property=og:title content="demo #<?=$params[1]?>"> <?php
        } else {
          ?> <meta property=og:image content="https://<?=$baseDomain?>/codegolf_logo.png"><meta property=og:title content="CODEGOLF.TK, where the demoscene lives on!"> <?php
        }
      ?> <link href=css/app.7a1b649a.css rel=preload as=style><link href=js/app.894156aa.js rel=preload as=script><link href=js/chunk-vendors.84d7af2a.js rel=preload as=script><link href=css/app.7a1b649a.css rel=stylesheet></head><body><noscript><strong>We're sorry but codegolf doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id=app></div><script src=js/chunk-vendors.84d7af2a.js></script><script src=js/app.894156aa.js></script><script>console.log('<?php echo json_encode($params);?>')</script></body></html>