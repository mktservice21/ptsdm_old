<?PHP
    include "config/koneksimysqli_it.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>
                        
<script>
    function showPosting(subpost, epost){
        var esubpost = document.getElementById(subpost).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entryotc/viewdata.php?module=viewdataposting&data1="+esubpost+"&data2="+epost,
            data:"usubpost="+esubpost+"&upost="+epost,
            success:function(data){
                $("#"+epost).html(data);
            }
        });
    }
</script>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>Isi Data COA Budget Request OTC</h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_isicoaotc/aksi_brisicoaotc.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                        
                    }

                    $(document).ready(function() {
                        //KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ket="";
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi="";
                        var ekodeid=document.getElementById('cb_subpost').value;
                        var esubkodeid=document.getElementById('cb_post').value;
                        var ecekhanya = document.getElementById('cekhanya').checked;
                        if (ecekhanya==false) {
                            var ek = "0";
                        }else{
                            var ek = "1";
                        }
                        
                        
                        if (ekodeid==""){
                            alert("kode / posting belum diisi...");
                            return false;
                        }
                        var idmenu = <?PHP echo $_GET['idmenu']; ?>;
                        
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_isicoaotc/viewdatatabel.php?module=breditcoaotc&idmenu="+idmenu+"&act=simpan",
                            data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&kodeid="+ekodeid+"&cekhanya="+ek+"&subkodeid="+esubkodeid,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        

                        <div class='col-sm-2'>
                            <small>Periode By</small>
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                    <option value="1">Tanggal Pengajuan</option>
                                    <option value="2" selected>Tanggal Transfer</option>
                                    <option value="3">Belum Transfer</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            <small>Periode</small>
                            <div class="form-group">
                                <div class='input-group date' id='tgl01'>
                                    <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
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
                        
                        <div class='col-sm-2'>
                           <small>Posting</small>
                           <div class="form-group">
                                <select class='form-control input-sm' id='cb_subpost' name='cb_subpost' onchange="showPosting('cb_subpost', 'cb_post')">
                                    <?PHP
                                    $tampil=mysqli_query($cnit, "select distinct subpost, nmsubpost from hrd.brkd_otc where ifnull(subpost,'') <> '' order by nmsubpost");
                                    echo "<option value='' selected>-- Pilihan --</option>";
                                    while($a=mysqli_fetch_array($tampil)){ 
                                        if ($a['subpost']==$subposting)
                                            echo "<option value='$a[subpost]' selected>$a[nmsubpost]</option>";
                                        else
                                            echo "<option value='$a[subpost]'>$a[nmsubpost]</option>";
                                    }
                                    ?>
                                </select>
                           </div>
                       </div>
                        
                        <div class='col-sm-2'>
                           <small>Sub Posting</small>
                           <div class="form-group">
                                <select class='form-control input-sm' id='cb_post' name='cb_post' onchange="showCOANya('cb_subpost', 'cb_post', 'cb_coa')">
                                    <?PHP
                                    $filsub="";
                                    if (!empty($subposting)) $filsub="where subpost='$subposting' AND ifnull(subpost,'') <> ''";

                                    $tampil=mysqli_query($cnit, "select distinct kodeid, nama from hrd.brkd_otc $filsub order by nama");
                                    echo "<option value='' selected>-- Pilihan --</option>";
                                    while($a=mysqli_fetch_array($tampil)){ 
                                        if ($a['kodeid']==$posting)
                                            echo "<option value='$a[kodeid]' selected>$a[nama]</option>";
                                        else
                                            echo "<option value='$a[kodeid]'>$a[nama]</option>";
                                    }
                                    ?>
                                </select>
                           </div>
                       </div>

                       
                        
                        
                        <div class='col-sm-2'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type=checkbox value='cek' name='cekhanya' id='cekhanya' class='cekhanya' checked> Belum ada COA <br/> 
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="Refresh" onclick="RefreshDataTabel()">
                           </div>
                       </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th><th><input type="checkbox" id="chkbtnall" value="select" onClick="SelAllCheckBox('chkbtnall', 'chkbox_id[]')"/></th>
                                        <th width='20px'>No ID</th>
                                        <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th>
                                        <th>NoSlip</th>
                                        <th width='20px'>Alokasi Budget</th><th width='20px'>Cabang</th>
                                        <th width='50px'>Keterangan</th><th width='50px'>Keterangan</th>
                                        <th>Usulan</th>
                                        <th width='50px'>Realisasi</th><th width='50px'>Tgl. Realisasi</th>
                                        <th>Jumlah Realisasi</th><th width='50px'>Selisih</th>
                                        <th>Tgl Report SBY</th><th width='50px'>Jenis Report SBY</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        
                        
                        
                        
                    </div>
                </div>
                
                <?PHP

            break;

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;
        
            case "editterima":
                include "terima.php";
            break;
        
            case "edittransfer":
                include "transfer.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

