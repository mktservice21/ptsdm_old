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
    
    $pcabangid=$_POST['ucab'];
    
    $_SESSION['SPGMSTGJTGLCAB']=date("F Y", strtotime($date1));
    
    
    $n_idzona[]="";
    $n_nmzona[]="";
    $query = "select id_zona, nama_zona from dbmaster.t_zona order BY id_zona";
    $tampilar = mysqli_query($cnmy, $query);
    while ($nar= mysqli_fetch_array($tampilar)) {
        $n_idzona[]=$nar['id_zona'];
        $n_nmzona[]=$nar['nama_zona'];
    }
    $jmldata_ar=count($n_idzona);
    
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <?PHP
            $fin_chkall = "<input type='checkbox' id='chkbtnbr_gp' value='select' onClick=\"SelAllCheckBoxGP('chkbtnbr_gp', 'chkbox_brgp[]')\" />";
        ?>
        <table id='datatableareazona' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>NO</th>
                    <th width='10px'><?PHP //echo $fin_chkall; ?></th>
                    <th align="center">AREA</th>
                    <th width='100px' align="center" nowrap>ZONA</th>
                    <th width='200px' align="center" nowrap>GAJI POKOK</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select a.icabangid_o, a.areaid_o, a.nama, a.aktif, gp.id_zona, zn.nama_zona, gp.gaji from 
                    mkt.iarea_o a
                    LEFT JOIN (SELECT icabangid, areaid, id_zona, gaji from dbmaster.t_spg_gaji_area_zona c 
                    WHERE DATE_FORMAT(bulan,'%Y-%m') = (select MAX(DATE_FORMAT(bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_area_zona d WHERE 
                    c.icabangid=d.icabangid AND c.areaid=d.areaid AND c.id_zona=d.id_zona)
                    ) as gp
                    on a.areaid_o=gp.areaid AND a.icabangid_o=gp.icabangid 
                    LEFT JOIN dbmaster.t_zona zn on gp.id_zona=zn.id_zona
                    WHERE a.icabangid_o='$pcabangid' AND IFNULL(a.aktif,'')='Y'
                    ORDER BY a.nama";
                $tampil = mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idzona=$row['id_zona'];
                    $nmzona=$row['nama_zona'];
                    $pidcab=$row['icabangid_o'];
                    $pidarea=$row['areaid_o'];
                    $pnmarea=$row['nama'];
                    $pjmlgp=$row['gaji'];
                    
                    $n_id="$pidcab"."_"."$pidarea"."_"."$idzona";
                    $fin_cekbox = "<input type=checkbox value='$n_id' id='chkbox_brgp[]' name='chkbox_brgp[]'>";
                    $finrp_id="<input type='hidden' size='8px' id='txt_idbr[]' name='txt_idbr[]' class='input-sm' autocomplete='off' value='$n_id'>";
                    
                    $fin_idcab="<input type='hidden' size='8px' id='txt_idcab[]' name='txt_idcab[]' class='input-sm' autocomplete='off' value='$pidcab'>";
                    $fin_idarea="<input type='hidden' size='8px' id='txt_idarea[]' name='txt_idarea[]' class='input-sm' autocomplete='off' value='$pidarea'>";
                    $fin_idzona="<input type='hidden' size='8px' id='txt_idzona[]' name='txt_idzona[]' class='input-sm' autocomplete='off' value='$idzona'>";
                    $finrp_gp="<input type='text' size='8px' id='txtrp_gp[]' name='txtrp_gp[]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlgp'>";
                    
                    $zn_sel0="selected";
                    $zn_sel1="";
                    $zn_sel2="";
                    if ($idzona=="1") $zn_sel1="selected";
                    if ($idzona=="2") $zn_sel2="selected";
                    
                    $pcb_zona="<select class='input-sm' id='cb_zona[]' name='cb_zona[]'>"
                            . "<option value='' $zn_sel0>--Pilih--</option>"
                            . "<option value='1' $zn_sel1>ZONA 1</option>"
                            . "<option value='2' $zn_sel2>ZONA 2</option>"
                            . "</select>";
                    
                    //ZONA
                    $data_zon_sel="<option value='' selected>--Pilih--</option>";
                    if ((double)$jmldata_ar>0) {
                        $xa=0;
                        for($xa=0;$xa<=$jmldata_ar;$xa++) {
                            if (isset($n_idzona[$xa]) AND isset($n_nmzona[$xa])) {
                                if (!empty(trim($n_idzona[$xa])) AND !empty(trim($n_nmzona[$xa]))) {
                                    $n_sel_zona="";
                                    if ($n_idzona[$xa]==$idzona) $n_sel_zona="selected";
                                    $data_zon_sel .="<option value='$n_idzona[$xa]' $n_sel_zona>$n_nmzona[$xa]</option>";
                                }
                            }
                        }
                    }
                    $pcb_zona="<select class='input-sm' id='cb_zona[]' name='cb_zona[]' >$data_zon_sel</select>";
                    //END ZONA
    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$finrp_id $fin_idcab $fin_idarea $fin_idzona</td>";//$fin_cekbox
                    echo "<td>$pnmarea</td>";
                    echo "<td>$pcb_zona</td>";
                    echo "<td align='right'>$finrp_gp</td>";
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
                                    <button type='button' class='btn btn-info btn-sm' onclick='disp_confirm_gp("simpan", "chkbox_brgp[]")'>Simpan</button>
                                    <button type='button' class='btn btn-danger btn-sm' id="btnhapus" name="btnhapus" onclick='disp_confirm_gp("hapus", "chkbox_brgp[]")'>Hapus</button>
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
        
        var table = $('#datatableareazona').DataTable({
            fixedHeader: true,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
            
    } );
    
    function SelAllCheckBoxGP(nmbuton, data){
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
    
    function disp_confirm_gp(ket, cekbr){
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
        var m_act="simpangp";
        if (ket=="simpan") {
            var cmt = confirm('Apakah akan melakukan '+ket+' ...?');
        }else if (ket=="hapus") {
            var cmt = confirm('Apakah akan melakukan hapus ...?');
            m_act="hapusgp";
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
            document.getElementById("demo-form4").action = "module/md_m_spg_gajispg/aksi_spggajimaster.php?module="+module+"&act="+m_act+"&idmenu="+idmenu;
            document.getElementById("demo-form4").submit();
            return 1;
        }
        
        
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableareazona th {
        font-size: 12px;
    }
    #datatableareazona td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>