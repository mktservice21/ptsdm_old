<script>
function disp_confirm(pText_,ket)  {
    
    var eid =document.getElementById('e_id').value;
    var enama =document.getElementById('e_nmcoa').value;
    
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
                document.getElementById("demo-form2").action = "module/mod_coa_coa1/aksi_coa1.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }else{
                $.ajax({
                    type:"post",
                    url:"module/mod_coa_coa1/aksi_coa1.php?module=carikodesama",
                    data:"ukode="+eid,
                    success:function(data){
                        var edata =data;
                        
                        if (edata=="") {
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_coa_coa1/aksi_coa1.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
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
</script>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Data COA Level 1</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //include "config/koneksimysqli_it.php";
        $aksi="module/mod_coa_coa1/aksi_coa1.php";
        switch($_GET['act']){
            default:

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru';\">
                            <small></small>
                            </h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='100px'>Kode</th><th>Nama</th>"
                            . "<th width='70px'>Gol</th><th width='70px'>Aktif</th><th width='100px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            $tampil = mysqli_query($cnmy, "SELECT * FROM dbmaster.coa_level1 order by COA1");
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[COA1]</td>";
                                echo "<td>$r[NAMA1]</td>";
                                echo "<td>$r[GOL1]</td>";
                                echo "<td>$r[AKTIF1]</td>";
                                echo "<td>";//AKSI
                                    echo " <a class='btn btn-success btn-sm' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[COA1]>Edit</a>
                                        <a class='btn btn-danger btn-sm' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[COA1]&idmenu=$_GET[idmenu]\"
                                        onClick=\"return confirm('Apakah Anda melakukan proses?')\">Aktif</a>";
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
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>COA KODE <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_id' name='id' required='required' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask=\"'mask' : '**'\">
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>NAMA <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmcoa' name='e_nmcoa' required='required' class='form-control col-md-7 col-xs-12'>
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

                $edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.coa_level1 WHERE COA1='$_GET[id]'");
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
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>COA KODE <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_id' name='id' required='required' class='form-control col-md-7 col-xs-12' value='$r[COA1]' readonly>
                            </div>";
                        echo "</div>";
                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmcoa'>NAMA <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmcoa' name='e_nmcoa' required='required' class='form-control col-md-7 col-xs-12' value='$r[NAMA1]'>
                            </div>";
                        echo "</div>";
                        
                        $chk1="";
                        $chk2="";
                        $chk3="";
                        if ($r['GOL1']=="A") $chk1="checked";
                        elseif ($r['GOL1']=="B") $chk2="checked";
                        elseif ($r['GOL1']=="H") $chk3="checked";
                        
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

                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='button' class='btn btn-success' onclick=\"disp_confirm('Update ?', 'update')\">Save</button>
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
