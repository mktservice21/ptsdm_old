<?php
session_start();
ini_set("memory_limit","5000M");
ini_set('max_execution_time', 0);
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_sql.php";

$date1=$_POST['utgl1'];
$date2=$_POST['utgl2'];
$tgl1= date("Y-m-d", strtotime($date1));
$tgl2= date("Y-m-d", strtotime($date2));
$ketnya=$_POST["cekhanya"];
    
$sql = "SELECT COA4, NAMA4, brId, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, DATE_FORMAT(tgltrans,'%d/%m/%Y') as tgltrans, DATE_FORMAT(tgltrm,'%d/%m/%Y') as tgltrm, "
        . "nama, nama_kode, nama_cabang, FORMAT(jumlah,2,'de_DE') as jumlah, FORMAT(jumlah1,2,'de_DE') as jumlah1, realisasi1, "
        . "dokterId,nama_dokter, "
        . "FORMAT(cn,2,'de_DE') as cn, "
        . "noslip, aktivitas1 ";
$sql.=" FROM dbmaster.v_br0_all ";
$sql.=" WHERE 1=1 ";
$sql.=" and brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";

$filtipe="Date_format(MODIFDATE, '%Y-%m-%d')";
if ($_POST['utgltipe']=="2") $filtipe="Date_format(tgltrans, '%Y-%m-%d')";
if ($_POST['utgltipe']=="3") $filtipe="Date_format(tgltrm, '%Y-%m-%d')";
if ($_POST['utgltipe']=="4") $filtipe="Date_format(tgl, '%Y-%m-%d')";
$sql.=" and $filtipe between '$tgl1' and '$tgl2' ";
if (!empty($_POST['kodeid'])) $sql.=" and kode='$_POST[kodeid]' ";
if ((int)$ketnya==0) $sql.=" and ifnull(COA4,'')<>'' ";
if ((int)$ketnya==1) $sql.=" and ifnull(COA4,'')='' ";
if ($_SESSION['ADMINKHUSUS']=="Y") {
    $sql .= " AND (kode in (select distinct kodeid from dbmaster.v_coa_wewenang where karyawanId='$_SESSION[IDCARD]') OR user1='$_SESSION[USERID]')";
}

?>
<form method='POST' action='<?PHP echo "?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
            
    <div class='col-md-12 col-sm-12 col-xs-12'>
        <div class='x_panel'>

            <div class='col-sm-4'>
                COA
                <div class="form-group">
                    <select class='form-control input-sm' id="cb_coa" name="cb_coa">
                        <?PHP
                        $divisi = getfieldcnit("select divprodid as lcfields from hrd.br_kode where kodeid='$_POST[kodeid]'");
                        $wewenang = getfieldcnit("select distinct karyawanid as lcfields from dbmaster.coa_wewenang where karyawanid='$_SESSION[IDCARD]'");
                        if ($wewenang=="0") $wewenang="";

                        $query = "select * from dbmaster.v_coa_all where DIVISI='$divisi' AND ifnull(kodeid,'') <> '' ";
                        if (!empty($wewenang)) $query .= " and COA4 in (select distinct COA4 from dbmaster.coa_wewenang where karyawanid='$_SESSION[IDCARD]')";

                        $tampil = mysqli_query($cnit, $query);
                        while ($ir=  mysqli_fetch_array($tampil)) {
                            if ($ir['kodeid']==$_POST['kodeid'])
                                echo "<option value='$ir[COA4]' selected>$ir[NAMA4]</option>";
                            else
                                echo "<option value='$ir[COA4]'>$ir[NAMA4]</option>";
                        }
                        ?>

                    </select>
                </div>
            </div>


            <div class='col-sm-3'>
                <small>&nbsp;</small>
               <div class="form-group">
                   <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Save" onclick='disp_confirm("Simpan ?")'>
               </div>
           </div>
        </div>
    </div>

    <table id='datatable' class='table nowrap table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='7px'>No</th><th><input type="checkbox" id="chkbtnall" value="select" onClick="SelAllCheckBox('chkbtnall', 'chkbox_id[]')"/></th>
                <th width='60px'>COA</th>
                <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th><th>Tgl. Terima</th><th>Keterangan</th>
                <th width='80px'>Yg Membuat</th><th width='100px'>Dokter</th><th width='50px'>Jumlah</th><th width='50px'>Realisasi</th>
                <th width='50px'>Realisasi</th><th>No Slip</th><th>Kode</th>

            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $tampil = mysqli_query($cnit, $sql);
            while ($row=  mysqli_fetch_array($tampil)) {
                $dok="";
                if (!empty($row['dokterId'])) $dok=$row["nama_dokter"]." <small>(".(int)$row['dokterId'].")</small>";

                $link = "";
                $coa = $row["NAMA4"];
                //if (empty($coa))
                    $link = "<input type='checkbox' value='$row[brId]' name='chkbox_id[]' id='chkbox_id[]' class='cekbr'>";

                $brid = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$row['brId'].">".$row["tgl"]."</a>";
                $tglbr = $row["tgl"];
                $tgltrans = "";

                if (!empty($row["tgltrans"]) AND $row["tgltrans"] <>"00/00/0000" AND $row["tgltrans"] <>"00-00-0000" )
                    $tgltrans = $row["tgltrans"];
                $tgltrm = "";
                if (!empty($row["tgltrm"]) AND $row["tgltrm"] <>"00/00/0000" AND $row["tgltrm"] <>"00-00-0000" )
                    $tgltrm = $row["tgltrm"];

                $aktivitas1 = $row["aktivitas1"];
                $nama = "<a href='#' title=".$row['nama_cabang'].">".$row["nama"]."</a>";
                $nnmdok = $dok;
                $jumlah = $row["jumlah"];
                $jumlah1 = $row["jumlah1"];
                $realisasi = $row["realisasi1"];
                $noslip = $row["noslip"];
                $namakode = $row["nama_kode"];


                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>$link</td>";
                echo "<td>$coa</td>";
                echo "<td>$brid</td>";
                echo "<td>$tgltrans</td>";
                echo "<td>$tgltrm</td>";
                echo "<td>$aktivitas1</td>";
                echo "<td>$nama</td>";
                echo "<td>$nnmdok</td>";
                echo "<td>$jumlah</td>";
                echo "<td>$jumlah1</td>";
                echo "<td>$realisasi</td>";
                echo "<td>$noslip</td>";
                echo "<td>$namakode</td>";
                echo "</tr>";

                $no++;
            }
            ?>
        </tbody>
    </table>
</form>
<script>
$(document).ready(function() {

    var table = $('#datatable').DataTable({
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "displayLength": -1,
        "columnDefs": [
            { "contentPadding": "1" },
            { "visible": false },
            { className: "text-right", "targets": [9,10] },//right
            { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

        ],
        bInfo: false, "bLengthChange": false,
		"bPaginate": false,
        "scrollY": 350,
        "scrollX": true
    } );
    $('div.dataTables_filter input', table.table().container()).focus();
} );
</script>


<script>
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
</script>

<script>
    function disp_confirm(pText_)  {
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_isicoa/simpandata.php";
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 13px;
    }
    #datatable td { 
        font-size: 12px;
    }
</style>