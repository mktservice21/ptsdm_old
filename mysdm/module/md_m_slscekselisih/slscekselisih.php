<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Cek Selisih Closing Sales Per Distributor</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_ms.php";
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
                        var ket="";
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                        var ereg="";
                        
                        var etgl=document.getElementById('e_periode01').value;
                        var edist=document.getElementById('distibutor').value;
                        var ecekpros = document.getElementById('cekallpros').checked;
                        var ecekselisih = document.getElementById('cekselisih').checked;
                        if (ecekpros==false) {
                            var ek = "0";
                        }else{
                            var ek = "1";
                        }
                        
                        if (ecekselisih==false) {
                            var esel = "0";
                        }else{
                            var esel = "1";
                        }
                        
                        if (edist==""){
                            alert("distributor belum diisi...");
                            return false;
                        }
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_slscekselisih/aksi_slscekselisih.php?module="+ket,
                            data:"eket="+ek+"&utgl="+etgl+"&bulan="+etgl+"&distibutor="+edist+"&region="+ereg+"&selisih="+esel,
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
                            Periode
                            <div class="form-group">
                                <div class='input-group date' id='cbln01'>
                                    <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        

                        <div class='col-sm-3'>
                            Distributor
                            <div class="form-group">
                                <select class='form-control input-sm' name='distibutor' id='distibutor'>
                                <?PHP
                                    $pinsel="('0000000002', '0000000003', '0000000005', '0000000006', '0000000010', "
                                            . " '0000000011', '0000000016', '0000000023', '0000000030', '0000000031')";
                                    //cComboDistibutorHanya('', '', $pinsel);
										
                                    //cComboDistibutor('', '');
									
                                    $sql=mysqli_query($cnms, "SELECT distinct Distid, nama, alamat1 from MKT.distrib0 WHERE"
                                            . " Distid IN $pinsel order by Distid, nama");
                                    echo "<option value=''>--Pilih--</option>";
                                    while ($Xt=mysqli_fetch_array($sql)){
                                        $pdisid=$Xt['Distid'];
                                        $pdisnm=$Xt['nama'];
                                        $cidcek=(INT)$pdisid;
                                        echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                    }

                                    $sql=mysqli_query($cnms, "SELECT distinct Distid, nama, alamat1 from MKT.distrib0 WHERE"
                                            . " Distid NOT IN $pinsel order by Distid, nama");
                                    echo "<option value=''></option>";
                                    while ($Xt=mysqli_fetch_array($sql)){
                                        $pdisid=$Xt['Distid'];
                                        $pdisnm=$Xt['nama'];
										$cidcek=(INT)$pdisid;
                                        echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                    }
									
									
                                ?>
                                </select>
                            </div>
                        </div>
                        
                        <div hidden>
                            Region
                            <div class="form-group">
                                <select class='form-control input-sm' name='region' id='region'>
                                    <option value='B'>B - Barat</option>
                                    <option value='T'>T - Timur</option>
                                </select>
                            </div>
                        </div>
                        <div class='col-sm-2'>
                            &nbsp;
                            <div class="form-group">
                                <input type=checkbox value='cek' name='cekselisih' id='cekselisih' class='cekselisih' checked> Hanya selisih <br/>
                                <input type=checkbox value='cek' name='cekallpros' id='cekallpros' class='cekallpros'> Yang sudah Query
                            </div>
                        </div>
                        
                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="Proses" onclick="RefreshDataTabel()">
                           </div>
                       </div>
                       
                        
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatable' class='table nowrap table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th></th><!--<input type=checkbox value='cekall' name=cekall class='cekall'>-->
                                        <th>Selisih</th>
                                        <th>Qty</th>
                                        <th>Qty SDM</th>
                                        <th>Kode Produk</th>
                                        <th>Produk</th>
                                        <th>Tgl Jual</th>
                                        <th>FakturId</th>
                                        <th>Cabang</th>
                                        <th>Subdist</th>
                                        <th>Ecust</th>
                                        <th>Alamat</th>
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

