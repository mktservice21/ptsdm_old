
<?php
    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    if ($_GET['module']=='lapbrtransotc'){
        include 'module/lap_br_otctrans/aksi_brotctrans.php';
    }elseif ($_GET['module']=='lapbrotctransfhari'){
        include 'module/lap_br_otctranshari/aksi_brotctranshari.php';
    }elseif ($_GET['module']=='lapbrotcpermo'){
        if ($_GET['ket']=="excel")
            include 'module/lap_br_otcpermohonan/aksi_brotcpermo_excel.php';//aksi_brotcpermo_excel.php
        else
            include 'module/lap_br_otcpermohonan/aksi_brotcpermo.php';
    }elseif ($_GET['module']=='lapbrotcpermorpt'){
        include 'module/mod_br_spdotc/laporanpdotc.php';
    }elseif ($_GET['module']=='lapbrinputsbyotc'){
        include 'module/lap_br_otcinputsby/aksi_otcinputsby.php';
    }elseif ($_GET['module']=='lapslscabdiv'){
        if ($_POST['rb_rpttipe']=='M')
            include 'module/a_sales/sls_cabang_divisi/index.php';
        elseif ($_POST['rb_rpttipe']=='Y')
            include 'module/a_sales/sls_cabang_divisi_ytd/index.php';
    }elseif ($_GET['module']=='lapslsproddiv'){
        if ($_POST['rb_rpttipe']=='M')
            include 'module/a_sales/sls_cabang_divisi_unit/index.php';
        elseif ($_POST['rb_rpttipe']=='Y')
            include 'module/a_sales/sls_cabang_divisi_unit_ytd/index.php';
            
            
    }elseif ($_GET['module']=='lapslscabdivams'){
        if ($_POST['rb_rpttipe']=='M')
            include 'module/a_sales/sls_cabang_divisi_dist/index.php';
        elseif ($_POST['rb_rpttipe']=='Y')
            include 'module/a_sales/sls_cabang_divisi_dist_ytd/index.php';
        
        
    }elseif ($_GET['module']=='lapslsproddivams'){
        if ($_POST['rb_rpttipe']=='M')
            include 'module/a_sales/sls_cabang_divisi_dist_unit/index.php';
        elseif ($_POST['rb_rpttipe']=='Y')
            include 'module/a_sales/sls_cabang_divisi_dist_unit_ytd/index.php';
        
    }elseif ($_GET['module']=='lapslsspv'){
        if ($_POST['rb_rpttipe']=='M')
            include 'module/a_sales/slsspv/index.php';
        elseif ($_POST['rb_rpttipe']=='Y')
            include 'module/a_sales/slsspvytd/index.php';
        
    }elseif ($_GET['module']=='lapslsdm'){
        if ($_POST['rb_rpttipe']=='Y')
            include 'module/a_sales/slsdmytd/index.php';
        
    }elseif ($_GET['module']=='lapslsmr'){
        if ($_POST['rb_rpttipe']=='Y')
            include 'module/a_sales/slsmrytd/index.php';
        
        
    }elseif ($_GET['module']=='entrybrbulan'){
        include 'module/mod_br_entrybrbulan/laporanbrbulan.php';
    }elseif ($_GET['module']=='entrybrrutin'){
        include 'module/mod_br_brrutin/laporanbrrutin.php';
    }elseif ($_GET['module']=='editdataketerutincalk'){
        include 'module/mod_apv_biayarutin/editdataketerangan.php';
    }elseif ($_GET['module']=='simpandataeditlkrutinket'){
        include 'module/mod_apv_biayarutin/aksi_editkrterangan.php';

        
    }elseif ($_GET['module']=='downloadrutinpdf'){
        include 'module/mod_br_brrutin/printpdf.php';
        
    }elseif ($_GET['module']=='pdfdownloadrutin'){
        include 'module/mod_br_brrutin/printpdf2.php';
        
    }elseif ($_GET['module']=='entrybrrutinotc'){
        include 'module/mod_br_brrutin/laporanbrrutin.php';
    }elseif ($_GET['module']=='entrybrluarkota'){
        include 'module/mod_br_entrybrluarkota/laporanbrluarkota.php';
    }elseif ($_GET['module']=='entrybrluarkotaotc'){
        include 'module/mod_br_entrybrluarkota/laporanbrluarkota.php';
    }elseif ($_GET['module']=='entrybrcash'){
        include 'module/mod_br_entrybrcash/laporanbrcash.php';
    }elseif ($_GET['module']=='entrybrcashotc'){
        include 'module/mod_br_entrybrcash/laporanbrcash.php';
    }elseif ($_GET['module']=='entrybrservicekendaraan'){
        include 'module/mod_br_entryservice/laporanbrservice.php';
    }elseif ($_GET['module']=='entrybrsewa'){
        include 'module/mod_br_entrysewa/laporanbrsewa.php';
        
    }elseif ($_GET['module']=='lapklaimpengobatan'){
        include 'module/laporan/mod_lap_pengobatan/aksi_lappengobatan.php';
    }elseif ($_GET['module']=='laprincianbrrutin'){
        include 'module/laporan/mod_lap_rincianbrrutin/aksi_laprincianbrrutin.php';
    }elseif ($_GET['module']=='laprutinrinciotc'){
        include 'module/laporan/mod_lap_rincianbrrutinotc/aksi_laprincianbrrutinotc.php';
        
    }elseif ($_GET['module']=='rptlamarealbr'){
        include 'module/data_lama/lap_br_realisasi/rlbr01.php';
    }elseif ($_GET['module']=='rptlamarealbredit'){
        include 'module/data_lama/lap_br_realisasi/rlbr02.php';
    }elseif ($_GET['module']=='rptlamarealbrbulan'){
        include 'module/data_lama/lap_br_realisasibulan/rprlar_1.php';
    }elseif ($_GET['module']=='rptlamabrdccdss'){
        include 'module/data_lama/lap_br_dccdss/rpbreq3.php';
    }elseif ($_GET['module']=='rptlamabrnon'){
        include 'module/data_lama/lap_br_nondccdss/rpbreq5.php';
    }elseif ($_GET['module']=='rptlamabrytddccdss'){
        include 'module/data_lama/lap_br_ytddccdss/rpbrthn1.php';
    }elseif ($_GET['module']=='rptlamabrrekapsby'){
        include 'module/data_lama/lap_br_rekapsby/rprlsby1.php';
    }elseif ($_GET['module']=='rptlamabrlapviasby'){
        include 'module/data_lama/lap_br_lapviasby/rpbrsby1.php';
    }elseif ($_GET['module']=='rptlamabrlapklaimdisbulan'){
        include 'module/data_lama/lap_br_lapklaimbulan/lpklaim1.php';
    }elseif ($_GET['module']=='rptlamabrlaprekapbr'){
        include 'module/data_lama/lap_br_lapbrrekap/rpbreq7.php';
    }elseif ($_GET['module']=='rptlamabrlapkeuangan'){
        include 'module/data_lama/lap_br_lapbrkeuangan/rpfnmrk1.php';
        
    }elseif ($_GET['module']=='apvrekapbr'){
        include 'module/data_lama/lap_apv_rekapbr/rptgltr1.php';
    }elseif ($_GET['module']=='apvrekapbrdisklaim'){
        include 'module/data_lama/lap_apv_rekapbrklaim/rptgltr3.php';
    }elseif ($_GET['module']=='apvrekapbracc'){
        include 'module/data_lama/lap_apv_rekapbracc/rptgacc1.php';
    }elseif ($_GET['module']=='apvrekapbrviasby'){
        include 'module/data_lama/lap_apv_rekapbraccsby/rpbrsby3.php';
    }elseif ($_GET['module']=='apvrekapbraccviasby'){
        include 'module/data_lama/lap_apv_rekapbraccsbyacc/rptgacc3.php';
        
    }elseif ($_GET['module']=='ethrealisasibrotc'){
        include 'module/data_lama/lap_eth_realisasibrotc/rlbrotc1.php';
    }elseif ($_GET['module']=='anneklaimkesehatan'){
        include 'module/data_lama/lap_anne_klaimkesehatan/rpklm011.php';
        
    }elseif ($_GET['module']=='otcrptlamaviewbrtrans'){
        include 'module/data_lama/otc_br_viewtrans/brotc11.php';
    }elseif ($_GET['module']=='otcrptlamaviewbrtgl'){
        include 'module/data_lama/otc_br_viewtgl/brotc21.php';
    }elseif ($_GET['module']=='otclaptrans'){
        include 'module/data_lama/otc_lap_brtransfer/rpbroan1.php';
    }elseif ($_GET['module']=='otclaprekaptrans'){
        include 'module/data_lama/otc_lap_rekaptrans/rptrnsf1.php';
    }elseif ($_GET['module']=='otclapinputsby'){
        include 'module/data_lama/otc_lap_sbyinputrpt/rpbosby1.php';
    }elseif ($_GET['module']=='otclapakhirsby'){
        include 'module/data_lama/otc_lap_sbyakhirrpt/rpbrasb1.php';
    }elseif ($_GET['module']=='otclaprekapbr'){
        include 'module/data_lama/otc_lap_rekapbr/rpdtbr01.php';
    }elseif ($_GET['module']=='otclaprekapbr2'){
        include 'module/data_lama/otc_lap_rekapbr2/rpdtbr01.php';
    }elseif ($_GET['module']=='otclaprekapbr3'){
        include 'module/data_lama/otc_lap_rekapbr3/rpdtbr01.php';
    }elseif ($_GET['module']=='kasisikas'){
        include 'module/data_lama/kas_isikas/rpdtbr01.php';
    }elseif ($_GET['module']=='kaslihatedit'){
        include 'module/data_lama/kas_kaslihatedit/kas11.php';
    }elseif ($_GET['module']=='kaslapkas'){
        include 'module/data_lama/kas_kaslap/rpkas5.php';
    }elseif ($_GET['module']=='kasrekap'){
        include 'module/data_lama/kas_kasrekap/rpkaskk1.php';
        
        
    }elseif ($_GET['module']=='lapbiayarutinotc'){
        include 'module/laporan/mod_lap_brrutinotc/aksi_lapbrrutinotc.php';
    }elseif ($_GET['module']=='lapbiayarutin'){
        include 'module/laporan/mod_lap_brrutin/aksi_lapbrrutin.php';
    }elseif ($_GET['module']=='rekapbiayarutinotc'){
        include 'module/laporan/mod_rekap_brrutinotc/aksi_rekapbrrutinotc.php';
    }elseif ($_GET['module']=='rekapbiayarutincaotc'){
        include 'module/mod_br_spdrutinotc/rptcarutin.php';
    }elseif ($_GET['module']=='rekapbiayarutin'){
        include 'module/laporan/mod_rekap_brrutin/aksi_rekapbrrutin.php';
    }elseif ($_GET['module']=='rekapbiayarutinnorek'){
        include 'module/laporan/mod_rekap_brrutin_rek/aksi_rekapbrrutin.php';
    }elseif ($_GET['module']=='rekapbiayaluarotc'){
        include 'module/laporan/mod_rekap_brluarotc/aksi_rekapbrluarotc.php';
    }elseif ($_GET['module']=='rekapbiayaluar'){
        include 'module/laporan/mod_rekap_brluar/aksi_rekapbrluar.php';
    }elseif ($_GET['module']=='lapbiayaluarotc'){
        include 'module/laporan/mod_lap_brluarotc/aksi_lapbrluarotc.php';
    }elseif ($_GET['module']=='laporanbiayarutinotc'){
        include 'module/laporan/mod_laporan_brrutinotc/aksi_lapbrrutinotc.php';
    }elseif ($_GET['module']=='lapbiayaluar'){
        include 'module/laporan/mod_lap_brluar/aksi_lapbrluar.php';
    }elseif ($_GET['module']=='lapbrca'){
        include 'module/laporan/mod_lap_brca/aksi_lapbrca.php';
    }elseif ($_GET['module']=='lapbrcaotc'){
        include 'module/laporan/mod_lap_brcaotc/aksi_lapbrcaotc.php';
    }elseif ($_GET['module']=='rekapbrcaotc'){
        include 'module/laporan/mod_rekap_brcaotc/aksi_rekapbrcaotc.php';
    }elseif ($_GET['module']=='rekapbrca'){
        include 'module/laporan/mod_rekap_brca/aksi_rekapbrca.php';
    }elseif ($_GET['module']=='realisasiblotc'){
        include 'module/laporan/mod_realisasiblotc/aksi_realisasiblotc.php';
    }elseif ($_GET['module']=='realisasibl'){
        include 'module/laporan/mod_realisasibl/aksi_realisasibl.php';
    }elseif ($_GET['module']=='lapsuratcalk'){
        include 'module/laporan/mod_lap_suratca/aksi_lapsuratca.php';
        
    }elseif ($_GET['module']=='lapbudgetcoa'){
        include 'module/laporan/mod_laporanbudgetcoa/aksi_laporanbudgetcoa.php';
    }elseif ($_GET['module']=='transferbrotc'){
        include 'module/laporan/mod_lapbrtransfer/aksi_lapbrtransfer.php';
    }elseif ($_GET['module']=='transferblotc'){
        include 'module/laporan/mod_lapbltransfer/aksi_lapbltransfer.php';
        
        
        
    }elseif ($_GET['module']=='datakaryawan'){
        include 'module/lap_m_karyawan/lihatdatakaryawan.php';
        
    }elseif ($_GET['module']=='finprosbiayarutin'){
        $iprintrut="";
        if (isset($_GET['iprint'])) {
            if ($_GET['iprint']=="editrutin"){
                include 'module/mod_fin_prosbiayarutin/editdatarutin.php';
                $iprintrut="true";
            }elseif ($_GET['iprint']=="isipajak"){
                include 'module/mod_fin_prosbiayarutin/pajakdatarutin.php';
                $iprintrut="true";
            }
        }
        if (empty($iprintrut))
            include 'module/mod_fin_prosbiayarutin/rekapdatarutin.php';
    }elseif ($_GET['module']=='finprosbiayaluar'){
        include 'module/mod_fin_prosbiayaluarkota/rekapdataluarkota.php';
    }elseif ($_GET['module']=='finprosca'){
        include 'module/mod_fin_prosca/editdataca.php';
        
        
    }elseif ($_GET['module']=='lapgl'){
        include 'module/laporan/mod_gl_laporan/aksi_lapgl.php';
        
    }elseif ($_GET['module']=='lapgeneralledgerx'){
        include 'module/laporan/mod_gl_laporan3/aksi_lapgl.php';
    }elseif ($_GET['module']=='lapgeneralledger'){
        include 'module/laporan_gl/mod_generalledger/aksi_generalledger.php';
        
    }elseif ($_GET['module']=='glreportspd'){
        include 'module/laporan_gl/mod_gl_rptspd/aksi_rptspd.php';
    }elseif ($_GET['module']=='suratpdpreview'){
        include 'module/laporan_gl/mod_gl_rptspd/aksi_rptspd.php';
    }elseif ($_GET['module']=='glreportspddetail'){
        include 'module/laporan_gl/mod_gl_rptspd/reportspd_detail.php';
        
    }elseif ($_GET['module']=='glrekapbank'){
        include 'module/laporan_gl/mod_gl_rekapbank/aksi_rekapbank.php';
    }elseif ($_GET['module']=='brdanabank'){
        include 'module/laporan_gl/mod_gl_rekapbank/aksi_rekapbank.php';
        
    }elseif ($_GET['module']=='cfrealisasidana'){
        include 'module/laporan_gl/mod_gl_cfrealisasi/aksi_cfrealisasi.php';
    }elseif ($_GET['module']=='realisasidana'){
        include 'module/laporan_gl/mod_gl_realisasidana/aksi_realisasidana.php';
        
    }elseif ($_GET['module']=='glrekapbr'){
        include 'module/laporan_gl/mod_gl_rekapbr/aksi_glrekapbr.php';
    }elseif ($_GET['module']=='glrekapbrotc'){
        include 'module/laporan_gl/mod_gl_rekapbrotc/aksi_glrekapbrotc.php';
    }elseif ($_GET['module']=='glrekapbrklaim'){
        include 'module/laporan_gl/mod_gl_rekapbrklaim/aksi_glrekapbrklaim.php';
    }elseif ($_GET['module']=='glrekapbrrutin'){
        include 'module/laporan_gl/mod_gl_rekapbrrutin/aksi_glrekapbrrutin.php';
    }elseif ($_GET['module']=='glrekapbrluarkota'){
        include 'module/laporan_gl/mod_gl_rekapbrlk/aksi_glrekapbrlk.php';
    }elseif ($_GET['module']=='glrekapbrkas'){
        include 'module/laporan_gl/mod_gl_rekapbrkas/aksi_glrekapbrkas.php';
        
    }elseif ($_GET['module']=='gldetailrekapbr'){
        include 'module/laporan_gl/mod_gl_rekapbrdtl/aksi_glrekapbrdtl.php';
    }elseif ($_GET['module']=='gldetailrekapbrotc'){
        include 'module/laporan_gl/mod_gl_rekapbrotcdtl/aksi_glrekapbrotcdtl.php';
    }elseif ($_GET['module']=='gldetailrekapbrklaim'){
        include 'module/laporan_gl/mod_gl_rekapbrklaimdtl/aksi_glrekapbrklaimdtl.php';
        
    }elseif ($_GET['module']=='glrealbiayamkt'){
        include 'module/laporan_gl/mod_gl_rbm/aksi_rbm.php';
    }elseif ($_GET['module']=='glrealbiayamktcab'){
        include 'module/laporan_gl/mod_gl_rbmcab/aksi_rbmcab.php';
    }elseif ($_GET['module']=='glrealbiayamktfin'){
        include 'module/laporan_gl/mod_gl_rbmfin/aksi_rbmfin.php';
        
    }elseif ($_GET['module']=='gllapbiayakendaraan'){
        include 'module/laporan_gl/mod_gl_biayakendaraan/aksi_biayakendaraan.php';
    }elseif ($_GET['module']=='gllapbiayakendaraanperjalanan'){
        include 'module/laporan_gl/mod_gl_biayakendaraanjalan/aksi_biayakendaraanjalan.php';
    }elseif ($_GET['module']=='sumcf'){
        include 'module/laporan_gl/mod_gl_sumcf/aksi_sumcf.php';
        
        
    }elseif ($_GET['module']=='spgrekapgaji'){
        include 'module/laporan/mod_spg_rekapgaji/aksi_spgrekapgaji.php';
    }elseif ($_GET['module']=='spglapgaji'){
        include 'module/laporan/mod_spg_lapgaji/aksi_spglapgaji.php';
        
        
        
    }elseif ($_GET['module']=='sbyrekapbm'){
        include 'module/surabaya/mod_sby_lap_rekapbm/aksi_laprekapbm.php';
    }elseif ($_GET['module']=='lapbudgetmarketing'){
        include 'module/mod_budget_laprealisasi/aksi_lapbudg_realisasi.php';
    }elseif ($_GET['module']=='lapbudgetmarketingvsrealisasi'){
        include 'module/mod_budget_laprealisasibudget/aksi_realisasi_budget.php';
    }elseif ($_GET['module']=='lapbudgetmarketingvsrealisasiotc'){
        include 'module/mod_budget_laprealisasibudgetotc/aksi_realisasi_budgetotc.php';
        
        
    }elseif ($_GET['module']=='spdotc'){
        if ($_GET['act']=='isitglrptsby'){
            include 'module/mod_br_spdotc/isirptsby.php';
        }else{
            include 'module/mod_br_spdotc/rpbrasb1.php';
        }
        
        
    }elseif ($_GET['module']=='saldosuratdana'){
        $iid="";
        if (isset($_GET['iid'])) $iid=$_GET['iid'];
        if ($iid==1) {
            include 'module/mod_br_spd/laporanerni.php';
        }elseif ($iid==2) {
            include 'module/mod_br_spd/laporanprita.php';
        }elseif ($iid==5) {
            include 'module/mod_br_suratpd/laporananne.php';
        }
    }elseif ($_GET['module']=='suratpd'){
        $print="";
        if (isset($_GET['iprint'])) {
            if ($_GET['iprint']=="print") $print="print";
        }
        if ($print=="print")
            include 'module/mod_br_suratpd/printspd.php';
        else
            include 'module/mod_br_suratpd/laporanbr.php';
        
        
    }elseif ($_GET['module']=='outlkcaethical'){
        include 'module/mod_br_otsdlkca_eth/rpt_otsd_lkca.php';
    }elseif ($_GET['module']=='outlkcaotc'){
        include 'module/mod_br_otsdlkca_otc/rpt_otsd_lkca.php';
        
    }elseif ($_GET['module']=='spgharikerja'){
        include 'module/md_m_spg_harikerja/spg_rpt.php';
        
    }elseif ($_GET['module']=='spdkas'){
        include 'module/mod_br_spdkas/spdkas_rpt.php';
    }elseif ($_GET['module']=='entrybrkasbon'){
        include 'module/mod_br_isikasbon/isikasbon_rpt.php';
    }elseif ($_GET['module']=='viewrptdatabpjs'){
        include 'module/mod_br_spdbpjs/isipdbpjs_rpt.php';
        
    }elseif ($_GET['module']=='spdrutinotc'){
        include 'module/mod_br_spdrutinotc/rpt_spd_rutinotc.php';
        
    }elseif ($_GET['module']=='mstprosesinsentif'){
        include 'module/md_m_prosesdatainsentif/rpt_prosinc.php';
        
    }elseif ($_GET['module']=='laprutintahun'){
        include 'module/laporan/mod_rutin_pertahun/aksi_rutinpertahun.php';
    }elseif ($_GET['module']=='lapluarkotatahun'){
        include 'module/laporan/mod_lk_pertahun/aksi_lkpertahun.php';
    }elseif ($_GET['module']=='lapbrrutinotctahun'){
        include 'module/laporan/mod_rutin_pertahunotc/aksi_rutinpertahunotc.php';
    }elseif ($_GET['module']=='lapbrlkotctahun'){
        include 'module/laporan/mod_lk_pertahunotc/aksi_lkpertahunotc.php';
    }elseif ($_GET['module']=='lapkendaraandinas'){
        include 'module/laporan/mod_lap_kendaraan/aksi_lapkendaraan.php';
    }elseif ($_GET['module']=='reportcasewa'){
        include 'module/mod_br_spdrutineth/rpt_spd_casewa.php';
        
        
    }elseif ($_GET['module']=='laporangajispgotc'){
        include 'module/mod_br_spdotc/rpt_gajispgotc.php';
        
    }elseif ($_GET['module']=='rekapotsbr'){
        include 'module/laporan_gl/mod_gl_rekapots/aksi_rekapots.php';
    }elseif ($_GET['module']=='rekapotsbrotc'){
        include 'module/laporan_gl/mod_gl_rekapotsotc/aksi_rekapotsotc.php';
        
    }elseif ($_GET['module']=='rekapinsentifrekbank'){
        include 'module/laporan/mod_rekap_insentif_rek/aksi_rekapinsentif.php';
        
    }elseif ($_GET['module']=='lapbrpajak'){
        include 'module/laporan_gl/mod_gl_rekapbrpajak/aksi_rekapbrpajak.php';
        
    }elseif ($_GET['module']=='bukafilenya'){
        include 'bkf.php';
        
    }elseif ($_GET['module']=='closingbrlkca2'){
        include 'module/mod_br_closing_lkca_baru/rptcaclosing.php';
        
    }elseif ($_GET['module']=='printentrybrdcccabang'){
        include 'module/mod_br_entrybrdcccab/printdatabrcab.php';
        
    }elseif ($_GET['module']=='fincekprosesbrcab'){
        include 'module/mod_fin_cekprosbrcab/laporanbrcabfin.php';
        
    }elseif ($_GET['module']=='tgttargetarea'){
        include 'module/tgt_tgt_area/targetareadetail.php';
        
    }elseif ($_GET['module']=='lapslsdistytd'){
        include 'module/sls_distytd/aksi_distytd.php';
    }elseif ($_GET['module']=='slsperoutlet'){
        include 'module/sls_slsperoutlet/aksi_slsperoutlet.php';
    }elseif ($_GET['module']=='lapslspermr'){
        include 'module/sls_slspermr/aksi_slspermr.php';
    }elseif ($_GET['module']=='evalusasislsout'){
        include 'module/sls_evalslsout/aksi_evalslsout.php';
    }elseif ($_GET['module']=='rptytdcab'){
        include 'module/sls_slsytdpercab/aksi_slsytdpercab.php';
    }elseif ($_GET['module']=='slsrptrawdatacab'){
        include 'module/sls_rptrawdata/aksi_rptrawdata.php';
    }elseif ($_GET['module']=='slsrptrowdatasektor'){
        include 'module/sls_rptrawsektordata/aksi_rptrawsektordata.php';
        
    }elseif ($_GET['module']=='barangdata'){
        include 'module/mod_brg_barang/lihatgambar.php';
    }elseif ($_GET['module']=='gimickeluarbarang'){
        include 'module/mod_brg_keluarbrg/laporanskb.php';
    }elseif ($_GET['module']=='gimicterimabarang'){
        include 'module/mod_brg_terimabrg/laporanstb.php';
    }elseif ($_GET['module']=='gimicprintskb'){
        include 'module/mod_brg_printskb/laporanprint.php';
    }elseif ($_GET['module']=='gimiclapcab'){
        include 'module/mod_brg_lapgimcabang/aksi_lapgimcabang.php';
    }elseif ($_GET['module']=='gimiclapho'){
        include 'module/mod_brg_lapgimho/aksi_lapgimho.php';
    }elseif ($_GET['module']=='gimiclapskb'){
        include 'module/mod_brg_lapgimskb/aksi_lapgimskb.php';
    }elseif ($_GET['module']=='gimiclapstb'){
        include 'module/mod_brg_lapgimstb/aksi_lapgimstb.php';
        
        
    }elseif ($_GET['module']=='listantriantransfer'){
        include 'module/mod_fin_listantriantrf/listdatabulan.php';
        
        
    //new marvis sales
    }elseif ($_GET['module']=='salesytddaerah'){
        include 'module/a_new/ytd_daerah/index.php';
    }elseif ($_GET['module']=='salesytddaerahdm'){
        include 'module/a_new/dmytd_daerah/index.php';
    }elseif ($_GET['module']=='salesytdregion'){
        include 'module/a_new/ytd_region/index.php';
    }elseif ($_GET['module']=='salesytdsm'){
        include 'module/a_new/ytd_sm/index.php';
    }elseif ($_GET['module']=='slslpbdist'){
        include 'module/a_new/lpb_daerah/index.php';
    }elseif ($_GET['module']=='slslpbregion'){
        include 'module/a_new/lpb_region/index.php';
    }elseif ($_GET['module']=='salespermrspv'){
        include 'module/a_new/slsspv/index.php';
    }elseif ($_GET['module']=='salesytddivisipm'){
        include 'module/a_new/ytd_pm/index.php';
    }elseif ($_GET['module']=='slsytdmr'){
        include 'module/a_new/slsmrytd/index.php';
    }elseif ($_GET['module']=='slsytdam'){
        include 'module/a_new/slsspvytd/index.php';
    }elseif ($_GET['module']=='slsytddm'){
        include 'module/a_new/slsdmytd/index.php';
    }elseif ($_GET['module']=='saleslappersektor'){
        include 'module/sls_lapslspersektor/aksi_lapslspersektor.php';
    }elseif ($_GET['module']=='detailsaleslappersektor'){
        include 'module/sls_lapslspersektor/aksi_detailrpt.php';
    }elseif ($_GET['module']=='saleslappersektorregion'){
        include 'module/sls_lapslspersektorreg/aksi_lapslspersektorreg.php';
    }elseif ($_GET['module']=='detailsaleslappersektorreg'){
        include 'module/sls_lapslspersektorreg/aksi_detailrptreg.php';
    }elseif ($_GET['module']=='saleslappersektorsm'){
        include 'module/sls_lapslspersektorsm/aksi_lapslspersektorsm.php';
    }elseif ($_GET['module']=='detailsaleslappersektorsm'){
        include 'module/sls_lapslspersektorsm/aksi_detailrptsm.php';
        
        
    }elseif ($_GET['module']=='laptgtdaerahsmgsm'){
        include 'module/tgt_lap_tgtdaerahgsm/aksi_tgtlapdaerahgsm.php';
    }elseif ($_GET['module']=='laptgtdaerahdm'){
        include 'module/tgt_lap_tgtdaerahdm/aksi_tgtlapdaerahdm.php';
        
    }elseif ($_GET['module']=='accprosesbiayamrk'){
        include 'module/act_prosesbiayamkt/viewdataprosbm.php';
        
    }elseif ($_GET['module']=='lapbudgetpm'){
        include 'module/laporan/lap_budgetbrpm/aksi_budgetbrpm.php';
    }elseif ($_GET['module']=='lapbuddccdssreg'){
        include 'module/laporan/lap_budgetbrdccdssreg/aksi_budgetbrdccdssreg.php';
        
        //khusus
    }elseif ($_GET['module']=='appdirpd'){
        include 'module/dir_apvspd/ttd_dir.php';
        
    }elseif ($_GET['module']=='lapbrdcc'){
        include 'module/laporan/lap_brethical/aksi_lapbrethical.php';
    }elseif ($_GET['module']=='lapbrklaim'){
        include 'module/laporan/lap_kalimdis/aksi_lapklaimdis.php';
    }elseif ($_GET['module']=='lapbrotc'){
        include 'module/laporan/lap_brotc/aksi_lapbrotc.php';
    }elseif ($_GET['module']=='lapservicekendchc'){
        include 'module/laporan/lap_servicekenchc/aksi_lapservicekenchc.php';
    }elseif ($_GET['module']=='lapsewakontrakanrumheth'){
        include 'module/laporan/lap_sewarumah/aksi_lapsewarumah.php';
    }elseif ($_GET['module']=='lapbrrealisasi'){
        include 'module/laporan/lap_brrealisasi/aksi_lapbrrealisasi.php';
    }elseif ($_GET['module']=='lapbrrealisasidaerah'){
        include 'module/laporan/lap_brrealisasidaerah/aksi_lapbrrealisasidaerah.php';
    }elseif ($_GET['module']=='lapbrrealisasicabang'){
        include 'module/laporan/lap_brrealisasicabang/aksi_lapbrrealisasicabang.php';
    }elseif ($_GET['module']=='lapbrytd'){
        include 'module/laporan/lap_brytdrealisasicab/aksi_lapbrytdrealisasicab.php';
        
    }elseif ($_GET['module']=='laprekapdatakaryawan'){
        include 'module/laporan/lap_listdatakaryawa/aksi_laplistdatakaryawa.php';
    }elseif ($_GET['module']=='stclapodatastock'){
        include 'module/laporan/stc_lapdatastock/aksi_lapdatastock.php';
        
    }elseif ($_GET['module']=='bgtpdkaskecilcabang'){
        include 'module/mod_br_spdkascab/spdkascab_rpt.php';
        
    }elseif ($_GET['module']=='lapslscusteth'){
        include 'module/sls_lapbycusteth/aksi_lapbycusteth.php';
    }elseif ($_GET['module']=='slspenjualandisteth'){
        include 'module/sls_lappenjualandist/aksi_lappenjualandist.php';
        
    }elseif ($_GET['module']=='lapbudgetexpenseschc'){
        include 'module/laporan/lap_expensispmchk/aksi_lapexpensispmchk.php';
    }elseif ($_GET['module']=='lapbudgetexpenseschcdet'){
        include 'module/laporan/lap_expensispmchk/aksi_lapexpensispmchk_d.php';
        
    }elseif ($_GET['module']=='bgtkaskecilcabang'){
        include 'module/mod_br_kaskecilcab/iviewdata.php';
    }elseif ($_GET['module']=='bgtkaskecilcabangotc'){
        include 'module/mod_br_kaskecilcabotc/iviewdataotc.php';
    }elseif ($_GET['module']=='bgtadmentrybrklaim'){
        include 'module/mod_br_admentryklaim/iviewdata.php';
    }elseif ($_GET['module']=='slspabrikretur'){
        include 'module/sls_lappabrikdanretur/aksi_lappabrikdanretur.php';
    }elseif ($_GET['module']=='salesinsentifdm'){
        include 'module/sls_lapsalesinsentifdm/aksi_lapsalesinsentifdm.php';
        
    }elseif ($_GET['module']=='slssalesdiscdist'){
        include 'module/sls_salesvsdisc/aksi_salesvsdisc.php';
        
    }elseif ($_GET['module']=='laprinciankaskecilcab'){
        include 'module/laporan/lap_kaskecilcab_rincian/aksi_lapkaskecilcabrinci.php';
    }elseif ($_GET['module']=='laprinciankaskecilcabotc'){
        include 'module/laporan/lap_kaskecilcab_rincian/aksi_lapkaskecilcabrinci.php';
        
    }elseif ($_GET['module']=='lapksmonituser'){
        include 'module/laporan/lap_ks_user/aksi_laporanksuser.php';
    }elseif ($_GET['module']=='lihatdatausernew'){
        include 'module/laporan/lap_ks_user/lihatuserbaru.php';
    }elseif ($_GET['module']=='bgtmonitoringks'){
        include 'module/mod_br_monitoringks/aksi_brmonitoringks.php';
        
    }elseif ($_GET['module']=='entrybrrutinho'){
        include 'module/mod_br_brrutinho/laporanbrrutinho.php';
    }elseif ($_GET['module']=='laprincianbrrutinbykry'){
        include 'module/laporan/mod_lap_rincianbrrutinkry/aksi_laprincianbrrutinkry.php';
        
    }elseif ($_GET['module']=='ksdaftaruser'){
        include 'module/ks_daftaruserdr/aksi_daftaruserdr.php';
    }elseif ($_GET['module']=='ksinfouser'){
        include 'module/ks_infouserdr/aksi_infouserdr.php';
    }elseif ($_GET['module']=='ksinfouserpilih'){
        include 'module/ks_infouserdr/iuserpilih.php';
    }elseif ($_GET['module']=='lihatdataksusr'){
        include 'module/ks_infouserdr/ilihatdataks.php';
    }elseif ($_GET['module']=='kslihatks'){
        include 'module/ks_lihatks/aksi_lihatks.php';
    }elseif ($_GET['module']=='ksmonitoringkiks'){
        include 'module/ks_monitorkiks/aksi_monitorkiks.php';
    }elseif ($_GET['module']=='ksmonitoringkikscab'){
        include 'module/ks_monitorkikscb/aksi_monitorkikscb.php';
        
    }elseif ($_GET['module']=='pchpurchasereq'){
        include 'module/pch_pr/iviewdatapr.php';
    }elseif ($_GET['module']=='pchpotransaksi'){
        include 'module/pch_purchaseorder/iviewdatapo.php';
        
        
    }elseif ($_GET['module']=='isikartustatus'){
        include 'module/ks_isiks/aksi_isiproduk.php';
    }elseif ($_GET['module']=='isikartustatusberhasil'){
        include 'module/ks_isiks/berhasilsimapnksisi.php';
    }elseif ($_GET['module']=='ksisiestimasiki'){
        include 'module/ks_isiestimasiki/printestks.php';
        
    }elseif ($_GET['module']=='lapbrbykaryawan'){
        include 'module/laporan/lap_br_bykaryawan/aksi_lapbrbykaryawan.php';
        
    }elseif ($_GET['module']=='eksekusifileelastik'){
        //include 'module/mst_import_sales/aksi_importelastik.php';
        
    }elseif ($_GET['module']=='xxx'){
        
    }
?>

<?PHP
error_reporting(0);
include "config/koneksimysqli.php";
$pidpilihcarduser=$_SESSION['IDCARD'];
$ptglpilihskr= date("Y-m-d");
$pidmenupihskr="";
if (isset($_GET['idmenu'])) $pidmenupihskr=$_GET['idmenu'];
$querylog = "select distinct idmenu from dbmaster.sdm_akseslogin WHERE tanggal='$ptglpilihskr' AND karyawanid='$pidpilihcarduser' AND idmenu='$pidmenupihskr'";
$pketemulog= mysqli_num_rows(mysqli_query($cnmy, $querylog));
if ($pketemulog==0) { mysqli_query($cnmy, "INSERT INTO dbmaster.sdm_akseslogin(tanggal, karyawanid, idmenu, jumlah, jml_aksesrpt)VALUES(CURRENT_DATE(), '$pidpilihcarduser', '$pidmenupihskr', '0', '0')"); }
mysqli_query($cnmy, "UPDATE dbmaster.sdm_akseslogin SET jml_aksesrpt=IFNULL(jml_aksesrpt,0)+1 WHERE tanggal='$ptglpilihskr' AND karyawanid='$pidpilihcarduser' AND idmenu='$pidmenupihskr'");
mysqli_close($cnmy);
error_reporting(-1);
?>