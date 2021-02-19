<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    //include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    
    $query = "select * from dbmaster.t_email WHERE id=3";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $psubject=$row['tsubject'];
    $pnmpengirim=$row['nama_from'];
    $pemailpengirim=$row['email_from'];
    $pemailcc1=$row['cc1'];
    $pemailcc2=$row['cc2'];
    $pemailcc3=$row['cc3'];
    $pemailcc4=$row['cc4'];
    $pemailcc5=$row['cc5'];
    
    
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form5' name='form5' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <table id='datatablesetemail1' class='datatable table nowrap table-striped table-bordered' width="100%">
            <tr>
                <td>Subject</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_subject' name='txt_subject' class='input-sm' autocomplete='off' value='<?PHP echo $psubject; ?>'></td>
            </tr>
            
            <tr>
                <td>Pengirim</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_nmpengirim' name='txt_nmpengirim' class='input-sm' autocomplete='off' value='<?PHP echo $pnmpengirim; ?>'></td>
            </tr>
            
            <tr>
                <td>Email Pengirim</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_emailpengirim' name='txt_emailpengirim' class='input-sm' autocomplete='off' value='<?PHP echo $pemailpengirim; ?>'></td>
            </tr>
            
            <tr>
                <td>Email CC1</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_emailcc1' name='txt_emailcc1' class='input-sm' autocomplete='off' value='<?PHP echo $pemailcc1; ?>'></td>
            </tr>
            
            <tr>
                <td>Email CC2</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_emailcc2' name='txt_emailcc2' class='input-sm' autocomplete='off' value='<?PHP echo $pemailcc2; ?>'></td>
            </tr>
            
            <tr>
                <td>Email CC3</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_emailcc3' name='txt_emailcc3' class='input-sm' autocomplete='off' value='<?PHP echo $pemailcc3; ?>'></td>
            </tr>
            
            <tr>
                <td>Email CC4</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_emailcc4' name='txt_emailcc4' class='input-sm' autocomplete='off' value='<?PHP echo $pemailcc4; ?>'></td>
            </tr>
            
            <tr>
                <td>Email CC5</td>
                <td>:</td>
                <td><input type='text' size='35px' id='txt_emailcc5' name='txt_emailcc5' class='input-sm' autocomplete='off' value='<?PHP echo $pemailcc5; ?>'></td>
            </tr>
            
            
        </table>
        <?PHP
            $fin_chkall = "<input type='checkbox' id='chkbtnbr_um' value='select' onClick=\"SelAllCheckBoxUM('chkbtnbr_um', 'chkbox_cab[]')\" />";
        ?>
        <table id='datatablesetemail' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>NO</th>
                    <th width='10px'><?PHP //echo $fin_chkall; ?></th>
                    <th width='50px' align="center">ID CABANG</th>
                    <th width='300px' align="center" nowrap>CABANG</th>
                    <th width='200px' align="center" nowrap>EMAIL</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select a.id, a.icabangid_o, b.nama nama_cabang, a.ckirim1 from dbmaster.t_email_cabang_otc a LEFT JOIN mkt.icabang_o b on 
                    a.icabangid_o=b.icabangid_o order by b.nama";
                $tampil = mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pid=$row['id'];
                    $pidcab=$row['icabangid_o'];
                    $pnmcab=$row['nama_cabang'];
                    $pemail1=$row['ckirim1'];
					
                    if ($pidcab=="JKT_RETAIL") $pnmcab="JAKARTA RETAIL";
                    elseif ($pidcab=="JKT_MT") $pnmcab="JAKARTA MT";
                    
                    $fin_cekbox = "<div hidden><input type=checkbox value='$pidcab' id='chkbox_cab[]' name='chkbox_cab[]' checked></div>";
                    $fin_idcab="<input type='hidden' size='8px' id='txt_idcab[]' name='txt_idcab[]' class='input-sm' autocomplete='off' value='$pidcab'>";
                    $fin_email="<input type='text' size='35px' id='txt_email[$pidcab]' name='txt_email[$pidcab]' class='input-sm' autocomplete='off' value='$pemail1'>";
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$fin_idcab $fin_cekbox</td>";//$fin_cekbox
                    echo "<td>$pidcab</td>";
                    echo "<td nowrap>$pnmcab</td>";
                    echo "<td nowrap>$fin_email</td>";
                    echo "</tr>";

                    $no++;
                }

            ?>
            </tbody>
        </table>    
        
    </div>
    
    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div id="div_jumlah">
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                       
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                            <div class='col-xs-9'>
                                <div class="checkbox">
                                    <button type='button' class='btn btn-info btn-sm' onclick='disp_confirm_email("simpan", "chkbox_cab[]")'>Simpan</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</form>
<script>
    $(document).ready(function() {
        
        var table = $('#datatablesetemail').DataTable({
            fixedHeader: true,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
            
    } );
    
    function SelAllCheckBoxUM(nmbuton, data){
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
    
    function disp_confirm_email(ket, cekbr){
        var allnobr="";
        var m_act="simpanum";
        if (ket=="simpan") {
            var cmt = confirm('Apakah akan melakukan '+ket+' ...?');
        }else if (ket=="hapus") {
            var cmt = confirm('Apakah akan melakukan hapus ...?');
            m_act="hapusum";
        }else{
            var cmt = confirm('Apakah akan melakukan proses ...?');
        }
        if (cmt == false) {
            return false;
        }else{
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            //document.write("You pressed OK!")
            document.getElementById("demo-form5").action = "module/md_m_spg_gajispg/aksi_spgemail.php?module="+module+"&act="+m_act+"&idmenu="+idmenu;
            document.getElementById("demo-form5").submit();
            return 1;
        }
        
        
        
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablesetemail, #datatablesetemail1 th {
        font-size: 12px;
    }
    #datatablesetemail td, #datatablesetemail1 td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>