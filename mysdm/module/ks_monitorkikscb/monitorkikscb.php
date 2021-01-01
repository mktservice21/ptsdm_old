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
    
    <div class="page-title"><div class="title_left"><h3>Laporan Monitoring User KI dan KS Per Cabang</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='form_eksekusi' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
        
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
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Cabang</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="">
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
                                        <b>Saldo Type</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_rpttype" name="cb_rpttype" onchange="">
                                                <?PHP
                                                echo "<option value=''>--All--</option>";
                                                echo "<option value='P' selected>Hanya Yang Plus</option>";
                                                echo "<option value='M'>Hanya Yang Minus</option>";
                                                echo "<option value='N'>Tidak Ada Saldo</option>";
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
    
    
</div>

<script>
    
    function disp_confirm(pText)  {
        var eidcb =document.getElementById('cb_cabang').value;
        if (eidcb=="") {
            alert("Cabang harus dipilih...!!!");
            return false;
        }
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
</script>
