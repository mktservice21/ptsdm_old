<div class="">

    <div class="page-title"><div class="title_left"><h3>Data Barang</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_brg_barang/aksi_barang.php";
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
                            echo "<thead><tr><th width='10px'>No</th><th width='150px'>Kategori</th>"
                            . "<th width='70px'>Kode</th><th>Nama</th><th width='100px'>Divisi</th><th width='100px'>Aktif</th>"
                                    . "<th width='100px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            $tampil = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_barang order by NAMA_BARANG, IDBARANG, NAMA_KATEGORI");
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[NAMA_KATEGORI]</td>";
                                echo "<td>$r[IDBARANG]</td>";
                                echo "<td>$r[NAMA_BARANG]</td>";
                                echo "<td>$r[DIVISIID]</td>";
                                echo "<td>$r[AKTIF]</td>";
                                echo "<td>";//AKSI
                                    echo " <a class='btn btn-success btn-sm' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[IDBARANG]>Edit</a>
                                        <a class='btn btn-danger btn-sm' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[IDBARANG]&idmenu=$_GET[idmenu]\"
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
                ?> <script> window.onload = function() { document.getElementById("e_nmbarang").focus(); } </script> <?PHP
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
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left' enctype='multipart/form-data'>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmbarang'>Kategori <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='form-control' name='cb_kategori'>";
                            echo "<option value=0 selected>- Pilih Kategori -</option>";
                            $tampil=mysqli_query($cnmy, "SELECT * FROM dbmaster.t_barang_kategori");
                            while($r=mysqli_fetch_array($tampil)){
                              echo "<option value='$r[IDKATEGORI]'>$r[NAMA_KATEGORI]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmbarang'>Nama <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmbarang' name='e_nmbarang' required='required' class='form-control col-md-7 col-xs-12'>
                            </div>";
                        echo "</div>";

                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_divisi'>Divisi <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='form-control' name='cb_divisi'>";
                            echo "<option value=0 selected>- Pilih Divisi -</option>";
                            $tampil=mysqli_query($cnmy, "SELECT * FROM dbmaster.divisi where cadv <> 'N'");
                            while($r=mysqli_fetch_array($tampil)){
                              echo "<option value='$r[divisiId]'>$r[divisiId]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmbarang'>Gambar <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='file' name= 'image' id ='image' accept='image/*;capture=camera'/>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='submit' class='btn btn-success'>Save</button>
                            </div>";
                        echo "</div>";

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";
            break;

            case "editdata":
                ?> <script> window.onload = function() { document.getElementById("e_nmbarang").focus(); } </script> <?PHP

                $edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.v_barang WHERE IDBARANG='$_GET[id]'");
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
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left' enctype='multipart/form-data'>
                            <input type='hidden' name='id' value='$r[IDBARANG]'>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmbarang'>Kategori <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='form-control' name='cb_kategori'>";
                            $tampil=mysqli_query($cnmy, "SELECT * FROM dbmaster.t_barang_kategori");
                            while($k=mysqli_fetch_array($tampil)){
                                if ($r['IDKATEGORI']==$k['IDKATEGORI'])
                                    echo "<option value='$k[IDKATEGORI]' selected>$k[NAMA_KATEGORI]</option>";
                                else
                                    echo "<option value='$k[IDKATEGORI]'>$k[NAMA_KATEGORI]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmbarang'>Nama <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmbarang' name='e_nmbarang' required='required' class='form-control col-md-7 col-xs-12' value='$r[NAMA_BARANG]'>
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_divisi'>Divisi <span class='required'></span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                            echo "<select class='form-control' name='cb_divisi'>";
                            echo "<option value=0 selected>- Pilih Divisi -</option>";
                            $tampil=mysqli_query($cnmy, "SELECT * FROM dbmaster.divisi where cadv <> 'N'");
                            while($k=mysqli_fetch_array($tampil)){
                                if ($r['DIVISIID']==$k['divisiId'])
                                    echo "<option value='$k[divisiId]' selected>$k[divisiId]</option>";
                                else
                                    echo "<option value='$k[divisiId]'>$k[divisiId]</option>";
                            }
                            echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmbarang'>Gambar <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='file' name= 'e_image' >
                            </div>";
                        echo "</div>";
                        
                        //header("Content-type: image/jpg"); 
                        echo $r['GAMBAR']; 
                        //echo '<img src="data:image/jpeg;base64,'.base64_encode( $r['GAMBAR'] ).'"/>';
                        
                        echo '<img src="data:image/jpeg;base64,'.base64_encode($r['GAMBAR'] ).'" height="200" width="200" class="img-thumnail" />'; 
                         
                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='submit' class='btn btn-success'>Save</button>
                            </div>";
                        echo "</div>";

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";
                
                 
                $query = "SELECT * FROM dbmaster.t_image ORDER BY ID DESC";  
                $result = mysqli_query($cnmy, $query);  
                while($row = mysqli_fetch_array($result))  
                {  
                     echo '  
                          <tr>  
                               <td>  
                                    <img src="data:image/jpeg;base64,'.base64_encode($row['GAMBAR'] ).'" height="200" width="200" class="img-thumnail" />  
                               </td>  
                          </tr>  
                     ';  
                }  
                

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
