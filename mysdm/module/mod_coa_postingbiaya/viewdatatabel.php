<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $_SESSION['COABIAYADIV']=$_POST['udivisi'];
    $divisi=$_POST['udivisi'];

?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_divisi' name='u_divisi' value='<?PHP echo $divisi; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='15px'><!--<input type="checkbox" id="chkbtnall" value="select" onClick="SelAllCheckBox('chkbtnall', 'chkbox_id[]')"/>--></th>
                    <th width='60px'>Keterangan</th>
                    <th width='40px'>Kode</th>
                    <th width='80px'>Akun</th>
                    <th width='50px'>COA</th>
                    <th width='100px'>Nama</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $awal = 1;
                    $lewat = 0;
                    $sql = "SELECT kode, nobrid, nama FROM dbmaster.t_brid WHERE aktif='Y' order by kode, nobrid asc";
                    $tampil = mysqli_query($cnmy, $sql);
                    while ($t= mysqli_fetch_array($tampil)) {
                        $nobrid=$t['nobrid'];
                        $nmakun=$t['nama'];
                        $ket = "";
                        if ($t['kode']==1) $ket = "Biaya Rutin";
                        if ($t['kode']==2) $ket = "Biaya Luar Kota";
                        if ($t['kode']==3) $ket = "Cash Advance";
                        if ($t['kode']==4) $ket = "Sewa";
                        if ($t['kode']==5) $ket = "Service";
                        
                        //$link="<a class='btn btn-default btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_POST[idmenu]&nmun=$_POST[idmenu]&id=$nobrid&divisi=$divisi'>Edit</a>";
                        $link="<input type='checkbox' value='$nobrid' name='chkbox_id[]' id='chkbox_id[]' class='cekbr'>";
                        $query = "select COA4, NAMA4 from dbmaster.v_posting_coa_rutin where nobrid='$nobrid' and divisi='$divisi'";
                        $tampilcoa= mysqli_query($cnmy, $query);
                        $c= mysqli_fetch_array($tampilcoa);
                        $coa=$c['COA4'];
                        $coanama=$c['NAMA4'];
                        
                        $lewat = $t['kode'];
                        if ($awal<>$lewat) {
                            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                            $awal = $t['kode'];
                        }
                        
                        echo "<tr>";
                        echo "<td>$link</td>";
                        echo "<td><b>$ket</b></td>";
                        echo "<td>$nobrid</td>";
                        echo "<td>$nmakun</td>";
                        echo "<td>$coa</td>";
                        echo "<td>$coanama</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>

    </div>
    
    <div class='col-md-12 col-sm-12 col-xs-12'>
        <div class='x_panel'>

            <div class='col-sm-4'>
                COA
                <div class="form-group">
                    <select class='form-control input-sm' id="cb_coa" name="cb_coa">
                        <?PHP
                        $fildiv="";
                        if (!empty($divisi)) $fildiv=" AND DIVISI='$divisi' ";
                        $query = "select * from dbmaster.v_coa_all where 1=1 $fildiv ";

                        $tampil = mysqli_query($cnmy, $query);
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
    
</form>

    
<script>
    $(document).ready(function() {
        var dataTable = $('#datatablercbi').DataTable( {
            fixedHeader: true,
            "stateSave": true,
            "ordering": false,
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
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
    function disp_confirm(pText_)  {
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_coa_postingbiaya/aksi_postingcoabiaya.php";
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>