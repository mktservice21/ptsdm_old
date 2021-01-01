
<?PHP
    include "config/koneksimysqli.php";  

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
        
        
        <div><p hidden>Tampil :</p><div id="displayarea"></div></div>


        <div id="signatureparent">
            <div id="signature"></div>
        </div>
        <div id="tools" style="margin-top:10px;"></div>


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
//include('../../tanda_tangan_base64/src/mobile.php');
//if(mobile_device_detect(true,true,true,true,false,false)) {
?><!--<script src="tanda_tangan_base64/src/jSignature_mobile.js"></script>--><?php    
//}else{
?><!--<script src="tanda_tangan_base64/src/jSignature_mobile.js"></script>--><?php
//}
?>

<!--
<script src="tanda_tangan_base64/src/jSignature_mobile.js" defer></script>

<script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/tanda_tangan_base64/src/plugins/jSignature.CompressorBase30.js"></script>
<script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/tanda_tangan_base64/src/plugins/jSignature.CompressorSVG.js"></script>
<script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/tanda_tangan_base64/src/plugins/jSignature.UndoButton.js"></script> 
<script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/tanda_tangan_base64/src/plugins/signhere/jSignature.SignHere.js"></script>
-->

    <script src="tanda_tangan_baru/src/jSignature.js"></script>
    <script src="tanda_tangan_baru/src/plugins/jSignature.CompressorBase30.js"></script>
    <script src="tanda_tangan_baru/src/plugins/jSignature.CompressorSVG.js"></script>
    <script src="tanda_tangan_baru/src/plugins/jSignature.UndoButton.js"></script> 
    <!--<script src="tanda_tangan_baru/src/plugins/signhere/jSignature.SignHere.js"></script> -->
    
<script>
function ReloadTandaTangan(){
        // This is the part where jSignature is initialized.
        //var $sigdiv = $("#signature").jSignature({'UndoButton':true})
        var $sigdiv = $("#signature").jSignature({ 'UndoButton': true, 'width': 355, 'height': 400 })

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
        
        $('<input type="hidden" value="Reject" class="btn btn-danger">').bind('click', function(e){
                disp_confirm('Hapus / Reject...?', 'reject')
        }).appendTo($tools)
        //<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
        $('<input type="button" value="Back" class="btn btn-dark">').bind('click', function(e){
                self.history.back()
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
            
            var ecab =document.getElementById('cb_cabangpil').value;
            var ebuat =document.getElementById('e_idkaryawan').value;
            var ejbtid =document.getElementById('e_jabatanid').value;
            var edivi =document.getElementById('cb_divisi').value;
            var ekode =document.getElementById('cb_kode').value;
            var edokterid =document.getElementById('cb_iddokter').value;
            var ejumlah =document.getElementById('e_jmlusulan').value;


            if (ecab==""){
                alert("cabang masih kosong....");
                return 0;
            }
            if (ebuat==""){
                alert("yang membuat masih kosong....");
                return 0;
            }
            if (edokterid==""){
                //alert("dokter masih kosong....");
                //return 0;
            }
            if (edivi==""){
                alert("divisi masih kosong....");
                return 0;
            }
            if (ekode==""){
                //alert("kode masih kosong....");
                //return 0;
            }
            if (ejumlah==""){
                //alert("jumlah masih kosong....");
                //document.getElementById('e_jmlusulan').focus();
                //return 0;
            }
            
            
            if (edivi=="HO"){

            }else{            

                if (ejbtid=="20"){
                    var cbatasan4 =document.getElementById('cb_gsm').value;

                    if (cbatasan4=="") {
                        alert("atasan masih kosong....");
                        return 0;
                    }

                }else{

                    var cbatasan1 =document.getElementById('cb_apvspv').value;
                    var cbatasan2 =document.getElementById('cb_apvdm').value;
                    var cbatasan3 =document.getElementById('cb_sm').value;
                    var cbatasan4 =document.getElementById('cb_gsm').value;

                    if (cbatasan1=="" && cbatasan2=="" && cbatasan3=="" && cbatasan4=="") {
                        alert("atasan masih kosong....");
                        return 0;
                    }

                    if (cbatasan3=="") {
                        alert("SM masih kosong....");
                        return 0;
                    }

                    if (cbatasan4=="") {
                        alert("GSM masih kosong....");
                        return 0;
                    }
                }
            
            }
        
        
            var nchktiket = document.getElementById("chk_tiket");
            var nchktglpulang = document.getElementById("chk_pulang");
            var nchkhotel = document.getElementById("chk_hotel");
            var nchksewa = document.getElementById("chk_sewa");
            if (nchktiket.checked==false && nchkhotel.checked==false && nchksewa.checked==false) {
                alert("Tiket, Hotel atau Sewa belum dipilih");
                return false;
            }
            
            if (nchktiket.checked==true) {
                var etujdari =document.getElementById('e_tjdari').value;
                var etujke =document.getElementById('e_tjke').value;
                var etglpergi=document.getElementById('e_tglpergi').value;
                var ejampergi=document.getElementById('e_jampergi').value;
                
                if (etujdari=="" && etujke=="") {
                    alert("Tujuan belum diisi"); document.getElementById('e_tjdari').focus(); return false;
                }
                
                if (etujdari=="") {
                    alert("Tujuan Dari Kota/Daerah, belum diisi"); document.getElementById('e_tjdari').focus(); return false;
                }
                
                if (etujke=="") {
                    alert("Tujuan Ke Kota/Daerah, belum diisi"); document.getElementById('e_tjke').focus(); return false;
                }
                
                
                if (etglpergi=="") {
                    alert("Tanggal Pergi Masih Kosong"); document.getElementById('e_tglpergi').focus(); return false;
                }

                if (ejampergi=="") {
                    alert("Jam Pergi Masih Kosong"); document.getElementById('e_jampergi').focus(); return false;
                }
                
                
                if (nchktglpulang.checked==true) {
                    var etglpulang=document.getElementById('e_tglpulang').value;
                    var ejampulang=document.getElementById('e_jampulang').value;
                    
                    if (etglpergi=="" && etglpulang=="") {
                        alert("Tanggal Tiket Pulang dan Pergi masih kosong"); document.getElementById('e_tglpergi').focus(); return false;
                    }
                    
                    if (etglpulang=="") {
                        alert("Tanggal Pulang Masih Kosong"); document.getElementById('e_tglpulang').focus(); return false;
                    }
                    
                    if (ejampulang=="") {
                        alert("Jam Pulang Masih Kosong"); document.getElementById('e_jampulang').focus(); return false;
                    }
                    
                    
                }
                
                
                
            }
            
            if (nchkhotel.checked==true) {
                var enginapdi=document.getElementById('e_nginapdi').value;
                var etglmulai=document.getElementById('e_tglmulai').value;
                var etglsampai=document.getElementById('e_tglsampai').value;
                
                if (enginapdi=="" && etglmulai=="" && etglsampai=="") {
                    alert("Data Hotel belum diisi"); document.getElementById('enginapdi').focus(); return false;
                }
                
                if (enginapdi=="") {
                    alert("Menginap di Hotel (Kota/Daerah) belum diisi"); document.getElementById('enginapdi').focus(); return false;
                }
                
                if (etglmulai=="") {
                    alert("Tanggal Mulai Menginap di Hotel belum diisi"); document.getElementById('e_tglmulai').focus(); return false;
                }
                
                if (etglsampai=="") {
                    alert("Tanggal Sampai Menginap di Hotel belum diisi"); document.getElementById('e_tglsampai').focus(); return false;
                }
                
            }
            
            if (nchksewa.checked==true) {
                var etglsewa1=document.getElementById('e_tglsewa1').value;
                var etglsewa2=document.getElementById('e_tglsewa2').value;
                
                if (etglsewa1=="" && etglsewa2=="") {
                    alert("Data Sewa Kendaraan belum diisi"); document.getElementById('etglsewa1').focus(); return false;
                }
                
                if (etglsewa1=="") {
                    alert("Tanggal Mulai Sewa Kendaraan belum diisi"); document.getElementById('etglsewa1').focus(); return false;
                }
                
                if (etglsewa2=="") {
                    alert("Tanggal Sampai Sewa Kendaraan belum diisi"); document.getElementById('etglsewa2').focus(); return false;
                }
                
            }
            
        
        
        
            var cmt = confirm('pastikan tanda tangan terisi....!!! jika sudah terisi klik OK');
            if (cmt == false) {
                return false;
            }
            
            var uttd = data;//gambarnya
            
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            
            document.getElementById("demo-form2").action = "module/mod_br_entrybrdcccab/aksi_entrybrdcccab.php?module="+module+"&act=input"+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            
        });
}

$(document).ready(function() {
    ReloadTandaTangan();
})

</script>

<br/>&nbsp;
<span hidden>
    <input class='btn btn-default' type='button' name='buttonreload' value='Reload Tanda Tangan' onClick="ReloadTandaTangan()">
    <div style="color:red;">*) jika tanda tangan tidak muncul klik tombol <b>Reload Tanda Tangan</b></div>
</span>