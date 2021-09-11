<?PHP
    include "config/cek_akses_modul.php";
    
    date_default_timezone_set('Asia/Jakarta'); 
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['FINRUTPERENTY1'])) $tgl_pertama = $_SESSION['FINRUTPERENTY1'];
    if (!empty($_SESSION['FINRUTPERENTY2'])) $tgl_akhir = $_SESSION['FINRUTPERENTY2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $pidjabatan=$_SESSION['JABATANID'];
    
    $pidmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pidact=$_GET['act'];
    
    $perror="";
    $pketeksekusi="";
    if (isset($_GET['iderror'])) $perror=$_GET['iderror'];
    if (isset($_GET['keteks'])) $pketeksekusi=$_GET['keteks'];
    
    include "config/koneksimysqli_ms.php";
    $pfilterkaryawan="";
    
    if ($pidjabatan=="38") {
        $pnregion="";
        $pfilterkry=CariDataKaryawanByCabJbt($fkaryawan, $pidjabatan, $pnregion);

        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
    }
    
?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Biaya Rutin By Admin Cabang";
                if ($pidact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pidact=="editdata")
                    echo "Edit $judul";
                elseif ($pidact=="uploaddok")
                    echo "Upload Bukti $judul";
                elseif ($pidact=="editttddata")
                    echo "Edit Tanda Tangan $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi3.php";
        switch($pidact){
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
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var ekry=document.getElementById('cb_karyawan').value;
                        var elstkry=document.getElementById('lst_karyawan').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/budget/bgt_brrutin_admcab/viewdatatabel_brac.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"uperiode1="+etgl1+"&uperiode2="+etgl2+"&ukry="+ekry+"&ulstkry="+elstkry,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
                
                <script>
                    function ProsesData(ket, noid){

                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                            if (r==true) {

                                var txt;
                                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                                    if (textket == null || textket == "") {
                                        txt = textket;
                                    } else {
                                        txt = textket;
                                    }
                                }

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("d-form2").action = "module/budget/bgt_brrutin_admcab/aksi_brrutinadmincab.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+txt+"&ket="+ket+"&id="+noid;
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

                        
                        <?PHP
                        
                        if ($perror=="error" OR $perror=="berhasil" OR $perror=="hapusok" OR $perror=="updatettd" OR $perror=="uploaddok" OR $perror=="hapusdok") {

                            echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                                echo "<div class='x_panel'>";

                                    echo "<div class='x_title'>";
                                        if ($perror=="error") {
                                            echo "<h2 style='color:red;'>Gagal Simpan Data...</h2>";
                                            echo "<div class='clearfix'></div>";
                                            echo "<div>($pketeksekusi)</div>";
                                        }elseif ($perror=="berhasil") {
                                            echo "<h2 style='color:blue;'>berhasil simpan dengan idrutin : $pketeksekusi</h2>";
                                        }elseif ($perror=="hapusok" OR $perror=="updatettd" OR $perror=="uploaddok" OR $perror=="hapusdok") {
                                            echo "<h2 style='color:blue;'>$pketeksekusi</h2>";
                                        }
                                        echo "<ul class='nav navbar-right panel_toolbox'><li><a class='close-link'><i class='fa fa-close'></i></a></li></ul>";
                                        echo "<div class='clearfix'></div>";

                                    echo "</div>";

                                echo "</div>";

                            echo "</div>";

                        }
                        ?>
                        
                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                              id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>


                            <div class='col-sm-3'>
                                Karyawan
                                <div class="form-group">
                                    <span hidden><textarea id='lst_karyawan' name='lst_karyawan'><?PHP echo $pfilterkaryawan2; ?></textarea></span>
                                    <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                        <?PHP
                                            //echo "<option value=''>--All--</option>";
                                            if ($pidgroup=="1" OR $pidgroup=="24") {
                                                $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                    FROM hrd.karyawan WHERE 1=1 AND jabatanId='38' ";
                                                $query_kry .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                $query_kry .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P', 'BKS -') "
                                                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                $query_kry .=" ORDER BY nama";
                                            }else{
                                                $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                    FROM hrd.karyawan WHERE (karyawanId='$fkaryawan' OR karyawanId IN $pfilterkaryawan) ";
                                                $query_kry .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P', 'BKS -') "
                                                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                $query_kry .=" ORDER BY nama";
                                            }

                                            if (!empty($query_kry)) {
                                                $tampil = mysqli_query($cnmy, $query_kry);
                                                $ketemu= mysqli_num_rows($tampil);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pkaryid=$z['karyawanid'];
                                                    $pkarynm=$z['nama'];
                                                    $pkryid=(INT)$pkaryid;

                                                    if ($pkaryid==$fkaryawan)
                                                        echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                    else
                                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                    
                                                }
                                            }else{
                                                echo "<option value='' selected>-- Pilih --</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Periode Input
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
                include "tambahbradmcb.php";
            break;

            case "editdata":
                include "tambahbradmcb.php";
            break;

            case "editttddata":
                include "editttd_admcb.php";
            break;

            case "uploaddok":
                include "uploaddokbradmcb.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }

</style>

<script>
    // SCROLL
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
        } else {
            document.getElementById("myBtn").style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    // END SCROLL
</script>


