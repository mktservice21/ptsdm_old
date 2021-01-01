
<?PHP

    $now=date("m/d/Y/h:i:s");
    $now=date("Ymd");
    ?>

    <style type="text/css">

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
        .btn_sig{
            font-size: 16px;
            background: linear-gradient(#ffbc00 5%, #ffdd7f 100%);
            border: 1px solid #e5a900;
            color: #4E4D4B;
            font-weight: bold;
            cursor: pointer;
            width: 100px;
            border-radius: 5px;
            padding: 5px 0;
            outline: none;
            margin-top: 5px;
            margin-left: 1%;
            margin-bottom: 1%;
        }
        .btn_sig:hover{
            background: linear-gradient(#ffdd7f 5%, #ffbc00 100%);
        }
    </style>

<div>
    <div id="content"  class="main">

        <div id="tools"></div>
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
?><script src="tanda_tangan_base64/src/jSignature_mobile.js"></script><?php
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

        $('<input type="button" value="Save" class="btn btn-success">').bind('click', function(){
                Tampilkan("image");
        }).appendTo($tools)

        $('<input type="button" value="Reset" class="btn btn-default">').bind('click', function(e){
                $sigdiv.jSignature('reset')
        }).appendTo($tools)

        $('<div hidden><textarea style="width:100%;height:7em;" name="txtgambar" id="txtgambar"></textarea></div>').appendTo($tools)

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
            

            var ekar =document.getElementById('e_idkaryawan').value;
            var ediv =document.getElementById('cb_divisi').value;
            var elvlpos =document.getElementById('e_lvl').value;
            var eatasan1 =document.getElementById('e_atasan').value;
            var eatasan2 =document.getElementById('e_atasan2').value;
            var eatasan3 =document.getElementById('e_atasan3').value;
            var etotsem =document.getElementById('e_totalsemua').value;
            if (etotsem === "") etotsem=0;

            elvlpos=elvlpos.trim();

            var rlevel = elvlpos.substring(0, 2);

            if (ekar==""){
                alert("yang membuat masih kosong....");
                return 0;
            }

            //if (ediv==""){
            //    alert("divisi masih kosong....");
            //    return 0;
            //}

            if (ediv != "OTC"){
                
                if (rlevel=="FF") {
                    if (elvlpos=="FF1") {
                        if (eatasan1=="" && eatasan2=="" && eatasan3==""){
                            alert("Atasan masih kosong....");
                            return 0;
                        }else{
                            
                            if (eatasan3==""){
                                alert("SM masih kosong....");
                                return 0;    
                            }
                            
                        }
                    }

                    if (elvlpos=="FF1" || elvlpos=="FF2") {
                        if (eatasan2=="" && eatasan3==""){
                            alert("Atasan masih kosong....");
                            return 0;
                        }
                    }

                    if (elvlpos=="FF1" || elvlpos=="FF2" || elvlpos=="FF3") {
                        if (eatasan3==""){
                            alert("SM masih kosong....");
                            return 0;
                        }
                    }

                }
                
            }
            
            if (parseInt(etotsem)==0) {
                alert("Total Rupiah Masih Kosong....");
                return 0;
            }
    
            
            //simpan data ke DB
            var cmt = confirm('pastikan tanda tangan terisi....!!! jika sudah terisi klik OK');
            if (cmt == false) {
                return false;
            }
            
    
            var uttd = data;//gambarnya
            
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            
            
            document.getElementById("demo-form2").action = "module/mod_br_entrybrcash/aksi_entrybrcash.php?module="+module+"&act=input"+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            
        });

})
</script>

