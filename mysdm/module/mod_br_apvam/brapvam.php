<?PHP
    $pildata = "'tgl1','tgl2', 'e_idkaryawan', 'e_lvlposisi', 'chkbox_divisiprod[]', 'e_ketapv'";
?>
<style>
    .divnone {
        display: none;
    }
    #per-kiri{float:left;width:30%; margin-right: 15px;}
    #per-kanan{float:left;width:30%; margin-right: 5px;}
</style>

<script>
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
     
    
    function pilihData(ket, periode1, periode2, ikaryawan, ilevel, idivisi, ketapv){
        var etgl1=document.getElementById(periode1).value;
        var etgl2=document.getElementById(periode2).value;
        var ekaryawan=document.getElementById(ikaryawan).value;
        var elevel=document.getElementById(ilevel).value;
        var eketapv=document.getElementById(ketapv).value;
        
        var chk_arr =  document.getElementsByName(idivisi);
        var chklength = chk_arr.length;             
        var alldiv="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                alldiv =alldiv + "'"+chk_arr[k].value+"',";
            }
        }
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_apvam/viewdata.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&ukaryawan="+ekaryawan+"&ulevel="+elevel+"&udiv="+alldiv+"&uketapv="+eketapv,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
    
    function ProsesData(ket, cekbr){
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        
        var txt;
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
        
        $.ajax({
            type:"post",
            url:"module/mod_br_apvam/aksi_brapvam.php?module="+ket,
            data:"uket="+ket+"&chkbox_br="+allnobr+"&ketrejpen="+txt,
            success:function(data){
                pilihData('unapprove',<?PHP echo $pildata; ?>);
                alert(data);
            }
        });
        
    }
    
    
</script>
<?PHP
    include "config/koneksimysqli_it.php";
    $aksi="module/mod_br_apvam/aksi_brapvam.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
                        
?>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Approve Budget Request By AM</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        
        <form name='form1' id='form1' method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=approve&idmenu=$_GET[idmenu]"; ?>'
                    enctype='multipart/form-data' onsubmit="return confirm('Apakah akan melalukan proses...?');">
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class="well" style="overflow: auto">
                        <input onclick="pilihData('approve',<?PHP echo $pildata; ?>)" class='btn btn-default' type='button' name='buttonview1' value='Belum Approve'>
                        <input onclick="pilihData('unapprove',<?PHP echo $pildata; ?>)" class='btn btn-default' type='button' name='buttonview2' value='Sudah Approve'>
                        <input onclick="pilihData('pending',<?PHP echo $pildata; ?>)" class='btn btn-default' type='button' name='buttonview3' value='Pending'>
                        <input onclick="pilihData('reject',<?PHP echo $pildata; ?>)" class='btn btn-default' type='button' name='buttonview4' value='Reject'>
                    </div>
                    
                    <div class='col-sm-3'>
                        Periode
                        <div class="form-group">
                            <div class='input-group date' id='tgl01'>
                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                     <div class='col-sm-3'>
                        <small>s/d.</small>
                        <div class="form-group">
                            <div class='input-group date' id='tgl02'>
                                <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                     <div class='col-sm-3'>
                        <small>Approve Employee :</small>
                        <div class="form-group">
                            <div class='input-group date'>
                                <input type='hidden' class='form-control' id='e_lvlposisi' name='e_lvlposisi' value='<?PHP echo $_SESSION['LVLPOSISI']; ?>' Readonly>
                                <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $_SESSION['IDCARD']; ?>' Readonly>
                                <input type='text' class='form-control input-sm' id='e_karyawan' name='e_karyawan' value='<?PHP echo $_SESSION['NAMALENGKAP']; ?>' Readonly>
                                <input type='hidden' class='form-control input-sm' id='e_ketapv' name='e_ketapv' value='AM' Readonly>
                            </div>
                        </div>
                    </div>
                    
                     <div class='col-sm-3'>
                        <small>Divisi :</small>
                        <div class="form-group">
                            <div class='input-group date'>
                                <?PHP
                                $sql=mysqli_query($cnit, "SELECT DivProdId, nama FROM MKT.divprod where br='Y' order by nama");
                                while ($Xt=mysqli_fetch_array($sql)){
                                    echo "<input type=checkbox value='$Xt[DivProdId]' name='chkbox_divisiprod[]' checked> $Xt[DivProdId] ";
                                }
                                ?>
                                <hr/>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div id='loading'></div>
                    <div id='c-data'>
                        <div class='x_content'>
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th><th><input type="checkbox" id="chkbtnbr" value="select" 
                                            onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" /></th>
                                        <th width='60px'>Tanggal</th><th width='40px'>Kode</th><th>Yg Membuat</th>
                                        <th width='80px'>Cabang</th><th width='100px'>Dokter</th><th width='50px'>Jumlah</th>
                                        <th width='50px'>Realisasi</th><th>CN</th><th>No Slip</th><th width='60px'>Tgl. Transfer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                
                            </table>
                            
                            
                        </div>
                    </div>
                    
                    
                    
                </div>
            </div>
            
        </form>
        
    </div>
</div>

<?php

?>
