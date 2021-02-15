<?PHP
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $aksi="eksekusi3.php";
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpkslstdoktmr01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpkslstdoktmr02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpkslstdoktmr03_".$puserid."_$now ";
    
    
    $pidkaryawan=$_POST['uidkry'];
    $pidcab=$_POST['uidcab'];
    $pstsdr=$_POST['ustsdr'];
    
    $_SESSION['LHTKSMRID']=$pidkaryawan;
    $_SESSION['LHTKSCBID']=$pidcab;
    $_SESSION['LHTKSDAPT']="Y";
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $query = "select nama from hrd.karyawan where karyawanid='$pidkaryawan'";
    $tampilk=mysqli_query($cnmy, $query);
    $rowk=mysqli_fetch_array($tampilk);
    $pnamakarywanpl=$rowk['nama'];
    
    $query = "select distinct a.srid, a.dokterid, a.idapotik, a.aptid, a.apttype, a.bulan from hrd.ks1 as a where ifnull(a.idapotik,'') IN ('', '0', '0000000000') and "
            . " a.srid='$pidkaryawan'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET apttype='0' WHERE IFNULL(apttype,'')<>'1'"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.srid, d.nama as nama_karyawan, a.dokterid, c.nama as nama_dokter, a.idapotik, a.aptid, a.apttype, a.bulan "
            . " from $tmp01 as a JOIN hrd.dokter as c on a.dokterid=c.dokterId "
            . " LEFT JOIN hrd.karyawan as d on a.srid=d.karyawanid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
?>

    <div class='x_content'>
        <b>List Data Dokter MR : <?PHP echo "$pnamakarywanpl"; ?></b>
        <hr/>
        <table id='datatabledrlstmr' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>&nbsp;</th>
                    <th width='20px'>Nama Dokter</th>
                    <th width='10px'>&nbsp;</th>
                    <th width='10px'>&nbsp;</th>
                    <th width='20px'>Apt. Type</th>
                    <th width='20px'>Bulan</th>
                    <th class='divnone'>&nbsp;</th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $prec=1;
                $query = "select distinct srid, nama_karyawan, dokterid, nama_dokter from $tmp02 order by nama_dokter";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidsr = $row["srid"];
                    $pnmsr = $row["nama_karyawan"];
                    $piddokt = $row["dokterid"];
                    $pnmdokt = $row["nama_dokter"];
                    
                    $plihatks="<button type='button' class='btn btn-info btn-xs' onclick=\"disp_confirm_ks('', '', '$piddokt', '')\">Preview KS</button>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$plihatks</td>";
                    echo "<td nowrap>$pnmdokt ($piddokt)</td>";
                    
                    echo "<td nowrap>";
                    
                        echo "<form method='POST' action='$aksi?module=$pmodule&act=input&idmenu=$pidmenu' "
                                . " id='form_data$no' name='form$no' data-parsley-validate "
                                . " target='_blank'>";
                            echo "<span hidden>";
                                echo "<button type='button' class='btn btn-dark btn-xs' onclick=\"disp_editdata('form_data$no')\">Edit</button>";
                                echo "<input type=checkbox value='$no' name=tag_km[] class='checkall$no' onClick='toggleCexBox(this)' >";
                            echo "</span>";
                        echo "</form>";
                        
                    echo "</td>";
                    
                    
                    $ilewat=true;
                    $query = "select distinct srid, dokterid, nama_dokter, apttype, bulan, aptid from $tmp02 WHERE srid='$pidsr' AND dokterid='$piddokt' order by nama_dokter, apttype, bulan";
                    $tampil2= mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pbulan = $row2["bulan"];
                        $papttype = $row2["apttype"];
                        $paptidpl = $row2["aptid"];
                        $pnmtype="R";
                        if ($papttype=="1") $pnmtype="D";
                        
                        $pplidapt="";
                        $query = "select distinct aptid from $tmp02 WHERE srid='$pidsr' AND dokterid='$piddokt' AND bulan='$pbulan' AND apttype='$papttype'";
                        $tampil3= mysqli_query($cnmy, $query);
                        while ($row3= mysqli_fetch_array($tampil3)) {
                            $pidaptpl=$row3['aptid'];
                            $pplidapt .=$pidaptpl.",";
                        }
                        if (!empty($pplidapt)) $pplidapt=substr($pplidapt, 0, -1);
                        
                        if ($ilewat==true) {
                            
                            echo "<td nowrap>";
                            
                                echo "<form method='POST' action='$aksi?module=$pmodule&act=input&idmenu=$pidmenu' "
                                        . " id='form_sdata$prec' name='sform$prec' data-parsley-validate "
                                        . " >";
                                    echo "<button type='button' class='btn btn-dark btn-xs' onclick=\"disp_editdata2('form_sdata$prec')\">Edit Data</button>";
                                echo "</form>";
                    
                            
                            echo "<span hidden>";
                            
                                echo "<input type='text' value='$piddokt' id='txtiddokt[$prec]' name='txtiddokt[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pidsr' id='txtidsr[$prec]' name='txtidsr[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pbulan' id='txtidbln[$prec]' name='txtidbln[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pnmdokt' id='txtnmdokt[$prec]' name='txtnmdokt[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pnmsr' id='txtnmsr[$prec]' name='txtnmsr[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$papttype' id='txtapttyp[$prec]' name='txtapttyp[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pplidapt' id='txtaptid[$prec]' name='txtaptid[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='checkbox' id='chkid[$no]' name='chkid[]' form='form_data$no' class='checkall$no' value='$prec'>";
                                
                                echo "<input type='text' value='$piddokt' id='txtiddokts' name='txtiddokts' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pidsr' id='txtidsrs' name='txtidsrs' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pbulan' id='txtidblns' name='txtidblns' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pnmdokt' id='txtnmdokts' name='txtnmdokts' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pnmsr' id='txtnmsrs' name='txtnmsrs' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$papttype' id='txtapttyps' name='txtapttyps' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$paptidpl' id='txtaptids' name='txtaptids' Readonly form='form_sdata$prec'>";
                            
                            echo "</span>";
                            
                            echo "<td >$paptidpl ($pnmtype)</td>";
                            echo "<td nowrap>$pbulan</td>";
                            echo "<td class='divnone'>$pnmdokt ($piddokt)</td>";
                            echo "</tr>";
                            $ilewat=false;
                        }else{
                        
                            echo "<tr>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>";
                            
                                echo "<form method='POST' action='$aksi?module=$pmodule&act=input&idmenu=$pidmenu' "
                                        . " id='form_sdata$prec' name='sform$prec' data-parsley-validate "
                                        . " >";
                                    echo "<button type='button' class='btn btn-dark btn-xs' onclick=\"disp_editdata2('form_sdata$prec')\">Edit Data</button>";
                                echo "</form>";
                                
                            echo "<span hidden>";
                            
                                echo "<input type='text' value='$piddokt' id='txtiddokt[$prec]' name='txtiddokt[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pidsr' id='txtidsr[$prec]' name='txtidsr[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pbulan' id='txtidbln[$prec]' name='txtidbln[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pnmdokt' id='txtnmdokt[$prec]' name='txtnmdokt[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pnmsr' id='txtnmsr[$prec]' name='txtnmsr[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$papttype' id='txtapttyp[$prec]' name='txtapttyp[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='text' value='$pplidapt' id='txtaptid[$prec]' name='txtaptid[$prec]' Readonly form='form_data$no'>";
                                echo "<input type='checkbox' id='chkid[]' name='chkid[]' form='form_data$no' class='checkall$no' value='$prec'>";
                                
                                echo "<input type='text' value='$piddokt' id='txtiddokts' name='txtiddokts' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pidsr' id='txtidsrs' name='txtidsrs' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pbulan' id='txtidblns' name='txtidblns' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pnmdokt' id='txtnmdokts' name='txtnmdokts' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$pnmsr' id='txtnmsrs' name='txtnmsrs' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$papttype' id='txtapttyps' name='txtapttyps' Readonly form='form_sdata$prec'>";
                                echo "<input type='text' value='$paptidpl' id='txtaptids' name='txtaptids' Readonly form='form_sdata$prec'>";
                                
                            echo "</span>";
                            
                            echo "</td>";
                            
                            echo "<td >$paptidpl ($pnmtype)</td>";
                            echo "<td nowrap>$pbulan</td>";
                            echo "<td class='divnone'>$pnmdokt ($piddokt)</td>";
                            echo "</tr>";
                            
                        }
                        $prec++;
                    }
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>


<style>
    .divnone {
        display: none;
    }
    #datatabledrlstmr th {
        font-size: 12px;
    }
    #datatabledrlstmr td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledrlstmr').DataTable( {
            //"stateSave": true,
            fixedHeader: true,
            "ordering": false,
            "processing": true,
            //"order": [[ 0, "asc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                //{ "orderable": false, "targets": 3 },
                //{ className: "text-right", "targets": [8, 9] },//right
                { className: "text-nowrap", "targets": [0, 1,2,3,4,6] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function disp_confirm_ks(pText, pnmform, eiddok, enmdokt)  {
        var eidkry =document.getElementById('cb_karyawan').value;
        
        if (eidkry=="") {
            alert("karyawan harus diisi...!!!");
            return false;
        }
        
        if (eiddok=="") {
            alert("dokter harus diisi...!!!");
            return false;
        }
        
        document.getElementById('e_iddokt').value=eiddok;
        document.getElementById('e_nmdokt').value=enmdokt;
        
        var eiddok2 =document.getElementById('e_iddokt').value;
        if (eiddok2=="") {
            alert("dokter harus diisi...!!!");
            return false;
        }
        
        //alert(eiddok); return false;
        
        if (pText == "excel") {
            document.getElementById('data_form01').action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById('data_form01').submit();
            return 1;
        }else{
            document.getElementById('data_form01').action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById('data_form01').submit();
            return 1;
        }
    }
    
    function disp_editdata(inform) {
    
        document.getElementById(inform).action = "<?PHP echo "eksekusi3.php?module=lihatkseditapt&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
        document.getElementById(inform).submit();
        return 1;
                        
    }
    
    function disp_editdata2(inform) {
    
        document.getElementById(inform).action = "<?PHP echo "?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&kriteria=Y&ket=bukan"; ?>";
        document.getElementById(inform).submit();
        return 1;
                        
    }
    
    function toggleCexBox(source) {
        var aInputs = document.getElementsByTagName('input');
        for (var i=0;i<aInputs.length;i++) {
            if (aInputs[i] != source && aInputs[i].className == source.className) {
                aInputs[i].checked = source.checked;
            }
        }
    }
    
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>

