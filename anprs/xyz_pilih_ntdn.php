
    <div>
        <div id="content"  class="main">
            
            <div id='n_ttd_1'>
                <div id="signatureparent">
                    <div id="signature"></div>
                </div>

                    <div id="tools"></div>
                    <div><p hidden>Tampil :</p><div id="displayarea"></div></div>
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
    
    <!--
    <script src="tanda_tangan_base64/src/jSignature_mobile.js" defer></script>
    <script src="tanda_tangan_base64/src/plugins/jSignature.CompressorBase30.js"></script>
    <script src="tanda_tangan_base64/src/plugins/jSignature.CompressorSVG.js"></script>
    <script src="tanda_tangan_base64/src/plugins/jSignature.UndoButton.js"></script> 
    <script src="tanda_tangan_base64/src/plugins/signhere/jSignature.SignHere.js"></script> 
    -->
    
    <script src="../mysdm/tanda_tangan_baru/src/jSignature.js"></script>
    <script src="../mysdm/tanda_tangan_baru/src/plugins/jSignature.CompressorBase30.js"></script>
    <script src="../mysdm/tanda_tangan_baru/src/plugins/jSignature.CompressorSVG.js"></script>
    <script src="../mysdm/tanda_tangan_baru/src/plugins/jSignature.UndoButton.js"></script> 
    <!--<script src="tanda_tangan_baru/src/plugins/signhere/jSignature.SignHere.js"></script> -->
    
    <script>
    function ReloadTandaTangan(){
            // This is the part where jSignature is initialized.
            //var $sigdiv = $("#signature").jSignature({'UndoButton':true})
            var $sigdiv = $("#signature").jSignature({ 'UndoButton': true, 'width': 370, 'height': 400 })

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

            $('<input type="button" value="Approve" class="btn btn-warning">').bind('click', function(){
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
                //simpan data ke DB
                var cmt = confirm('pastikan tanda tangan terisi....!!! jika sudah terisi klik OK');
                if (cmt == false) {
                    return false;
                }            
                var chk_arr =  document.getElementsByName("chkbox_br[]");
                var chklength = chk_arr.length;             
                var allnobr="";
                for(k=0;k< chklength;k++)
                {
                    if (chk_arr[k].checked == true) {
                        allnobr =allnobr + "'"+chk_arr[k].value+"',";
                    }
                }
                if (allnobr.length > 0) {
                    var lastIndex = allnobr.lastIndexOf(",");
                    allnobr = "("+allnobr.substring(0, lastIndex)+")";
                }else{
                    alert("Tidak ada data yang diproses...!!!");
                    return false;
                }


                var ekaryawan=document.getElementById('e_idkaryawan').value;
                var eidgrplnk=document.getElementById('e_idgroup_lnk').value;
                var eketapv="approve";
                var uttd = data;//gambarnya

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                
                $.ajax({
                    type:"post",
                    url:"aksi_ansxyz.php?module="+module+"&idmenu="+idmenu+"&act=simpan_ttdallam",
                    data:"ket=approve"+"&uttd="+uttd+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&uketapv="+eketapv+"&uidgrplnk="+eidgrplnk,
                    success:function(data){
                        if (data=="ttdkosong") {
                            alert("Anda harus menambahkan tanda tangan...!!!");
                        }else{
                            location.reload();
                            alert(data);
                        }
                    }
                });

            });
    }

    $(document).ready(function() {
        ReloadTandaTangan();
    })

    </script>

    <style type="text/css">
        body {
            padding:10px;
        }
        #signatureparent {
            color:darkblue;
            color:#000;
            background-color:darkgrey;
            /*max-width:600px;*/
            padding:20px;
            width:415px;
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

        #kiri {
            width:450px;
            height:500px;
            float:left;
            border: 0px solid #000;
        }
        #kanan {
            padding-left:20px;
            width:49%;
            height:500px;
            overflow: scroll;
            border: 0px solid #000;
        }
        
        .btn{
            background:#2C97DF;
            color:white;
            border-top:0;
            border-left:0;
            border-right:0;
            border-bottom:3px solid #2A80B9;
            padding:8px 20px;
            text-decoration:none;
            font-family:sans-serif;
            font-size:10pt;
            margin-right: 10px;
            margin-top: 5px;
        }
        .btn:hover {
          background-color: lightgreen;
          cursor: pointer;
        }
        .btn-warning {
            background:#ffbc00;
        }
        .btn-default {
            background:#ffffff;
            color:#000;
            border: 1px solid #cccccc;
            border-bottom:3px solid #2A80B9;
        }
        .btn-success {
            background:#00cc99;
        }
        .btn-sm {
            padding:7px 12px;
            font-size:10pt;
        }
        
        #n_ttd_2 {
          position: absolute;
          bottom: 100px;
          left: 330px;
        }

    </style>

