<?php

    //dipakai di UPLOAD DATA SALES (IMPORT) untuk konek ke IT, jika koneksi ke IT error, statusnya diubah menjadi false;
    $_SESSION['PROSESLOGKONEK_IT']=true;
    
    
    //OTC
    $_SESSION['OTCTIPE']="";
    $_SESSION['OTCTGLTIPE']="";
    $_SESSION['OTCPERENTY1']="";
    $_SESSION['OTCPERENTY2']="";
    
    
    
    //NON
    $_SESSION['FINNONTIPE']="";
    $_SESSION['FINNONTGLTIPE']="";
    $_SESSION['FINNONPERENTY1']="";
    $_SESSION['FINNONPERENTY2']="";
    $_SESSION['FINNONDIV']="";
    $_SESSION['FINNONUSPL']="";
    
    
    
    //DSS
    $_SESSION['FINDDTIPE']="";
    $_SESSION['FINDDTGLTIPE']="";
    $_SESSION['FINDDPERENTY1']="";
    $_SESSION['FINDDPERENTY2']="";
    $_SESSION['FINDDDIV']="";
    $_SESSION['FINUSPL']="";
    
    
    //klaim
    $_SESSION['FINKLMTIPE']="";
    $_SESSION['FINKLMTGLTIPE']="";
    $_SESSION['FINKLMPERENTY1']="";
    $_SESSION['FINKLMPERENTY2']="";
    
    
    //Biaya rutin luar kota
    $_SESSION['FINBLTIPE']="";
    $_SESSION['FINBLTGLTIPE']="";
    $_SESSION['FINBLPERENTY1']="";
    $_SESSION['FINBLPERENTY2']="";
    $_SESSION['FINBLDIV']="";
    $_SESSION['FINBLCAB']="";
    
    
    //Biaya rutin
    $_SESSION['FINRUTTIPE']="";
    $_SESSION['FINRUTTGLTIPE']="";
    $_SESSION['FINRUTPERENTY1']="";
    $_SESSION['FINRUTPERENTY2']="";
    $_SESSION['FINRUTDIV']="";
    $_SESSION['FINRUTCAB']="";
    
    //Biaya rutin otc
    $_SESSION['FINRUTTIPEOTC']="";
    $_SESSION['FINRUTTGLTIPEOTC']="";
    $_SESSION['FINRUTPERENTYOTC1']="";
    $_SESSION['FINRUTPERENTYOTC2']="";
    $_SESSION['FINRUTDIVOTC']="";
    $_SESSION['FINRUTCABOTC']="";
    
    //Luar Kota
    $_SESSION['FINLURTIPE']="";
    $_SESSION['FINLURTGLTIPE']="";
    $_SESSION['FINLURPERENTY1']="";
    $_SESSION['FINLURPERENTY2']="";
    $_SESSION['FINLURDIV']="";
    $_SESSION['FINLURCAB']="";
    
    //Luar Kota OTC
    $_SESSION['FINLURTIPEOTC']="";
    $_SESSION['FINLURTGLTIPEOTC']="";
    $_SESSION['FINLURPERENTYOTC1']="";
    $_SESSION['FINLURPERENTYOTC2']="";
    $_SESSION['FINLURDIVOTC']="";
    $_SESSION['FINLURCAB']="";
    
    //COA BIAYA
    $_SESSION['COABIAYADIV']="";
    
    //CA
    $_SESSION['FINCAATIPE']="";
    $_SESSION['FINCAATGLTIPE']="";
    $_SESSION['FINCAAPERENTY1']="";
    $_SESSION['FINCAAPERENTY2']="";
    $_SESSION['FINCAADIV']="";
    $_SESSION['FINCAACAB']="";
    
    //CA OTC
    $_SESSION['FINCAATIPEOTC']="";
    $_SESSION['FINCAATGLTIPEOTC']="";
    $_SESSION['FINCAAPERENTYOTC1']="";
    $_SESSION['FINCAAPERENTYOTC2']="";
    $_SESSION['FINCAADIVOTC']="";
    $_SESSION['FINCAACABOTC']="";
    
    
    //Plan UC
    $_SESSION['EKARUC']="";
    $_SESSION['UCPTGL1']="";
    
    
    //SERVICE
    $_SESSION['SVCKTGLTIPE']="";
    $_SESSION['SVCKTIPE']="";
    $_SESSION['SVCKENTY1']="";
    $_SESSION['SVCKENTY2']="";
    $_SESSION['SVCKDIV']="";
    $_SESSION['SVCKCAB']="";
    
    
    //SEWA
    $_SESSION['SWKRTGLTIPE']="";
    $_SESSION['SWKRTIPE']="";
    $_SESSION['SWKRENTY1']="";
    $_SESSION['SWKRENTY2']="";
    $_SESSION['SWKRDIV']="";
    $_SESSION['SWKRCAB']="";
    
    //approve biaya luar kota
    $_SESSION['APVBLK_KET']="";
    $_SESSION['APVBLK_TGL1']="";
    $_SESSION['APVBLK_TGL2']="";
    $_SESSION['APVBLK_KRY']="";
    $_SESSION['APVBLK_LVL']="";
    $_SESSION['APVBLK_DIV']="";
    $_SESSION['APVBLK_STSAPV']="";
    
    //proses biaya luar kota
    $_SESSION['PROSBLK_TIPE']="";
    $_SESSION['PROSBLK_KET']="";
    $_SESSION['PROSBLK_TGL1']="";
    $_SESSION['PROSBLK_TGL2']="";
    $_SESSION['PROSBLK_KRY']="";
    $_SESSION['PROSBLK_LVL']="";
    $_SESSION['PROSBLK_DIV']="";
    $_SESSION['PROSBLK_STSAPV']="";
    
    //approve biaya rutin
    $_SESSION['APVRUT_KET']="";
    $_SESSION['APVRUT_TGL1']="";
    $_SESSION['APVRUT_TGL2']="";
    $_SESSION['APVRUT_KRY']="";
    $_SESSION['APVRUT_LVL']="";
    $_SESSION['APVRUT_DIV']="";
    $_SESSION['APVRUT_STSAPV']="";
    
    //proses biaya rutin
    $_SESSION['PROSRUT_TIPE']="";
    $_SESSION['PROSRUT_KET']="";
    $_SESSION['PROSRUT_TGL1']="";
    $_SESSION['PROSRUT_TGL2']="";
    $_SESSION['PROSRUT_KRY']="";
    $_SESSION['PROSRUT_LVL']="";
    $_SESSION['PROSRUT_DIV']="";
    $_SESSION['PROSRUT_STSAPV']="";
    
    //apv ca
    $_SESSION['APVCAISI_TIPE']="";
    $_SESSION['APVCAISI_KET']="";
    $_SESSION['APVCAISI_TGL1']="";
    $_SESSION['APVCAISI_TGL2']="";
    $_SESSION['APVCAISI_KRY']="";
    $_SESSION['APVCAISI_LVL']="";
    $_SESSION['APVCAISI_DIV']="";
    $_SESSION['APVCAISI_STSAPV']="";
    
    //apv ca biaya otc
    $_SESSION['APVCAISIOTC_TIPE']="";
    $_SESSION['APVCAISIOTC_KET']="";
    $_SESSION['APVCAISIOTC_TGL1']="";
    $_SESSION['APVCAISIOTC_TGL2']="";
    $_SESSION['APVCAISIOTC_KRY']="";
    $_SESSION['APVCAISIOTC_LVL']="";
    $_SESSION['APVCAISIOTC_DIV']="";
    $_SESSION['APVCAISIOTC_STSAPV']="";
    
    //proses ca
    $_SESSION['PROSCAISI_TIPE']="";
    $_SESSION['PROSCAISI_KET']="";
    $_SESSION['PROSCAISI_TGL1']="";
    $_SESSION['PROSCAISI_TGL2']="";
    $_SESSION['PROSCAISI_KRY']="";
    $_SESSION['PROSCAISI_LVL']="";
    $_SESSION['PROSCAISI_DIV']="";
    $_SESSION['PROSCAISI_STSAPV']="";
    
    //apv service kendaraan
    $_SESSION['APVCASERVICE_TIPE']="";
    $_SESSION['APVCASERVICE_KET']="";
    $_SESSION['APVCASERVICE_TGL1']="";
    $_SESSION['APVCASERVICE_TGL2']="";
    $_SESSION['APVCASERVICE_KRY']="";
    $_SESSION['APVCASERVICE_LVL']="";
    $_SESSION['APVCASERVICE_DIV']="";
    $_SESSION['APVCASERVICE_STSAPV']="";
    
    //apv sewa
    $_SESSION['APVCASEWA_TIPE']="";
    $_SESSION['APVCASEWA_KET']="";
    $_SESSION['APVCASEWA_TGL1']="";
    $_SESSION['APVCASEWA_TGL2']="";
    $_SESSION['APVCASEWA_KRY']="";
    $_SESSION['APVCASEWA_LVL']="";
    $_SESSION['APVCASEWA_DIV']="";
    $_SESSION['APVCASEWA_STSAPV']="";
    
    //proses service
    $_SESSION['PROSCASERVICE_TIPE']="";
    $_SESSION['PROSCASERVICE_KET']="";
    $_SESSION['PROSCASERVICE_TGL1']="";
    $_SESSION['PROSCASERVICE_TGL2']="";
    $_SESSION['PROSCASERVICE_KRY']="";
    $_SESSION['PROSCASERVICE_LVL']="";
    $_SESSION['PROSCASERVICE_DIV']="";
    $_SESSION['PROSCASERVICE_STSAPV']="";
    
    //proses sewa
    $_SESSION['PROSCASEWA_TIPE']="";
    $_SESSION['PROSCASEWA_KET']="";
    $_SESSION['PROSCASEWA_TGL1']="";
    $_SESSION['PROSCASEWA_TGL2']="";
    $_SESSION['PROSCASEWA_KRY']="";
    $_SESSION['PROSCASEWA_LVL']="";
    $_SESSION['PROSCASEWA_DIV']="";
    $_SESSION['PROSCASEWA_STSAPV']="";
    
    
    //COA BUDGET BR
    $_SESSION['COABRBUDDIV']="";
    
    //master karyawan
    $_SESSION['FMSTJBT']="";
    $_SESSION['FMSTDIV']="";
    
    
    //pindah cabang data master
    $_SESSION['PIND_CABLAMA']="";
    $_SESSION['PIND_AREALAMA']="";
    $_SESSION['PIND_CABBARU']="";
    $_SESSION['PIND_AREABARU']="";
    
    
    //SPD
    $_SESSION['SPDTIPE']="";
    $_SESSION['SPDTGLTIPE']="";
    $_SESSION['SPDPERENTY1']="";
    $_SESSION['SPDPERENTY2']="";
    $_SESSION['SPDDIV']="";
    
    //SURAT PD
    $_SESSION['STPDTIPE']="";
    $_SESSION['STPDPERENTY1']="";
    $_SESSION['STPDPERENTY2']="";
    
    //SPD INC
    $_SESSION['STPDTIPEINC']="";
    $_SESSION['STPDPERENTYINC1']="";
    $_SESSION['STPDPERENTYINC2']="";
    
    
    //BM SBY
    $_SESSION['BMTIPE']="";
    $_SESSION['BMTGLTIPE']="";
    $_SESSION['BMPERENTY1']="";
    $_SESSION['BMPERENTY2']="";
    $_SESSION['BMDIV']="";
    
    //MASTER SPG GAJI
    $_SESSION['SPGMSTGJICAB']="";
    $_SESSION['SPGMSTGJTGL']="";
    
    //MASTER SPG GAJI CABANG
    $_SESSION['SPGMSTGJICABCAB']="";
    $_SESSION['SPGMSTGJTGLCAB']="";
    
    //SPG BR
    $_SESSION['SPGBRTIPE']="";
    $_SESSION['SPGBRTGLTIPE']="";
    $_SESSION['SPGBRPERENTY1']="";
    $_SESSION['SPGBRPERENTY2']="";
    $_SESSION['SPGBRCAB']="";
    
    //SPG HARI KERJA
    $_SESSION['SPGMSTHKCAB']="";
    $_SESSION['SPGMSTHKTGL']="";
    
    //SPG PROSES
    $_SESSION['SPGMSTPRSCAB']="";
    $_SESSION['SPGMSTPRSTGL']="";
    
    //SPG PROSES FIN
    $_SESSION['SPGMSTPRSFTIPE']="";
    $_SESSION['SPGMSTPRSFCAB']="";
    $_SESSION['SPGMSTPRSFTGL']="";
    
    //SPG PROSES MGR
    $_SESSION['SPGMSTPRSMTIPE']="";
    $_SESSION['SPGMSTPRSMCAB']="";
    $_SESSION['SPGMSTPRSMTGL']="";
    
    
    //REALISASI BUDGET
    $_SESSION['BMRTIPE']="";
    $_SESSION['BMRTGLTIPE']="";
    $_SESSION['BMRPERENTY1']="";
    $_SESSION['BMRPERENTY2']="";
    $_SESSION['BMRDIV']="";
    
    //SPD OTC
    $_SESSION['SPDTIPEOTC']="";
    $_SESSION['SPDTGLTIPEOTC']="";
    $_SESSION['SPDDIVOTC']="";
    $_SESSION['SPDPERENTYOTC1']="";
    $_SESSION['SPDPERENTYOTC2']="";
    
    
    //dir apv ca
    $_SESSION['DIRCAAPVTGL1']="";
    $_SESSION['DIRCAAPVTGL2']="";
    $_SESSION['DIRCAAPVKET']="";
    
    //dir apv blk
    $_SESSION['DIRBLKAPVTGL1']="";
    $_SESSION['DIRBLKAPVTGL2']="";
    $_SESSION['DIRBLKAPVKET']="";
    
    //dir apv rutin
    $_SESSION['DIRRTNAPVTGL1']="";
    $_SESSION['DIRRTNAPVTGL2']="";
    $_SESSION['DIRRTNAPVKET']="";
    
    //dir apv spd
    $_SESSION['DIRSPDAPVTGL1']="";
    $_SESSION['DIRSPDAPVTGL2']="";
    $_SESSION['DIRSPDAPVKET']="";
    
    //data budget
    $_SESSION['DBPERENTY1']="";
    $_SESSION['DBPERENTY2']="";
    
    //penempatan marketing
    $_SESSION['MKTTMPPERIODE']="";
    $_SESSION['MKTTMPREG']="";
    $_SESSION['MKTTMPCAB']="";
    $_SESSION['MKTTMPARE']="";
    
    //dana bank
    $_SESSION['DBTIPE']="";
    $_SESSION['DBTGLTIPE']="";
    $_SESSION['DBKENTRY1']="";
    $_SESSION['DBKENTRY2']="";
    
    
    //dana bank by finance
    $_SESSION['BNKDANATIPE']="";
    $_SESSION['BNKDANAKARY']="";
    $_SESSION['BNKDANATGL01']="";
    
    
    //fin ttd spd
    $_SESSION['FINSPDAPVTGL1']="";
    $_SESSION['FINSPDAPVTGL2']="";
    $_SESSION['FINSPDAPVKET']="";
    
    //FIN ttd by fin
    $_SESSION['FINTTDBSSTS']="";
    $_SESSION['FINTTDBSBLN1']="";
    $_SESSION['FINTTDBSBLN2']="";
    
    
    //pros data insentif
    $_SESSION['PITIPE']="";
    $_SESSION['PITGLTIPE']="";
    $_SESSION['PIPERENTY1']="";
    $_SESSION['PIPERENTY2']="";
    $_SESSION['PIDIVISI']="";
    $_SESSION['PIINCFROM']="";
    
    
    //closingan LK ethical
    $_SESSION['CLSETHPERIODE01']="";
    $_SESSION['CLSETHSTS']="";
    $_SESSION['CLSETHPILIHPROS']="";
    $_SESSION['CLSETHBTNPILIH']="";
    $_SESSION['CLSETHPILCA1']="";
    $_SESSION['CLSETHPILCA2']="";
    
    //BR DSS DSS CABANG
    $_SESSION['FDTBRCABTGL1']="";
    $_SESSION['FDTBRCABTGL2']="";
    
    //FIN PROSES CEK BR CABANG
    $_SESSION['PSFBRC_TGL1'] = "";
    $_SESSION['PSFBRC_TGL2'] = "";
    $_SESSION['PSFBRC_KET'] = "";
    
    //FIN PROSES BR
    $_SESSION['PSFBRCFIN_TGL1'] = "";
    $_SESSION['PSFBRCFIN_TGL2'] = "";
    $_SESSION['PSFBRCFIN_KET'] = "";
    
    
    // MARKETING APV BR DSS DCC CABANG
    $_SESSION['APVBRCAB_TGL1']="";
    $_SESSION['APVBRCAB_TGL2']="";
    $_SESSION['APVBRCAB_STSAPV']="";
    $_SESSION['APVBRCAB_KET']="";
    
    
    //SPG IMPORT
    $_SESSION['SPGMSTIMPCAB']="";
    $_SESSION['SPGMSTIMPTGL']="";
    $_SESSION['SPGMSTIMPSTS']="";
    $_SESSION['SPGMSTIMPPILIH']="";
    
    
    //TARGET PER DAERAH
    $_SESSION['MKSTRGDPERIODE']="";
    $_SESSION['MKSTRGDKRY']="";
    $_SESSION['MKSTRGDCAB']="";
    
    
    //IMPORT SALES
    $_SESSION['MSTIMPPERTPIL']="";
    $_SESSION['MSTIMPDISTPIL']="";
    $_SESSION['MSTIMPFOLDPIL']="";
    $_SESSION['MSTIMPFILEPIL']="";
    $_SESSION['MSTIMPKONEPIL']="";
    
    
    //UPLOAD TARGET CABANG
    $_SESSION['TGTUPDPERTPILCB']="";
    $_SESSION['TGTUPDCABPILCB']="";
    $_SESSION['TGTUPDFOLDPILCB']="";
    
    
    //TARGET CABANG
    $_SESSION['TGTTMLPERTPILCB']="";
    $_SESSION['TGTTMLCABPILCB']="";
    $_SESSION['TGTTMLAREPILCB']="";
    $_SESSION['TGTTMLBYPILCB']="";
    
    
    //UPLOAD TARGET AREA
    $_SESSION['TGTUPDPERTPIL']="";
    $_SESSION['TGTUPDCABPIL']="";
    $_SESSION['TGTUPDAREAPIL']="";
    $_SESSION['TGTUPDFOLDPIL']="";
    
    
    //TARGET AREA
    $_SESSION['TGTTMLPERTPIL']="";
    $_SESSION['TGTTMLCABPIL']="";
    $_SESSION['TGTTMLBYPIL']="";
    
    
    //TARGET CAB TAHUN
    $_SESSION['TGTUPDPERTPILCBT']="";
    $_SESSION['TGTUPDCABPILCBT']="";
    $_SESSION['TGTUPDREGPILCBT']="";
    $_SESSION['TGTUPDFOLDPILCBT']="";
    
    
    
    //SPD BPJS
    $_SESSION['SBPJSINPTIPE']="";
    $_SESSION['SBPJSINPBLN01']="";
    $_SESSION['SBPJSINPTGLAJU']="";
    
    //APV BARANG GIMICK PM
    $_SESSION['BRGPMAPVSTS']="";
    $_SESSION['BRGPMAPVBLN1']="";
    $_SESSION['BRGPMAPVBLN2']="";
    $_SESSION['BRGPMAPVAPVBY']="";
    
    //SJB
    $_SESSION['BRGSJBTGL1']="";
    $_SESSION['BRGSJBTGL2']="";
    $_SESSION['BRGSJBDIVI']="";
    $_SESSION['BRGSJBCABA']="";
    $_SESSION['BRGSJBKEYS']="";
    
    
    //OPNAME HO
    $_SESSION['BRGOPNHOTGL1']="";
    $_SESSION['BRGOPNHODIVP']="";
    
    
    //OPNAME CABANG
    $_SESSION['BRGOPNCABTGL1']="";
    $_SESSION['BRGOPNCABDIVP']="";
    $_SESSION['BRGOPNCABCABA']="";
    
    
    //UPLOAD STOCK
    $_SESSION['STCUPDPERTPIL']="";
    $_SESSION['STCUPDFOLDPIL']="";
    
    //UPLOAD STOCK
    $_SESSION['SSKASKECILCABT1']="";
    $_SESSION['SSKASKECILCABT2']="";
    
    //PROSES KAS KECIL CAB FIN
    $_SESSION['FPROSKKCSTS']="";
    $_SESSION['FPROSKKCBLN1']="";
    $_SESSION['FPROSKKCBLN2']="";
    $_SESSION['FPROSKKCAPVBY']="";
    
    //APV KAS KECIL CAB MKT
    $_SESSION['MAPVKKCSTS']="";
    $_SESSION['MAPVKKCBLN1']="";
    $_SESSION['MAPVKKCBLN2']="";
    $_SESSION['MAPVKKCAPVBY']="";
    
    
    //APV KLAIM DISC DISCOUNT
    $_SESSION['MAPVKDSTS']="";
    $_SESSION['MAPVKDBLN1']="";
    $_SESSION['MAPVKDBLN2']="";
    $_SESSION['MAPVKDAPVBY']="";
    
    
    //UPLOAD DOKTER
    $_SESSION['DOKUPNMFILE']="";
    $_SESSION['DOKUPIDCAB']="";
    
    //PR 
    $_SESSION['PCHSESITGL01']="";
    $_SESSION['PCHSESITGL02']="";
    
    //ISI VENDOR PR 
    $_SESSION['PCHSSIVSTS']="";
    $_SESSION['PCHSSIVTGL1']="";
    $_SESSION['PCHSSIVTGL2']="";
    $_SESSION['PCHSSIVPVBY']="";
    $_SESSION['PCHSSIVIDPR']="";
    $_SESSION['PCHSSIVIDPD']="";
    $_SESSION['PCHSSIVNMBG']="";
    
    //PO 
    $_SESSION['PCHSESITGLPO01']="";
    $_SESSION['PCHSESITGLPO02']="";
    
    //SALES MAPING CUSTOMER AREA
    $_SESSION['SLSMPCUSTTGL01']="";
    $_SESSION['SLSMPCUSTTGL02']="";
    
    //MONITORING USER
    $_SESSION['SSMONITUSERTGL1']="";
    $_SESSION['SSMONITUSERTGL2']="";
    $_SESSION['SSMONITUSERTIPE']="";
    
    
    //APPROVE BR RUTIN HO
    $_SESSION['BRRTNAPVSTS']="";
    $_SESSION['BRRTNAPVBLN1']="";
    $_SESSION['BRRTNAPVBLN2']="";
    $_SESSION['BRRTNAPVBY']="";
    
    
    //KS DATA APOTIK
    $_SESSION['KSDTAPTKRY']="";
    
    //KS DATA USER DR
    $_SESSION['KSDTDRKRY']="";
    
    //KS ISI KS
    $_SESSION['KSDTKSKRY']="";
    $_SESSION['KSDTKSDOK']="";
    $_SESSION['KSDTKSBLN01']="";
    $_SESSION['KSDTKSBLN02']="";
    
    //DPL OUTLET
    $_SESSION['DISCDPLCBOTL']="";
    
    
    //KS LIST DOKTER MR KARYAWAN
    $_SESSION['KSLSTDRMR']="";
    
    //KS LIST DOKTER BARU INPUT
    $_SESSION['KSLSTDRNEW']="";
    
    
    //UPLOAD BUDGET DIVISI
    $_SESSION['BGTUPDFIL']="";
    $_SESSION['BGTUPDTHN']="";
    $_SESSION['BGTUPDDVL']="";
    $_SESSION['BGTUPDKRY']="";
    $_SESSION['BGTUPDDPT']="";
    $_SESSION['BGTUPDCAB']="";
    
    
    //PINDAH CUSTOMER
    $_SESSION['PNDCSTNWIDCAB']="";
    $_SESSION['PNDCSTNWIDARA']="";
    $_SESSION['PNDCSTOLIDCAB']="";
    $_SESSION['PNDCSTOLIDARA']="";
    
    
    //CUSTOMER SDM
    $_SESSION['MAPCUSTIDCAB']="";
    $_SESSION['MAPCUSTIDARE']="";
    $_SESSION['MAPCUSTFILTE']="";
    
    
    //CUSTOMER DISTRIBUTOR
    $_SESSION['MAPCUSTDISIDCAB']="";
    $_SESSION['MAPCUSTDISIDARE']="";
    $_SESSION['MAPCUSTDISFILTE']="";
    
    
    //PEMBAGIAN SALES MANUAL
    $_SESSION['MAPCUSTBAGIDCAB']="";
    $_SESSION['MAPCUSTBAGIIDARE']="";
    $_SESSION['MAPCUSTBAGIFILTE']="";
    $_SESSION['MAPCUSTBAGIBULAN']="";
    
    
    //LIHAT KS
    $_SESSION['LHTKSMRID']="";
    $_SESSION['LHTKSCBID']="";
    $_SESSION['LHTKSDAPT']="";
    

    //LIST CUSTOMET OUTLET BARU
    $_SESSION['LSTCUSTNEWICAB']="";
    $_SESSION['LSTCUSTNEWIARE']="";
    

    //PROSES PR BY IT
    $_SESSION['PCHPRITSTS']="";
    $_SESSION['PCHPRITTGL1']="";
    $_SESSION['PCHPRITTGL2']="";
    

    //WEEKLY PLAN
    $_SESSION['WEKPLNCAB']="";
    $_SESSION['WEKPLNJBT']="";
    $_SESSION['WEKPLNKRY']="";
    $_SESSION['WEKPLNTGL']="";
    
    //REALISASI WEEKLY PLAN VISIT
    $_SESSION['RLWEKPLNCAB']="";
    $_SESSION['RLWEKPLNJBT']="";
    $_SESSION['RLWEKPLNKRY']="";
    $_SESSION['RLWEKPLNTGL']="";
    
    //REALISASI WEEKLY PLAN ACTIVITY
    $_SESSION['RLACWEKPLNCAB']="";
    $_SESSION['RLACWEKPLNJBT']="";
    $_SESSION['RLACWEKPLNKRY']="";
    $_SESSION['RLACWEKPLNTGL']="";

    //WEEKLY PLAN REALISASI
    $_SESSION['WEKPLNRLCAB']="";
    $_SESSION['WEKPLNRLJBT']="";
    $_SESSION['WEKPLNRLKRY']="";
    $_SESSION['WEKPLNRLTGL']="";

    //DKD MASTER DOKTER
    $_SESSION['DKDMSTDOKTCAB']="";
    $_SESSION['DKDMSTDOKTJBT']="";
    $_SESSION['DKDMSTDOKTKRY']="";
    $_SESSION['DKDMSTDOKTTGL']="";
    
    
    //FORM CUTI
    $_SESSION['FCUTICAB']="";
    $_SESSION['FCUTIJBT']="";
    $_SESSION['FCUTIKRY']="";
    $_SESSION['FCUTITGL01']="";
    $_SESSION['FCUTITGL02']="";
    
    
    //APPROVE CUTI
    $_SESSION['APVCUTISTS']="";
    $_SESSION['APVCUTIBLN1']="";
    $_SESSION['APVCUTIBLN2']="";
    $_SESSION['APVCUTIAPVBY']="";
    
    
    //PROSES CLOSING CUTI TAHUNAN
    $_SESSION['CLSCUTITHN']="";
    
    
    //PERMINTAAN DANA KLAIM DISCOUNT
    $_SESSION['SPDKDTGL01']="";
    $_SESSION['SPDKDTGL02']="";
?>
