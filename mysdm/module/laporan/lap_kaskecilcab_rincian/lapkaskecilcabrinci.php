<?PHP
    include "config/cek_akses_modul.php";
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pmodule="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    
    if ($pmodule=="laprinciankaskecilcabotc") {
        $pseldiv0="";
        $pseldiv1="";
        $pseldiv2="selected";
    }else{
        if ($fkaryawan=="0000000144") {
            $pseldiv0="";
            $pseldiv1="selected";
            $pseldiv2="";
        }else{
            $pseldiv0="selected";
            $pseldiv1="";
            $pseldiv2="";
        }
    }
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Rincian Kas Kecil Cabang</h3></div></div><div class="clearfix"></div>
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
                                echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
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
                                
                                
                                <div class='col-sm-6'>
                                    <b>Periode</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type='text' id='e_tgl1' name='e_tgl1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-sm-6'>
                                    <b>s/d.</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln02'>
                                            <input type='text' id='e_tgl2' name='e_tgl2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Karyawan</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                                <?PHP
                                                $query = "select b.karyawanId as karyawanid, b.nama as nama from hrd.karyawan as b WHERE IFNULL(b.nama,'')<>'' ";
                                                $query .=" AND b.karyawanId NOT IN (select distinct karyawanId from dbmaster.t_karyawanadmin)";
                                                $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ')  "
                                                        . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                                        . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
                                                        . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
                                                if ($pmodule=="laprinciankaskecilcabotc") {
                                                    $query .= " AND b.divisiId='OTC' ";
                                                }
                                                $query .=" order by b.nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>--All--</option>";
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
                                        <b>Jenis</b> <input type="checkbox" id="chkjenisobat" value="deselect" onClick="SelAllCheckBox('chkjenisobat', 'chkbox_jnsobat[]')" checked/>
                                        <div class="form-group">
                                            <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    $query = "select a.urutan, a.kode, a.nama, a.coa_kode, a.coa_kode_otc, a.divisi from dbmaster.t_kode_kascab a "
                                                            . " WHERE 1=1 ";
                                                    if ($pmodule=="laprinciankaskecilcabotc") {
                                                        $query .= " AND IFNULL(a.divisi,'') NOT IN ('ETH')  ";
                                                    }else{
                                                        if ($fkaryawan=="0000000144") $query .= " AND IFNULL(a.divisi,'') NOT IN ('OTC', 'CHC')  ";
                                                    }
                                                    if ($pmodule=="laprinciankaskecilcabotc") $query .= " ORDER BY a.urutan";
                                                    else $query .= " ORDER BY a.divisi, a.urutan";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pnobrid=$z['kode'];
                                                        $pnmbrid=$z['nama'];
                                                        $pdivi=$z['divisi'];
                                                        
                                                        if ($pdivi=="ETH") $pdivi="Ethical";
                                                        elseif ($pdivi=="OTC" OR $pdivi=="CHC") $pdivi="CHC";
                                                        
                                                        $pnamakode="$pnobrid - $pnmbrid";
                                                        if (!empty($pdivi)) $pnamakode="$pnobrid - $pnmbrid &nbsp; ($pdivi)";
                                                        echo "&nbsp; <input type=checkbox value='$pnobrid' name='chkbox_jnsobat[]' checked> $pnamakode<br/>";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Divisi</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_divisiid" name="cb_divisiid">
                                                <?PHP
                                                if ($fdivisi=="OTC" OR $fdivisi=="CHC") {
                                                    echo "<option value='OTC' $pseldiv2>CHC</option>";
                                                }else{
                                                    if ($fkaryawan=="0000000144") {
                                                        echo "<option value='ETH' $pseldiv1>ETHICAL</option>";
                                                    }else{
                                                        echo "<option value='' $pseldiv0>--ALL--</option>";
                                                        echo "<option value='ETH' $pseldiv1>ETHICAL</option>";
                                                        echo "<option value='OTC' $pseldiv2>CHC</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Status</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_status" name="cb_status">
                                                <?PHP
                                                echo "<option value=''>-- ALL --</option>";
                                                echo "<option value='apvfin' selected>Sudah Proses Finance</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Sort By</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_sortby" name="cb_sortby">
                                                <?PHP
                                                echo "<option value='kry' selected>Nama Karyawan</option>";
                                                echo "<option value='periode'>Periode Pengajuan</option>";
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