<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Isi Data COA Budget Request</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        
        $aksi="module/mod_br_entrydcc/aksi_entrybrdcc.php";
        switch($_GET['act']){
            default:
                ?>
        
                
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    //$(document).ready(function() {
                        //KlikDataTabel();
                    //} );

                    function KlikDataTabel() {
                        
                        var etipe=document.getElementById('cb_tgltipe').value;
                        var etgl1=document.getElementById('e_periode01').value;
                        var etgl2=document.getElementById('e_periode02').value;
                        var ekodeid=document.getElementById('kodeid').value;
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
                            url:"module/mod_br_isicoa/aksi_brisicoa.php?module=breditcoa&idmenu="+idmenu+"&act=simpan",
                            data:"utgltipe="+etipe+"&utgl1="+etgl1+"&utgl2="+etgl2+"&kodeid="+ekodeid+"&cekhanya="+ek,
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
                            Periode By
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                    <option value="1">Last Input / Update</option>
                                    <option value="2" selected>Tanggal Transfer</option>
                                    <option value="3">Tanggal Terima</option>
                                    <option value="4">Tanggal Pengajuan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Periode
                            <div class="form-group">
                                <div class='input-group date' id='tgl01'>
                                    <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            s/d.
                            <div class="form-group">
                                <div class='input-group date' id='tgl02'>
                                    <input type='text' id='e_periode02' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        

                        <div class='col-sm-2'>
                            Kode / Posting
                            <div class="form-group">
                                <select class='form-control input-sm' name='kodeid' id='kodeid'>
                                <?PHP
                                    include "config/koneksimysqli_it.php";
                                    
                                    if ($_SESSION['ADMINKHUSUS']=="Y") $fil=" AND divprodid in $_SESSION[KHUSUSSEL] ";
                                        
                                    $sql=mysqli_query($cnit, "SELECT distinct kodeid, nama, divprodid from hrd.br_kode where br<>'N' "
                                            . " and divprodid not in ('OTC') $fil order by divprodid, nama, kodeid");
                                    
                                    echo "<option value=''>--Pilih--</option>";
                                    while ($Xt=mysqli_fetch_array($sql)){
                                        if ((int)$Xt['kodeid']==(int)$fsel)
                                            echo "<option value='$Xt[kodeid]' selected>$Xt[divprodid] - $Xt[nama] ($Xt[kodeid])</option>";
                                        else
                                            echo "<option value='$Xt[kodeid]'>$Xt[divprodid] - $Xt[nama] ($Xt[kodeid])</option>";
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            &nbsp;
                            <div class="form-group">
                                <input type=checkbox value='cek' name='cekhanya' id='cekhanya' class='cekhanya' checked> Belum ada COA <br/>
                            </div>
                        </div>
                        
                        
                        <div class='col-sm-2'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="Refresh" onclick="RefreshDataTabel()">
                           </div>
                       </div>
                       
                        
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatable' class='table nowrap table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th><th>Aksi</th>
                                        <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th><th>Tgl. Terima</th><th>Keterangan</th>
                                        <th width='80px'>Yg Membuat</th><th width='100px'>Dokter</th><th width='50px'>Jumlah</th><th width='50px'>Realisasi</th>
                                        <th width='50px'>Realisasi</th><th>No Slip</th><th>Kode</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>
                       
                    </div>
                </div>
                
                <?PHP

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

