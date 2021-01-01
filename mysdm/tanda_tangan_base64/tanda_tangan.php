    <script language="javascript">
        document.onmousedown = disableclick;
        status = "Right Click Disabled";
        Function disableclick(e)
        {
            if(event.button == 2)
            {
                alert(status);
                return false; 
            }
        }
    </script>
    
    <script>
        function showlocation() {
            // One-shot position request.
            navigator.geolocation.getCurrentPosition(callback);
        }

        function callback(position) {
            var cLat = position.coords.latitude;
            var cLong = position.coords.longitude;

            var lat = document.getElementById("latitude");
            var long = document.getElementById("longitude");

            lat.value = cLat;
            long.value = cLong;
        }
        function cSimPan($refno, $prod, $qty, $clang, $clong, $modif, $ket, $cPrice, $cEmp, $cSP){
            var uc1=$($refno).val();
            var uc2=$($prod).val();
            var uc3=$($qty).val();
            var uc4=$($clang).val();
            var uc5=$($clong).val();
            var uc6=$($modif).val();
            var uc7=$($ket).val();
            var uc8=$($cEmp).val();

            if (uc1=="") {
                alert("RefNo/Outlet Kosong...");
                return (false);
            }

            if (uc2=="") {
                alert("ERROR");
                return (false);
            }
            if (uc4=="" & uc5=="") {
                alert("Lokasi masih Kosong, please wait...");
                return (false);
            }
            if (uc6=="") {
                alert("ERROR");
                return (false);
            }
            showlocation();
            simpanUnitKeData($refno, $prod, $qty, $clang, $clong, $modif, $ket, $cPrice, $cEmp, $cSP);
        }
        function simpanUnitKeData($refno, $prod, $qty, $clang, $clong, $modif, $ket, $price, $cEmp, cSP) {
            var urefno=$($refno).val();
            var uproduk=$($prod).val();
            var uqty=$($qty).val();
            var ulang=$($clang).val();
            var ulong=$($clong).val();
            var umodif=$($modif).val();
            var uket=$($ket).val();
            var uprice=$($price).val();
            var uemp=$($cEmp).val();       

            var uSp;
            if($(cSP).is(":checked")){
                uSp="1";
            }else{
                uSp="0";
            }

            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
            $.ajax({
                type:"post",
                url:"tanda_tangan_base64/save_call.php?module=save_harian",
                data:"urefno="+urefno+"&uproduk="+uproduk+"&uqty="+uqty+"&ulang="+ulang
                        +"&ulong="+ulong+"&umodif="+umodif+"&uket="+uket+"&uprice="+uprice+"&uemp="+uemp+"&usp="+uSp,
                success:function(){
                    alert("success");
                    $("#loading").html("");
                }
            });

        }

    </script>
    <style>
        body{
            padding:10px;
        }
    </style>
    
    <script type='text/javascript' src='js/jquery.min.js'></script>
    <script type='text/javascript' src='js/jquery-ui.min.js'></script>

           
</head>

<body  onfocus="javascript:showlocation()" onload="javascript:showlocation()" oncontextmenu="return false">
    
    <?PHP
    
        $actual_link = 'http://'.$_SERVER['HTTP_HOST']."/ptsdm";
        echo "<a class='btn' href='$actual_link/media.php?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[act]'></a><br />";
        include "config/koneksimysqli.php";
        //include "../config/fungsi_divisi.php";
        //include "../config/fungsi_sql.php";    
        
        $now=date("m/d/Y/h:i:s");
        $now=date("Ymd");
        ?>
        <div hidden>
            <form name="flokasi" id="flokasi">
                Location Latitude : <input type="text" name="latitude" id="latitude" size="10" readonly><br/>
                Location Longitude : <input type="text" name="longitude" id="longitude" size="10" readonly>
            </form>
        </div>
                    
                    
    <script src="libs/modernizr.js"></script>
    <style type="text/css">
	div {
            margin-top:1em;
            margin-bottom:1em;
	}
	input {
            padding: .5em;
            margin: .5em;
	}
	select {
            padding: .5em;
            margin: .5em;
	}
	
	#signatureparent {
            color:darkblue;
            color:#000;
            background-color:darkgrey;
            /*max-width:600px;*/
            padding:20px;
	}
	
	/*This is the div within which the signature canvas is fitted*/
	#signature {
            border: 2px dotted black;
            background-color:lightgrey;
	}

	/* Drawing the 'gripper' for touch-enabled devices */ 
	html.touch #content {
            float:left;
            width:92%;
	}
	html.touch #scrollgrabber {
            float:right;
            width:4%;
            margin-right:2%;
            background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAFCAAAAACh79lDAAAAAXNSR0IArs4c6QAAABJJREFUCB1jmMmQxjCT4T/DfwAPLgOXlrt3IwAAAABJRU5ErkJggg==)
	}
	html.borderradius #scrollgrabber {
            border-radius: 1em;
	}
        #content .btn{
            font-size: 16px;
            background: linear-gradient(#ffbc00 5%, #ffdd7f 100%);
            border: 1px solid #e5a900;
            color: #4E4D4B;
            font-weight: bold;
            cursor: pointer;
            width: 300px;
            border-radius: 5px;
            padding: 10px 0;
            outline: none;
            margin-top: 20px;
            margin-left: 15%;
        }
        #content .btn:hover{
            background: linear-gradient(#ffdd7f 5%, #ffbc00 100%);
        }
    </style>
    
    <div>
        <div id="content"  class="main">
            
            <form name="tdata" id="tdata" hidden>
                <input type="text" name="e_nobr" id="e_nobr" value="<?PHP echo "$_GET[nobr]"; ?> " />
                <input type="text" name="nkode" id="nkode" value="<?PHP //echo "$_GET[kode]"; ?> " />
                <input type="text" name="nket" id="nket" value="<?PHP //echo "$_GET[ket]"; ?> " hidden/><br/>
                <input type="text" name="ncardid" id="ncardid" value="<?PHP //echo "$_GET[cardid]"; ?> " hidden/><br/>
            </form>
            
            
            <?PHP $actual_link = 'http://'.$_SERVER['HTTP_HOST']."/ptsdm"; ?>
            <div id="tools"></div><!--<a class="btn" href="<?PHP //echo "$actual_link/media.php?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[act]"; ?>">BACK</a>-->
            <div><p hidden>Tampil :</p><div id="displayarea"></div></div>
            
            
            <div id="signatureparent">
                <div id="signature"></div>
            </div>
            
            
            
        </div>
        <div id="scrollgrabber"></div>
    </div>
    <script>
    /*  @preserve
    jQuery pub/sub plugin by Peter Higgins (dante@dojotoolkit.org)
    Loosely based on Dojo publish/subscribe API, limited in scope. Rewritten blindly.
    Original is (c) Dojo Foundation 2004-2010. Released under either AFL or new BSD, see:
    http://dojofoundation.org/license for more information.
    */
    (function($) {
            var topics = {};
            $.publish = function(topic, args) {
                if (topics[topic]) {
                    var currentTopic = topics[topic],
                    args = args || {};

                    for (var i = 0, j = currentTopic.length; i < j; i++) {
                        currentTopic[i].call($, args);
                    }
                }
            };
            $.subscribe = function(topic, callback) {
                if (!topics[topic]) {
                    topics[topic] = [];
                }
                topics[topic].push(callback);
                return {
                    "topic": topic,
                    "callback": callback
                };
            };
            $.unsubscribe = function(handle) {
                var topic = handle.topic;
                if (topics[topic]) {
                    var currentTopic = topics[topic];

                    for (var i = 0, j = currentTopic.length; i < j; i++) {
                        if (currentTopic[i] === handle.callback) {
                            currentTopic.splice(i, 1);
                        }
                    }
                }
            };
    })(jQuery);
    </script>
    <?php
    include('tanda_tangan_base64/src/mobile.php');
    if(mobile_device_detect(true,true,true,true,false,false)) {
    ?><script src="tanda_tangan_base64/src/jSignature_mobile.js"></script><?php    
    }else{
    ?><script src="tanda_tangan_base64/src/jSignature.js"></script><?php
    }
    ?>
    <script src="tanda_tangan_base64/src/plugins/jSignature.CompressorBase30.js"></script>
    <script src="tanda_tangan_base64/src/plugins/jSignature.CompressorSVG.js"></script>
    <script src="tanda_tangan_base64/src/plugins/jSignature.UndoButton.js"></script> 
    <script src="tanda_tangan_base64/src/plugins/signhere/jSignature.SignHere.js"></script> 
    <script>
    $(document).ready(function() {

            // This is the part where jSignature is initialized.
            var $sigdiv = $("#signature").jSignature({'UndoButton':true})

            // All the code below is just code driving the demo. 
            , $tools = $('#tools')
            , $extraarea = $('#displayarea')
            , pubsubprefix = 'jSignature.demo.'
            /*
            var export_plugins = $sigdiv.jSignature('listPlugins','export')
            , chops = ['<span><b></b></span><select hidden>','<option value="">(select export format)</option>']
            , name
            for(var i in export_plugins){
                    if (export_plugins.hasOwnProperty(i)){
                            name = export_plugins[i]
                            chops.push('<option value="' + name + '">' + name + '</option>')
                            
                    }
            }
            chops.push('</select>')
            */
            
            var export_plugins = $sigdiv.jSignature('listPlugins','export')
            , chops = ['<span><b></b></span>','']
            , name
            for(var i in export_plugins){
                    if (export_plugins.hasOwnProperty(i)){
                            name = export_plugins[i]
                            chops.push('')
                            
                    }
            }
            chops.push('')
            
            $(chops.join('')).bind('change', function(e){
                    if (e.target.value !== ''){
                            var data = $sigdiv.jSignature('getData', e.target.value)
                            $.publish(pubsubprefix + 'formatchanged')
                            if (typeof data === 'string'){
                                    $('textarea', $tools).val(data)
                            } else if($.isArray(data) && data.length === 2){
                                    $('textarea', $tools).val(data.join(','))
                                    $.publish(pubsubprefix + data[0], data);
                            } else {
                                    try {
                                            $('textarea', $tools).val(JSON.stringify(data))
                                    } catch (ex) {
                                            $('textarea', $tools).val('Not sure how to stringify this, likely binary, format.')
                                    }
                            }
                    }
            }).appendTo($tools)
            
            $('<input type="button" value="Simpan" class="btnX">').bind('click', function(){
                    Tampilkan("image");
            }).appendTo($tools)
            
            $('<input type="button" value="Reset" class="btnX">').bind('click', function(e){
                    $sigdiv.jSignature('reset')
            }).appendTo($tools)

            $('<div hidden><textarea style="width:100%;height:7em;"></textarea></div>').appendTo($tools)

            $.subscribe(pubsubprefix + 'formatchanged', function(){
                    $extraarea.html('')
            })           
            
            function Tampilkan(e){
                if (e !== ''){                            
                    var data = $sigdiv.jSignature('getData', e)
                    $.publish(pubsubprefix + 'formatchanged')
                    if (typeof data === 'string'){
                        $('textarea', $tools).val(data)
                    } else if($.isArray(data) && data.length === 2){
                            $('textarea', $tools).val(data.join(','))
                            $.publish(pubsubprefix + data[0], data);
                    } else {
                            try {
                                    $('textarea', $tools).val(JSON.stringify(data))
                            } catch (ex) {
                                    $('textarea', $tools).val('Not sure how to stringify this, likely binary, format.')
                            }
                    }
                }
            }
            $.subscribe(pubsubprefix + 'image/png;base64', function(data) {
                
                    var uttd = data;
                    var unobr = document.getElementById("e_nobr").value;
                    showlocation();
                    var uClat = document.getElementById("latitude").value;
                    var uClong = document.getElementById("longitude").value;
                    
                        $.ajax({
                            type:"post",
                            url:"tanda_tangan_base64/simpan_ttd.php?module=simpan_ttd",
                            data:"unobr="+unobr+"&uttd="+uttd+"&ulat="+uClat+"&ulong="+uClong,
                            success:function(data){
                                alert(data);
                            }
                        });

                        var i = new Image()
                        i.src = 'data:' + data[0] + ',' + data[1]
                        $('<span hidden><b>the signature.</b></span>').appendTo($extraarea)
                        $(i).appendTo($extraarea)
                        /*alert(data);*/
                    
            });
            
    })
    </script>

</body>
