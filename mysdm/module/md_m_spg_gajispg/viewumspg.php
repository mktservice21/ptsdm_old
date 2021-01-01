<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Y-m", strtotime($date1));
    
    $_SESSION['SPGMSTGJTGLCAB']=date("F Y", strtotime($date1));
    
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <?PHP
            $fin_chkall = "<input type='checkbox' id='chkbtnbr_um' value='select' onClick=\"SelAllCheckBoxUM('chkbtnbr_um', 'chkbox_brum[]')\" />";
        ?>
        <table id='datatablezonajbt' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>NO</th>
                    <th width='10px'><?PHP //echo $fin_chkall; ?></th>
                    <th align="center">JABATAN</th>
                    <th width='300px' align="center" nowrap>ZONA</th>
                    <th width='200px' align="center" nowrap>U. Makan</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select tbl.id_zona, tbl.nama_zona, tbl.jabatid, tbl.nama_jabatan, tbl.jabatanid, tgj.umakan from (
                    select a.id_zona, a.nama_zona, b.jabatid, b.nama_jabatan, b.jabatanid from 
                    dbmaster.t_zona a, dbmaster.t_spg_jabatan b 
                    ) as tbl
                    LEFT JOIN (SELECT id_zona, jabatid, umakan from dbmaster.t_spg_gaji_zona_jabatan c 
                    WHERE DATE_FORMAT(bulan,'%Y-%m') = (select MAX(DATE_FORMAT(bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_zona_jabatan d WHERE c.id_zona=d.id_zona AND c.jabatid=d.jabatid)
                    ) as tgj
                    on tbl.id_zona=tgj.id_zona AND tbl.jabatid=tgj.jabatid 
                    ORDER BY tbl.id_zona, tbl.jabatanid";
                $tampil = mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idzona=$row['id_zona'];
                    $nmzona=$row['nama_zona'];
                    $idjabatan=$row['jabatanid'];
                    $idjbt=$row['jabatid'];
                    $nmjabatan=$row['nama_jabatan'];
                    $pjumlah=$row['umakan'];
                    
                    $n_id="$idzona"."_"."$idjbt";
                    $fin_cekbox = "<input type=checkbox value='$n_id' id='chkbox_brum[]' name='chkbox_brum[]'>";
                    $finrp_id="<input type='hidden' size='8px' id='txt_idbr[]' name='txt_idbr[]' class='input-sm' autocomplete='off' value='$n_id'>";
                    $finrp_um="<input type='text' size='8px' id='txtrp_um[]' name='txtrp_um[]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjumlah'>";
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$finrp_id</td>";//$fin_cekbox
                    echo "<td>$idjabatan - $nmjabatan</td>";
                    echo "<td>$nmzona</td>";
                    echo "<td align='right'>$finrp_um</td>";
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
                                    <button type='button' class='btn btn-info btn-sm' onclick='disp_confirm_um("simpan", "chkbox_brum[]")'>Simpan</button>
                                    <button type='button' class='btn btn-danger btn-sm' id="btnhapus" name="btnhapus" onclick='disp_confirm_um("hapus", "chkbox_brum[]")'>Hapus</button>
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
        
        var table = $('#datatablezonajbt').DataTable({
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
    
    function disp_confirm_um(ket, cekbr){
        var allnobr="";
        /*
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
            
            //jml_inc = jml_inc.substring(0, lastIndex);
            
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        */
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
            document.getElementById("demo-form2").action = "module/md_m_spg_gajispg/aksi_spggajimaster.php?module="+module+"&act="+m_act+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            return 1;
        }
        
        
        
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablezonajbt th {
        font-size: 12px;
    }
    #datatablezonajbt td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>