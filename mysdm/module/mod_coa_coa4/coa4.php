<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
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
<script>
$(document).ready(function() {
    var table = $('#datatable').DataTable({
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "displayLength": 10
    } );

} );
function disp_confirm(pText_,ket)  {
    
    var elevel =document.getElementById('cb_level').value;
    var eid =document.getElementById('e_id').value;
    var enama =document.getElementById('e_nmcoa').value;
    
    if (elevel==""){
        alert("Level 3 masih kosong....");
        return 0;
    }
    if (eid==""){
        alert("coa kode masih kosong....");
        document.getElementById('e_id').focus();
        return 0;
    }
    if (enama==""){
        alert("coa nama masih kosong....");
        document.getElementById('e_nmcoa').focus();
        return 0;
    }
  
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            var emodule =document.getElementById('u_module').value;
            var eact =document.getElementById('u_act').value;
            var eidmenu =document.getElementById('u_idmenu').value;
            
            if (ket=="update") {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_coa_coa4/aksi_coa4.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }else{
                $.ajax({
                    type:"post",
                    url:"module/mod_coa_coa4/aksi_coa4.php?module=carikodesama",
                    data:"ukode="+eid,
                    success:function(data){
                        var edata =data;
                        
                        if (edata=="") {
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_coa_coa4/aksi_coa4.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            alert("kode sudah ada...");
                        }
                    }
                });
            }
            
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}


    function showLevel2(level1, level2){
        var elvel1 = document.getElementById(level1).value;
        $.ajax({
            type:"post",
            url:"module/mod_coa_coa4/viewdata.php?module=viewdatalevel2",
            data:"ulevel1="+elvel1+"&ulevel2="+level2,
            success:function(data){
                $("#"+level2).html(data);
            }
        });
    }

    function showLevel3(level2, level3){
        var elvel2 = document.getElementById(level2).value;
        $.ajax({
            type:"post",
            url:"module/mod_coa_coa4/viewdata.php?module=viewdatalevel3",
            data:"ulevel2="+elvel2+"&ulevel3="+level3,
            success:function(data){
                $("#"+level3).html(data);
                showPosting(elvel2);
            }
        });
    }

    function showPosting(level2){
        $.ajax({
            type:"post",
            url:"module/mod_coa_coa4/viewdata.php?module=viewdataposting",
            data:"ulevel2="+level2,
            success:function(data){
                $("#cb_kode").html(data);
            }
        });
    }
    
    function showSubPosting(kode, subkode){
        var ekode = document.getElementById(kode).value;
        $.ajax({
            type:"post",
            url:"module/mod_coa_coa4/viewdata.php?module=viewdatasubpos",
            data:"ukode="+ekode+"&usub="+subkode,
            success:function(data){
                $("#"+subkode).html(data);
            }
        });
    }
</script>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Data COA Level 4</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //include "config/koneksimysqli_it.php";
        $aksi="module/mod_coa_coa4/aksi_coa4.php";
        switch($_GET['act']){
            default:
                
                
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru';\">";
                            if ($_SESSION['MOBILE']=="N")
                                echo "<a class='btn btn-default' href='eksekusi.php?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru' target='_blank'>Lihat Data COA</a>";
                        
                        echo "<small></small>
                            </h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='250px'>Level 3</th><th width='80px'>Kode</th><th>Nama</th>"
                            . "<th width='40px'>Gol</th><th width='40px'>Aktif</th>"
                            . "<th width='40px'>Posting</th><th width='80px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            
                            $query = "SELECT c1.*, c2.*, c3.*, c4.* FROM dbmaster.coa_level4 as c4 "
                                    . "left join dbmaster.coa_level3 as c3 on c4.COA3=c3.COA3 "
                                    . "left join dbmaster.coa_level2 as c2 on c3.COA2=c2.COA2 "
                                    . "left join dbmaster.coa_level1 as c1 on c2.COA1=c1.COA1 order by c1.COA1, c2.COA2, c3.COA3, c4.COA4";
                            
                            $filekhusus="";
                            if ($_SESSION['ADMINKHUSUS']=="Y") {
                                if (!empty($_SESSION['KHUSUSSEL']))
                                    $filekhusus=" and (DIVISI in $_SESSION[KHUSUSSEL] or ifnull(DIVISI, '')='') ";
                            }
                            $query="select * from dbmaster.v_coa_all WHERE 1=1 $filekhusus order by COA1, COA2, COA3, COA4";
                            
                            $tampil = mysqli_query($cnmy, $query);
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[COA3] - $r[NAMA3]</td>";
                                echo "<td>$r[COA4]</td>";
                                echo "<td><a href='#' title='$r[DESKRIPSI4]'>$r[NAMA4]</a></td>";
                                echo "<td>$r[GOL4]</td>";
                                echo "<td>$r[AKTIF4]</td>";
                                if ($r['DIVISI2']=="OTC") {
                                    $sub="";
                                    $kodes="";
                                    //if (!empty($r['subpost'])) $sub="$r[nmsubpost]";
                                    //if (!empty($r['kodeid'])) $kodes=" - $r[nama_kode]";
                                    $kode=""; $namakode="";
                                    if (!empty($r['subpost']) AND empty($r['nmsubpost'])) {
                                        $kode=$r['subpost'];
                                        $namakode=  getfieldit("select nmsubpost as lcfields from hrd.brkd_otc where subpost='$kode'");
                                    }elseif (!empty($r['nmsubpost'])) {
                                        $kode=$r['subpost'];
                                        $namakode=$r['nmsubpost'];
                                    }else{
                                        $kode=$r['kodeid'];
                                        $namakode=$r['nama_kodeotc'];
                                    }
                                    
                                    $namaall="";
                                    if (!empty($kode) AND !empty($namakode))
                                        $namaall=$kode." - ".$namakode;
                                    
                                    echo "<td><a href='#' title='$r[kodeid] - $r[divprodid]'>$namaall</a></td>";
                                }else
                                    echo "<td><a href='#' title='$r[kodeid] - $r[divprodid]'>$r[nama_kode]</a></td>";
                                
                                echo "<td>";//AKSI
                                    echo "<a class='btn btn-success btn-xs' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[COA4]>Edit</a>";
                                    if ($_SESSION['LEVELUSER']=="admin"){
                                        echo " <a class='btn btn-danger btn-xs' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[COA4]&idmenu=$_GET[idmenu]\"
                                            onClick=\"return confirm('Apakah Anda melakukan proses?')\">Aktif</a>";
                                    }
                                    
                                echo "</td>";
                                echo "</tr>";
                                $no++;
                            }
                            echo "</tbody>";
                            echo "</table>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

            case "tambahbaru":
                ?>
                    
                    <script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
                <?PHP
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <small>Tambah Baru</small></h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'><br/>";
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>";
                        
                        //selalu ada
                        echo "<input type='hidden' id='u_module' name='u_module' value='$_GET[module]' Readonly>
                            <input type='hidden' id='u_idmenu' name='u_idmenu' value='$_GET[idmenu]' Readonly>
                            <input type='hidden' id='u_act' name='u_act' value='input' Readonly>";
                        //selalu ada
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 1 <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_level1' name='cb_level1' onchange=\"showLevel2('cb_level1', 'cb_level2')\">";
                            echo "<option value=''>-- Pilih --</option>";
                            $tampil=mysqli_query($cnmy, "SELECT COA1, NAMA1 FROM dbmaster.coa_level1 order by COA1");
                            while($a=mysqli_fetch_array($tampil)){
                                echo "<option value='$a[COA1]'>$a[COA1] - $a[NAMA1]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 2 <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_level2' name='cb_level2' onchange=\"showLevel3('cb_level2', 'cb_level')\">";
                            echo "<option value=''>-- Pilih --</option>";
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 3 <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_level' name='cb_level'>";
                            echo "<option value=''>-- Pilih --</option>";
                            $tampil=mysqli_query($cnmy, "SELECT COA3, NAMA3 FROM dbmaster.coa_level3 order by COA3");
                            while($a=mysqli_fetch_array($tampil)){
                                echo "<option value='$a[COA3]'>$a[COA3] - $a[NAMA3]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>COA KODE <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_id' name='id' required='required' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask=\"'mask' : '***-**-***'\">
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>NAMA <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmcoa' name='e_nmcoa' required='required' class='form-control col-md-7 col-xs-12'>
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>DESKRIPSI <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_desk' name='e_desk' class='form-control col-md-7 col-xs-12'>
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Golongan <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <div class='btn-group' data-toggle='buttons'>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol1' value='A'> A </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol2' value='B'> B </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol3' value='H'> H </label>
                                </div>
                            </div>";
                        echo "</div>";

						
                        $nhiden="";
                        if ($_SESSION['GROUP']==22 OR $_SESSION['GROUP']==34) $nhiden="hidden";
						
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_kode'>Posting (kode) <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_kode' name='cb_kode'>";
                            echo "<option value=''>-- Pilih --</option>";
                            
                            $tampil=mysqli_query($cnmy, "SELECT kodeid, nama, divprodid FROM dbmaster.br_kode order by divprodid, kodeid");
                            
                            while($a=mysqli_fetch_array($tampil)){
								$pkodedivisi=$a['divprodid'];
                                echo "<option value='$a[kodeid]'>$a[kodeid] - $a[nama] ($pkodedivisi)</option>";
                            }
                            
                            
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='button' class='btn btn-success' onclick=\"disp_confirm('Simpan ?', 'simpan')\">Save</button>
                            </div>";
                        echo "</div>";
                        

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";
            break;

            case "editdata":
                
                
                ?> <script> window.onload = function() { document.getElementById("e_nmcoa").focus(); } </script> <?PHP
                /*
                $edit=mysqli_query($cnmy, "SELECT c4.*, c3.COA2, c2.COA1, c2.DIVISI2 FROM dbmaster.coa_level4 as c4 "
                        . " left join dbmaster.coa_level3 as c3 on c4.COA3=c3.COA3 "
                        . " left join dbmaster.coa_level2 as c2 on c3.COA2=c2.COA2 "
                        . " WHERE c4.COA4='$_GET[id]'");
                 * 
                 */
                $edit=mysqli_query($cnmy, "SELECT * from dbmaster.v_coa WHERE COA4='$_GET[id]'");
                
                $r=mysqli_fetch_array($edit);
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <small>Edit Data</small></h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'><br/>";
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>
                            ";
                        
                        //selalu ada
                        echo "<input type='hidden' id='u_module' name='u_module' value='$_GET[module]' Readonly>
                            <input type='hidden' id='u_idmenu' name='u_idmenu' value='$_GET[idmenu]' Readonly>
                            <input type='hidden' id='u_act' name='u_act' value='update' Readonly>";
                        //selalu ada
                ?>
                <style>
                    .aread {
                        pointer-events: none;
                    }
                </style>
                <?PHP
                //readonly
                $rd="aread";
                if ($_SESSION['LEVELUSER']=="admin") $rd="";
                if ($_SESSION['GROUP']==22 OR $_SESSION['GROUP']==34) $rd="";
                echo "<div class='$rd'>";
                
                
                
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 1 <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_level1' name='cb_level1' onchange=\"showLevel2('cb_level1', 'cb_level2')\">";
                            echo "<option value=''>-- Pilih --</option>";
                            $tampil=mysqli_query($cnmy, "SELECT COA1, NAMA1 FROM dbmaster.coa_level1 order by COA1");
                            while($a=mysqli_fetch_array($tampil)){
                                if ($a['COA1']==$r['COA1'])
                                    echo "<option value='$a[COA1]' selected>$a[COA1] - $a[NAMA1]</option>";
                                else
                                    echo "<option value='$a[COA1]'>$a[COA1] - $a[NAMA1]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 2 <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_level2' name='cb_level2' onchange=\"showLevel3('cb_level2', 'cb_level')\">";
                            echo "<option value=''>-- Pilih --</option>";
                            $tampil=mysqli_query($cnmy, "SELECT COA2, NAMA2 FROM dbmaster.coa_level2 where COA1='$r[COA1]' order by COA2");
                            while($a=mysqli_fetch_array($tampil)){
                                if ($a['COA2']==$r['COA2'])
                                    echo "<option value='$a[COA2]' selected>$a[COA2] - $a[NAMA2]</option>";
                                else
                                    echo "<option value='$a[COA2]'>$a[COA2] - $a[NAMA2]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>LEVEL 3 <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_level' name='cb_level'>";
                            echo "<option value=''>-- Pilih --</option>";
                            $tampil=mysqli_query($cnmy, "SELECT COA3, NAMA3 FROM dbmaster.coa_level3 where COA2='$r[COA2]' order by COA3");
                            while($a=mysqli_fetch_array($tampil)){
                                if ($a['COA3']==$r['COA3'])
                                    echo "<option value='$a[COA3]' selected>$a[COA3] - $a[NAMA3]</option>";
                                else
                                    echo "<option value='$a[COA3]'>$a[COA3] - $a[NAMA3]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";

                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>COA KODE <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_id' name='id' required='required' class='form-control col-md-7 col-xs-12' value='$r[COA4]' readonly>
                            </div>";
                        echo "</div>";
                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>NAMA <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmcoa' name='e_nmcoa' required='required' class='form-control col-md-7 col-xs-12' value='$r[NAMA4]'>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>DESKRIPSI <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_desk' name='e_desk' class='form-control col-md-7 col-xs-12' value='$r[DESKRIPSI4]'>
                            </div>";
                        echo "</div>";
                        
                        
                        $chk1="";
                        $chk2="";
                        $chk3="";
                        if ($r['GOL4']=="A") $chk1="checked";
                        elseif ($r['GOL4']=="B") $chk2="checked";
                        elseif ($r['GOL4']=="H") $chk3="checked";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Golongan <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                <div class='btn-group' data-toggle='buttons'>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol1' value='A' $chk1> A </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol2' value='B' $chk2> B </label>
                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_gol' id='rb_gol3' value='H' $chk3> H </label>
                                </div>
                            </div>";
                        echo "</div>";
                        
                        
                echo "</div>";//end readonly
                
                
                $rd="";
                if ($_SESSION['GROUP']==22 OR $_SESSION['GROUP']==34) $rd="aread";
                echo "<div class=''>";
                
                
                    $div=  $r['DIVISI2'];
                    if ($div=="OTC") {
                        
                    
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_kode'>kode <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_kodesub' name='cb_kodesub' onchange=\"showSubPosting('cb_kodesub', 'cb_kode')\">";
                            
                            $tampil=mysqli_query($cnmy, "SELECT distinct subpost as  kodeid, nmsubpost as nama FROM dbmaster.brkd_otc where ifnull(subpost,'')<>'' order by kodeid");
                            
                            echo "<option value=''>-- Pilih --</option>";
                            while($a=mysqli_fetch_array($tampil)){
                                $kd="";
                                $kdoid=$r['subpost'];
                                if ($a['kodeid']==$kdoid)
                                    echo "<option value='$a[kodeid]' selected>$kd$a[nama]</option>";
                                else
                                    echo "<option value='$a[kodeid]'>$kd$a[nama]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                    }else{
                        echo "<input type='hidden' id='cb_kodesub' name='cb_kodesub' value=''>";
                    }
                        

                
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_kode'>Posting (kode) <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='soflow' id='cb_kode' name='cb_kode'>";
                            
                            $div=  $r['DIVISI2'];
                            $filter="where divprodid='$div'";
                            if (empty($div)) $filter="";
                            
                            if ($div=="OTC") {
                                $filsub="";
                                if (!empty($r['subpost'])) $filsub=" where subpost='$r[subpost]'";
                                $tampil=mysqli_query($cnmy, "SELECT kodeid, nama FROM dbmaster.brkd_otc order by kodeid");
                            }else{
                                if (empty($div) OR $div=="OTHER" OR $div=="OTHERS") {
                                    $tampil=mysqli_query($cnmy, "SELECT kodeid, nama, divprodid FROM dbmaster.br_kode  order by divprodid, kodeid");
                                }else{
                                    $tampil=mysqli_query($cnmy, "SELECT kodeid, nama, divprodid FROM dbmaster.br_kode WHERE divprodid='$div' order by kodeid");
                                }
                            }
                            
                            echo "<option value=''>-- Pilih --</option>";
                            while($a=mysqli_fetch_array($tampil)){
                                $kd="$a[kodeid] - ";
                                $kdoid=$r['kodeid'];
								$pkodedivisi=$a['divprodid'];
                                if ($div=="OTC") {
                                    $kd="";
                                }
                                
                                if ($a['kodeid']==$kdoid)
                                    echo "<option value='$a[kodeid]' selected>$kd$a[nama] ($pkodedivisi)</option>";
                                else
                                    echo "<option value='$a[kodeid]'>$kd$a[nama] ($pkodedivisi)</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                
                echo "</div>";//end readonly
                        

                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='button' class='btn btn-success' onclick=\"disp_confirm('Update ?','update')\">Save</button>
                            </div>";
                        echo "</div>";

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
