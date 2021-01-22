<?PHP
    include "config/cek_akses_modul.php";
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    $fgroupid=$_SESSION['GROUP'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    //$fkaryawan="0000000158"; $fjbtid="05";//hapussaja
    
    $pfilterkaryawan="";
    $pfilterkaryawan2="";
    $pfilterkry="";
    
    //$fjbtid=="38" OR $fjbtid=="33" OR 
    if ($fjbtid=="38" OR $fjbtid=="33" OR $fjbtid=="05" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
        
        $pnregion="";
        if ($fkaryawan=="0000000159") $pnregion="T";
        elseif ($fkaryawan=="0000000158") $pnregion="B";
        $pfilterkry=CariDataKaryawanByCabJbt($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
        
    }elseif ($fjbtid=="38x" OR $fjbtid=="33x") {
        $pnregion="";
        $pfilterkry=CariDataKaryawanByRsmAuthCNIT($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
    }
    
    
    //echo "karyawan : $pfilterkaryawan<br/>karyawan2 : $pfilterkaryawan2<br/>";exit;
    
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Daftar Dokter</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <?PHP
                            if ($_SESSION['MOBILE']!="Y") {
                                echo "<span hidden><button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button></span>";
                            }
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Karyawan</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                                <?PHP
                                                if (!empty($pfilterkaryawan)) {
                                                    $query = "select distinct b.karyawanid as karyawanid, b.nama as nama from hrd.karyawan as b WHERE 1=1  ";
                                                    $query .= " AND b.karyawanid IN $pfilterkaryawan ";
                                                }else{
                                                    $query = "select distinct a.karyawanid as karyawanid, b.nama as nama "
                                                        . " from hrd.mr_dokt as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid ";
                                                }
                                                $query .= " ORDER BY b.nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidkry=$z['karyawanid'];
                                                    $pnamakry=$z['nama'];
                                                    echo "<option value='$pnidkry'>$pnamakry ($pnidkry)</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Status User</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_status" name="cb_status">
                                                <?PHP
                                                echo "<option value=''>-- All --</option>";
                                                echo "<option value='Y' selected>Aktif</option>";
                                                echo "<option value='N'>Non Aktif</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                

                            </div>
                        </div>           
                    </div>

                </div>
            </div>
        </form>

    </div>
    <!--end row-->
</div>

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
    
    function ShowCOA(udiv, ucoa) {
        var icar = "";
        var idiv = document.getElementById(udiv).value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_generalledger/viewdata.php?module=viewcoadivisichk",
            data:"umr="+icar+"&udivi="+idiv,
            success:function(data){
                $("#"+ucoa).html(data);
            }
        });
    }
</script>