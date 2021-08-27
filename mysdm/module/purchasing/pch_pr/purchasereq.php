<?PHP
    include "config/cek_akses_modul.php";
    $hari_ini2 = date("Y-m-d");
    $hari_ini = date("Y-m-01");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    //$tgl_pertama = date('d F Y', strtotime($hari_ini));
    $tgl_akhir = date('F Y', strtotime($hari_ini2));
    
    if (!empty($_SESSION['PCHSESITGL01'])) $tgl_pertama = $_SESSION['PCHSESITGL01'];
    if (!empty($_SESSION['PCHSESITGL02'])) $tgl_akhir = $_SESSION['PCHSESITGL02'];
    
    $pidkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $pidgroup=$_SESSION['GROUP'];
    $pidjabatan=$_SESSION['JABATANID'];
    
    
    $pfilterkaryawan="";
    $pfilterkaryawan2="";
    $pfilterkry="";
    //$pidjabatan=="38" OR $pidjabatan=="33" OR 
    if ($pidjabatan=="38" OR $pidjabatan=="33" OR $pidjabatan=="05" OR $pidjabatan=="20" OR $pidjabatan=="08" OR $pidjabatan=="10" OR $pidjabatan=="18" OR $pidjabatan=="15") {

        $pnregion="";
        if ($pidkaryawan=="0000000159") $pnregion="T";
        elseif ($pidkaryawan=="0000000158") $pnregion="B";
        $pfilterkry=CariDataKaryawanByCabJbt($pidkaryawan, $pidjabatan, $pnregion);

        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }

    }
        
    $pbolehpilihkaryawan=false;
    $pkryid_pl="";
    if ($pidgroup=="1" OR $pidgroup=="24") {
        $pkryid_pl="";
        $pbolehpilihkaryawan=true;
    }else{
        $pkryid_pl=$pidkaryawan;
    }
    
    $query = "select karyawanid from dbpurchasing.t_pr_admin WHERE karyawanid='$pidkaryawan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $pkryid_pl="";
        $pbolehpilihkaryawan=true;
    }
    
    
    
    
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Purchase Request";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/purchasing/pch_pr/.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
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
                        var ket="";
                        var ekryid=document.getElementById('cb_karyawan').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/purchasing/pch_pr/viewdatatabelpr.php?module="+module+"&act="+act+"&idmenu="+idmenu+"&nmun="+idmenu,
                            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&uidc="+eidc+"&ucabang="+"&ukryid="+ekryid,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
                <script>
                    function disp_confirm(pText)  {

                        if (pText == "excel") {
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
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
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div class='col-sm-3'>
                                Yg. Mengajukan
                                <div class="form-group">
                                    <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                        
                                        <?PHP
                                            if ($pbolehpilihkaryawan==true) {
                                                echo "<option value='' selected>-- Pilih --</option>";
                                                $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                    FROM hrd.karyawan WHERE 1=1 ";
                                                $query_kry .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                $query_kry .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                $query_kry .=" ORDER BY nama";
                                            }else{
                                                if ($pidjabatan=="08" OR $pidjabatan=="10" OR $pidjabatan=="18" OR $pidjabatan=="15") {
                                                    $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                        FROM hrd.karyawan WHERE karyawanId='$pidkaryawan' ";
                                                    $query_kry .=" ORDER BY nama";
                                                }elseif ($pidjabatan=="38" OR empty($pfilterkaryawan)) {
                                                    $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                        FROM hrd.karyawan WHERE 1=1 ";
														
													if ($pidkaryawan=="0000002329") {
														$query_kry .=" AND karyawanId IN ('0000002329', '0000000158')";
													}else{
														$query_kry .=" AND karyawanId='$pidkaryawan' ";
													}
													
                                                    $query_kry .=" ORDER BY nama";
                                                }else{
                                                    $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                        FROM hrd.karyawan WHERE karyawanId IN $pfilterkaryawan ";
                                                    $query_kry .=" ORDER BY nama";
                                                }
                                            }

                                            if (!empty($query_kry)) {
                                                $tampil = mysqli_query($cnmy, $query_kry);
                                                $ketemu= mysqli_num_rows($tampil);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pkaryid=$z['karyawanid'];
                                                    $pkarynm=$z['nama'];
                                                    $pkryid=(INT)$pkaryid;
                                                    
                                                    if (empty($pkryid_pl)) {
                                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                    }else{
                                                        if ($pkaryid==$pkryid_pl) {
                                                            echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                            $pidkaryawan=$pkryid_pl;
                                                        }else{
                                                            if ($pkaryid==$pidkaryawan)
                                                                echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                            else
                                                                echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                        }
                                                    }
                                                }
                                            }else{
                                                echo "<option value='' selected>-- Pilih --</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Tgl. Transaksi 
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



                            <div class='col-sm-5'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                                   <!--<a href="?module=bgtpdkaskecilcabang&idmenu=350&act=8" class='btn btn-dark btn-xs' >Permintaan Dana</a>-->
                                   <!--<input type='hidden' class='btn btn-default btn-xs' id="s-print" value="Preview" onclick="disp_confirm('bukan')">
                                   <input type='hidden' class='btn btn-info btn-xs' id="s-excel" value="Excel" onclick="disp_confirm('excel')">-->
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
                include "tambahpr.php";
            break;

            case "editdata":
                include "tambahpr.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

