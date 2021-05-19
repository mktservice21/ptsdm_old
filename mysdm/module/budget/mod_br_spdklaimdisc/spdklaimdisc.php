<?PHP
    include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $hari_ini2 = date("Y-01-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    //$tgl_pertama = date('F Y', strtotime($hari_ini2));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $pkryidpilih="";
    $ptglpilih01="";
    $ptglpilih02="";
    if (isset($_SESSION['SPDKDKARYA'])) $pkryidpilih=$_SESSION['SPDKDKARYA'];
    if (isset($_SESSION['SPDKDTGL01'])) $ptglpilih01=$_SESSION['SPDKDTGL01'];
    if (isset($_SESSION['SPDKDTGL02'])) $ptglpilih02=$_SESSION['SPDKDTGL02'];
    
    if (!empty($ptglpilih01)) $tgl_pertama = $ptglpilih01;
    if (!empty($ptglpilih02)) $tgl_akhir = $ptglpilih02;
    if (!empty($pkryidpilih)) $fkaryawan = $pkryidpilih;
    
    $pmodule="";
    $pidmenu="";
    $pact="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
?>


<div class="">

    <div class="page-title">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Permintaan Dana Klaim Discount";
                if ($pact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        switch($pact){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var eaksi = "module/budget/mod_br_spdklaimdisc/aksi_spdklaimdisc.php";
                        var ekryid=document.getElementById('cb_karyawan').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/budget/mod_br_spdklaimdisc/viewdatatabelpdkd.php?module="+module+"&idmenu="+idmenu,
                            data:"ukryid="+ekryid+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&uaksi="+eaksi,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function ProsesDataHapus(ket, noid){
                        
                        
                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');

                            if (r==true) {

                                var txt;
                                var textket = prompt("Masukan alasan "+ket+" : ", "");
                                if (textket == null || textket == "") {
                                    txt = textket;
                                } else {
                                    txt = textket;
                                }


                                if (txt=="") {
                                    alert("alasan harus diisi...");
                                    return false;
                                }else if (txt==null) {
                                    return false;
                                }

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("d-form2").action = "module/budget/mod_br_spdklaimdisc/aksi_spdklaimdisc.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                                document.getElementById("d-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }



                    }
    
                </script>

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='d-form1' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div class='col-sm-3'>
                                Yang membuat
                                <div class="form-group">
                                    <select class='form-control' id="cb_karyawan" name="cb_karyawan" onchange="">
                                        <?PHP
                                            //if ($pidgroup=="1" OR $pidgroup=="24") {
                                                $query = "select karyawanId as karyawanid, nama as nama_karyawan from hrd.karyawan WHERE 1=1 ";
                                                $query .=" AND jabatanid NOT IN ('15', '10', '18', '08', '20', '05')";
                                            //}else{
                                            //    $query = "select karyawanId as karyawanid, nama as nama_karyawan from hrd.karyawan WHERE karyawanId='$fkaryawan' ";
                                            //}
                                            $query .= " Order by nama, karyawanId";
                                            $tampilket= mysqli_query($cnmy, $query);
                                            $ketemu=mysqli_num_rows($tampilket);
                                            //if ((INT)$ketemu<=0) 
                                            echo "<option value='' selected>-- Pilih --</option>";

                                            while ($du= mysqli_fetch_array($tampilket)) {
                                                $nidkry=$du['karyawanid'];
                                                $nnmkry=$du['nama_karyawan'];
                                                $nidkry_=(INT)$nidkry;
                                                if ($nidkry==$fkaryawan)
                                                    echo "<option value='$nidkry' selected>$nnmkry ($nidkry_)</option>";
                                                else
                                                    echo "<option value='$nidkry'>$nnmkry ($nidkry_)</option>";

                                            }

                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Periode Permintaan
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
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
                                   <div class='input-group date' id='cbln02'>
                                       <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                       <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                       </span>
                                   </div>
                               </div>
                           </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                               </div>
                           </div>
                       </form>

                        <div id='loading'></div>
                        <div id='c-data'>

                        </div>

                    </div>
                </div>
                
                
                

                <?PHP

            break;

            case "tambahbaru":
                include "tambahpdkd.php";
            break;

            case "editdata":
                include "tambahpdkd.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>