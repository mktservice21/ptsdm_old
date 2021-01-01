<?PHP
    include "config/cek_akses_modul.php";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact=$_GET['act'];
    
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pmodule="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    
?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">
    
    <div class="page-title"><div class="title_left"><h3>Laporan Monitoring User KI dan KS</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='form_eksekusi' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div hidden class='col-md-12 col-sm-12 col-xs-12'>
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
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Cabang</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="ShowDataDokter()">
                                                <?PHP
                                                $query = "select distinct icabangid as icabangid, nama as nama from MKT.icabang WHERE 1=1 ";
                                                $query .=" AND IFNULL(aktif,'')<>'N' ";
                                                $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -') ";
                                                $query .=" order by nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>--Pilihan--</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidcab=$z['icabangid'];
                                                    $pcabnm=$z['nama'];
                                                    echo "<option value='$pnidcab'>$pcabnm</option>";
                                                }
                                                echo "<option value='' >--Non Aktif--</option>";
                                                $query = "select distinct icabangid as icabangid, nama as nama from MKT.icabang WHERE 1=1 ";
                                                $query .=" AND IFNULL(aktif,'')='N' ";
                                                $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -') ";
                                                $query .=" order by nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidcab=$z['icabangid'];
                                                    $pcabnm=$z['nama'];
                                                    echo "<option value='$pnidcab'>$pcabnm</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>User</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_dokter" name="cb_dokter" onchange="ShowDataKaryawanDokt()">
                                                <?PHP
                                                $query = "select distinct a.dokterid as dokterid, b.nama as nama "
                                                        . " from hrd.br0 as a JOIN hrd.dokter as b "
                                                        . " on a.dokterid=b.dokterid where IFNULL(a.stsbr,'')='KI' ";
                                                $query .=" order by b.nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>--Pilihan--</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pniddokt=$z['dokterid'];
                                                    $pdoktnm=$z['nama'];
                                                    echo "<option value='$pniddokt'>$pdoktnm ($pniddokt)</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                                
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div id='loading'></div>
                                <div id='c-datakaryawan'>
                                    
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                </div>
            </div>
            
            
        </form>
        
        
    </div>
    
    
</div>

<script>
    
    function disp_confirm(pText)  {
        if (pText == "excel") {
            document.getElementById("form_eksekusi").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("form_eksekusi").submit();
            return 1;
        }else{
            document.getElementById("form_eksekusi").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("form_eksekusi").submit();
            return 1;
        }
    }
    
    
    function ShowDataDokter() {
        var eidcb =document.getElementById('cb_cabang').value;
        
        $.ajax({
            type:"post",
            url:"module/ks_monitorkiks/viewdata_kiksdr.php?module=viewdatadoktcab",
            data:"uidcb="+eidcb,
            success:function(data){
                $("#cb_dokter").html(data);
                KosongkanKryDokt();
            }
        });
    }
    
    function ShowDataKaryawanDokt() {
        var eidcb =document.getElementById('cb_cabang').value;
        var eiddokt =document.getElementById('cb_dokter').value;
        //$("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/ks_monitorkiks/viewdata_kiksdr.php?module=viewdatakaryawanpilih",
            data:"uidcb="+eidcb+"&uiddokt="+eiddokt,
            success:function(data){
                $("#c-datakaryawan").html(data);
                $("#loading").html("");
            }
        });
    }
    
    function KosongkanKryDokt() {
        $("#c-datakaryawan").html("");
        $("#loading").html("");
    }
</script>


<style>
    .form-group, .input-group, .control-label {
        margin-bottom:2px;
    }
    .control-label {
        font-size:11px;
    }
    #datatable input[type=text], #tabelnobr input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:11px;
        height: 25px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }

    table.datatable, table.tabelnobr {
        color: #000;
        font-family: Helvetica, Arial, sans-serif;
        width: 100%;
        border-collapse:
        collapse; border-spacing: 0;
        font-size: 11px;
        border: 0px solid #000;
    }

    table.datatable td, table.tabelnobr td {
        border: 1px solid #000; /* No more visible border */
        height: 10px;
        transition: all 0.1s;  /* Simple transition for hover effect */
    }

    table.datatable th, table.tabelnobr th {
        background: #DFDFDF;  /* Darken header a bit */
        font-weight: bold;
    }

    table.datatable td, table.tabelnobr td {
        background: #FAFAFA;
    }

    /* Cells in even rows (2,4,6...) are one color */
    tr:nth-child(even) td { background: #F1F1F1; }

    /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
    tr:nth-child(odd) td { background: #FEFEFE; }

    tr td:hover.biasa { background: #666; color: #FFF; }
    tr td:hover.left { background: #ccccff; color: #000; }

    tr td.center1, td.center2 { text-align: center; }

    tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
    tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
    /* Hover cell effect! */
    tr td {
        padding: -10px;
    }
    .divnone {
        display: none;
    }
</style>