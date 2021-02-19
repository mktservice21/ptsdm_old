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
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Y-m", strtotime($date1));
    
    $_SESSION['SPGMSTGJTGLCAB']=date("F Y", strtotime($date1));
    
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form3' name='form3' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <?PHP
            $fin_chkall = "<input type='checkbox' id='chkbtnbr_tj' value='select' onClick=\"SelAllCheckBoxTJ('chkbtnbr_tj', 'chkbox_brum[]')\" />";
        ?>
        <table id='datatablejbt' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>NO</th>
                    <th width='10px'><?PHP //echo $fin_chkall; ?></th>
                    <th width='300px' align="center">JABATAN</th>
                    <th width='150px' align="center" nowrap>SEWA KENDARAAN</th>
                    <th width='150px' align="center" nowrap>PULSA</th>
                    <th width='150px' align="center" nowrap>BBM</th>
                    <th width='150px' align="center" nowrap>PARKIR DAN TOL</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select a.jabatid, a.nama_jabatan, a.jabatanid, tgj.sewakendaraan, tgj.pulsa, tgj.bbm, tgj.parkir from 
                    dbmaster.t_spg_jabatan a
                    LEFT JOIN (SELECT jabatid, sewakendaraan, pulsa, bbm, parkir from dbmaster.t_spg_gaji_jabatan c 
                    WHERE DATE_FORMAT(bulan,'%Y-%m') = (select MAX(DATE_FORMAT(bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_jabatan d WHERE c.jabatid=d.jabatid)
                    ) as tgj
                    on a.jabatid=tgj.jabatid 
                    ORDER BY a.jabatanid";
                
                $tampil = mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idjabatan=$row['jabatanid'];
                    $idjbt=$row['jabatid'];
                    $nmjabatan=$row['nama_jabatan'];
                    $pjmlsw=$row['sewakendaraan'];
                    $pjmlpulsa=$row['pulsa'];
                    $pjmlbbm=$row['bbm'];
                    $pjmlparkir=$row['parkir'];
                    
                    $fin_cekbox = "<input type=checkbox value='$idjbt' id='chkbox_brum[]' name='chkbox_brum[]'>";
                    $finrp_id="<input type='hidden' size='8px' id='txt_idbr[]' name='txt_idbr[]' class='input-sm' autocomplete='off' value='$idjbt'>";
                    $finrp_sw="<input type='text' size='8px' id='txtrp_sw[]' name='txtrp_sw[]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlsw'>";
                    $finrp_pulsa="<input type='text' size='8px' id='txtrp_pulsa[]' name='txtrp_pulsa[]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlpulsa'>";
                    $finrp_bbm="<input type='text' size='8px' id='txtrp_bbm[]' name='txtrp_bbm[]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlbbm'>";
                    $finrp_parkir="<input type='text' size='8px' id='txtrp_parkir[]' name='txtrp_parkir[]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlparkir'>";
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$finrp_id</td>";//$fin_cekbox
                    echo "<td nowrap>$idjabatan - $nmjabatan</td>";
                    echo "<td align='right'>$finrp_sw</td>";
                    echo "<td align='right'>$finrp_pulsa</td>";
                    echo "<td align='right'>$finrp_bbm</td>";
                    echo "<td align='right'>$finrp_parkir</td>";
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
                                    <button type='button' class='btn btn-info btn-sm' onclick='disp_confirm_tj("simpan", "chkbox_brum[]")'>Simpan</button>
                                    <button type='button' class='btn btn-danger btn-sm' id="btnhapus" name="btnhapus" onclick='disp_confirm_tj("hapus", "chkbox_brum[]")'>Hapus</button>
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
        
        var table = $('#datatablejbt').DataTable({
            fixedHeader: true,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
            
    } );
    
    function SelAllCheckBoxTJ(nmbuton, data){
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
    
    function disp_confirm_tj(ket, cekbr){
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
        var m_act="simpantunjangan";
        if (ket=="simpan") {
            var cmt = confirm('Apakah akan melakukan '+ket+' ...?');
        }else if (ket=="hapus") {
            var cmt = confirm('Apakah akan melakukan hapus ...?');
            m_act="hapustunjangan";
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
            document.getElementById("demo-form3").action = "module/md_m_spg_gajispg/aksi_spggajimaster.php?module="+module+"&act="+m_act+"&idmenu="+idmenu;
            document.getElementById("demo-form3").submit();
            return 1;
        }
        
        
        
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablejbt th {
        font-size: 12px;
    }
    #datatablejbt td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>