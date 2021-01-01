<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $tgl2= date("Y-m-t", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    $idkar=$_POST['ukar'];
    $idajukan=$_POST['ukar'];
    
    $pjabatanid = getfieldcnmy("select jabatanId as lcfields from hrd.karyawan where karyawanId='$idkar'");
    $lvlpengajuan = getfieldcnmy("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
    
    $_SESSION['EKARUC']=$idkar;
    $_SESSION['UCPTGL1']=date("F Y", strtotime($date1));
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_karyawan' name='u_karyawan' value='<?PHP echo $idkar; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_tgl2' name='u_tgl2' value='<?PHP echo $tgl2; ?>' Readonly>
    <input type='hidden' id='e_lvl' name='e_lvl' value='<?PHP echo $lvlpengajuan; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'></th>
                    <th width='50px'>Hari / Tanggal</th>
                    <th width='220px'>Tujuan / Note</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $patasan1="";
                    $patasan2="";
                    $patasan3="";
                    while (strtotime($tgl1) <= strtotime($tgl2)) {
                        $mytgl= date("Ymd", strtotime($tgl1));
                        $ptgl= date("d/m/Y", strtotime($tgl1));
                        $pddat= date("Y-m-d", strtotime($tgl1));
                        $mhari= date("d", strtotime($tgl1));
                        $phari=date("w", strtotime($tgl1));
                        
                        $nmhari=$seminggu[$phari];
                        $ket="";
                        $stl="";
                        if ($phari==0 OR $phari==6)
                            $stl="style='background:#FAEBD7;'";
                        
                        $sql = "SELECT * FROM dbmaster.t_planuc_mkt WHERE karyawanid='$idkar' and DATE_FORMAT(tgl, '%Y%m%d') = '$mytgl' order by tgl";
                        $tampil = mysqli_query($cnmy, $sql);
                        $ketemu = mysqli_num_rows($tampil);
                        if ($ketemu>0) {
                            $t= mysqli_fetch_array($tampil);
                            $nourut=$t['nourut'];
                            $ket=$t['keterangan'];
                            
                            if (!empty($t['atasan1'])) $patasan1=$t['atasan1'];
                            if (!empty($t['atasan2'])) $patasan2=$t['atasan2'];
                            if (!empty($t['atasan3'])) $patasan3=$t['atasan3'];
                            
                        }
                        
                        echo "<tr $stl>";
                        echo "<td>$mhari</td>";
                        echo "<td>$nmhari, $ptgl</td>";
                        echo "<input type='hidden' name='txttgl$no' id='txttgl$no' size='80px' value='$pddat'>";
                        echo "<td><input type='text' name='txtket$no' id='txtket$no' size='80px' value='$ket'></td>";
                        echo "</tr>";
                        $no++;
                        $tgl1 = date ("Y-m-d", strtotime("+1 day", strtotime($tgl1)));
                    }
                ?>
            </tbody>
        </table>

    </div>
    
    <div class='col-md-12 col-sm-12 col-xs-12'>
        <div class='x_panel'>

            <div hidden class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                <div class='col-xs-5'>
                    <input type="button" class='btn btn-warning btn-xs'  name="btn_refresh" id="btn_refresh" onclick='refresh_atasan()' value="Refresh Atasan..">
                </div>
            </div>
			
            <!-- Appove SPV / AM -->
            <div hidden class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                <div class='col-xs-9'>
                    <select class='form-control input-sm' id='e_atasan' name='e_atasan' onchange="ShowAtasanDM('e_atasan')">
                        <?PHP
                            echo "<option value='' selected>-- Pilihan --</option>";
                            if ($lvlpengajuan=="FF1") {
                                $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.divisiid, a.icabangid) in 
                                    (select CONCAT(b.divisiid, b.icabangid) 
                                    from MKT.imr0 b where b.karyawanid='$idajukan')";
                                
                                $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.divisiid, a.icabangid, a.areaid) in 
                                    (select CONCAT(b.divisiid, b.icabangid, b.areaid) 
                                    from MKT.imr0 b where b.karyawanid='$idajukan')";
                                $query .=" order by nama, karyawanId";
                                $tampil = mysqli_query($cnmy, $query);
                                $ketemu = mysqli_num_rows($tampil);
                                while($a=mysqli_fetch_array($tampil)){ 
                                    $sel="";
                                    if ($ketemu==1) { $sel="selected"; $patasan1=$a['karyawanId']; }
                                    if ($a['karyawanId']==$patasan1)
                                        echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                                    else
                                        echo "<option value='$a[karyawanId]'>$a[nama]</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
            
            
            <!-- DM -->
            <div hidden class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                <div class='col-xs-9'>
                    <select class='form-control input-sm' id='e_atasan2' name='e_atasan2' onchange="ShowAtasanSM('e_atasan2')">
                    <?PHP
                    
                        if ($lvlpengajuan=="FF2")
                            $karyawan=$idajukan;
                        else
                            $karyawan=$patasan1;
                        
                        $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatandm as a WHERE CONCAT(a.icabangid) in 
                            (select CONCAT(b.icabangid) 
                            from MKT.ispv0 b where b.karyawanid='$karyawan')";
                        $query .=" order by nama, karyawanId";
                        $tampil = mysqli_query($cnmy, $query);
                        $ketemu = mysqli_num_rows($tampil);
                        echo "<option value='' selected>-- Pilihan --</option>";
                        while($a=mysqli_fetch_array($tampil)){
                            $sel="";
                            if ($ketemu==1) { $sel="selected"; $patasan2=$a['karyawanId']; }
                            
                            if ($a['karyawanId']==$patasan2) 
                                echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                            else
                                echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
                        }
                    ?>
                    </select>
                </div>
            </div>

            <!-- SM -->
            <div hidden class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                <div class='col-xs-9'>
                    <select class='form-control input-sm' id='e_atasan3' name='e_atasan3' onchange="">
                    <?PHP
                    
                        if ($lvlpengajuan=="FF3")
                            $karyawan=$idajukan;
                        else
                            $karyawan=$patasan2;

                        $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatansm as a WHERE CONCAT(a.icabangid) in 
                            (select CONCAT(b.icabangid) 
                            from MKT.idm0 b where b.karyawanid='$karyawan')";
                        $query .=" order by nama, karyawanId";
                        $tampil = mysqli_query($cnmy, $query);
                        $ketemu = mysqli_num_rows($tampil);
                        echo "<option value='' selected>-- Pilihan --</option>";
                        while($a=mysqli_fetch_array($tampil)){
                            $sel="";
                            if ($ketemu==1) $sel="selected";
                            
                            if ($a['karyawanId']==$patasan3) 
                                echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                            else
                                echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
                        }
                    
                    ?>
                    </select>
                </div>
            </div>
            
            
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                <div class='col-xs-5'>
                    <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Save" onclick='disp_confirm("Simpan ?")'>
                </div>
            </div>
                                
        </div>
    </div>
    
</form>

    
<script>
    $(document).ready(function() {
        var dataTable = $('#datatablercbi').DataTable( {
            "stateSave": true,
            "ordering": false,
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2] }//nowrap

            ],
            "language": {
                "zeroRecords": "data KOSONG..."
            },
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
    } );
    
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

<style>
    .divnone {
        display: none;
    }
    #datatablercbi th {
        font-size: 12px;
    }
    #datatablercbi td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<script>
    $(document).ready(function() {
        refresh_atasan();
    });
	
    function disp_confirm(pText_)  {
        var ekar =document.getElementById('u_karyawan').value;
        var elvlpos =document.getElementById('e_lvl').value;
        var eatasan1 =document.getElementById('e_atasan').value;
        var eatasan2 =document.getElementById('e_atasan2').value;
        var eatasan3 =document.getElementById('e_atasan3').value;
    
        elvlpos=elvlpos.trim();
        var rlevel = elvlpos.substring(0, 2);
        
        if (ekar==""){
            alert("yang membuat masih kosong....");
            return 0;
        }

		/*
        if (rlevel=="FF") {
            if (elvlpos=="FF1") {
                if (eatasan1==""){
                    alert("SPV / AM masih kosong....");
                    return 0;
                }
            }

            if (elvlpos=="FF1" || elvlpos=="FF2") {
                if (eatasan2==""){
                    alert("DM masih kosong....");
                    return 0;
                }
            }

            if (elvlpos=="FF1" || elvlpos=="FF2" || elvlpos=="FF3") {
                if (eatasan3==""){
                    alert("SM masih kosong....");
                    return 0;
                }
            }
        }
        */
		
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_mkt_planuc/aksi_planuc.php";
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function ShowAtasanSPV(idkar) {
        var icar = document.getElementById(idkar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdataatasanspv",
            data:"umr="+icar,
            success:function(data){
                $("#e_atasan").html(data);
                ShowAtasanDM('e_atasan');
            }
        });
    }
    
    function ShowAtasanDM(idkar) {
        var icar = document.getElementById(idkar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdataatasandm",
            data:"umr="+icar,
            success:function(data){
                $("#e_atasan2").html(data);
                ShowAtasanSM('e_atasan2');
            }
        });
    }
    
    function ShowAtasanSM(idkar) {
        var icar = document.getElementById(idkar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdataatasansm",
            data:"umr="+icar,
            success:function(data){
                $("#e_atasan3").html(data);
            }
        });
    }
    
    
    function refresh_atasan() {
        var elvlpos =document.getElementById('e_lvl').value;
        elvlpos=elvlpos.trim();
        var rlevel = elvlpos.substring(0, 2);
        
        $("#e_atasan").html("<option value=''>blank_</option");
        $("#e_atasan2").html("<option value=''>blank_</option");
        $("#e_atasan3").html("<option value=''>blank_</option");
        
        if (rlevel=="FF") {
            if (elvlpos=="FF1") {
                ShowAtasanSPV('e_idkaryawan');
            }

            if (elvlpos=="FF2") {
                $("#e_atasan").html("<option value=''>blank_</option");
                ShowAtasanDM('e_idkaryawan');
            }

            if (elvlpos=="FF3") {
                $("#e_atasan").html("<option value=''>blank_</option");
                $("#e_atasan2").html("<option value=''>blank_</option");
                ShowAtasanSM('e_idkaryawan');
            }

        }
    }
</script>