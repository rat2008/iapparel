<?php

// define('USE_PCONNECT', 'false');

$this_profile = (isset($_SESSION["profile"])? $_SESSION["profile"]: "iapparelintl");
$this_timezone = (isset($_SESSION["proClientTimeZone"])? $_SESSION["proClientTimeZone"]: "0");

// echo "<br/>This_profile: $this_profile << ";
if($this_timezone>0){
	define('glb_timezone', '+ INTERVAL '.$this_timezone.' HOUR');
}
else{
	$this_timezone = $this_timezone * -1;
	define('glb_timezone', '- INTERVAL '.$this_timezone.' HOUR');
}

switch($this_profile){
		case "lushbax":
			define('DB_SERVER', '127.0.0.1');
			define('DB_USERNAME', 'apparelezi_lbaxsupp');
			define('DB_PASSWORD', 'W3lcome123@789!!');
			define('DB_DATABASE', 'apparelezi_lushbax');
			define('DB_SERVER_log', '127.0.0.1');
			define('DB_USERNAME_log', 'apparelezi_lbaxmgrlog');
			define('DB_PASSWORD_log', 'H3ll0w0rld@_!!');
			define('DB_DATABASE_log', 'apparelezi_lushbaxlog');

			define('glb_profile','lushbax');
			define('glb_profile_logo','lushbax.png');//media/logo

			define('glb_binv_prefix','LINV');
			define('glb_binv_length','6');
			define('glb_order_prefix','LB');
			define('glb_NE_prefix','NE');
			define('glb_bulk_acc_move','MV.A');
			define('glb_bulk_fab_move','MV.F');
			define('glb_fab_po_type','7,25,26,27');
			define('glb_bulk_fab_po','LB.F');
			define('glb_bulk_acc_po','LB.A');
			define('glb_smpl_fab_po','SB.F');
			define('glb_smpl_acc_po','SB.A');
			define('glb_bulk_acc_issue','DO.AI');
			define('glb_leftover_acc_issue','DO.AL');
			define('glb_bulk_fab_issue','DO.FI');
			define('glb_leftover_fab_issue','DO.FL');
			define('glb_pre_fab_po','PB.F');
			define('glb_pre_acc_po','PB.A');
            define('glb_smpl_mo_po','MB.F');
			define('glb_prebook','PB');
			define('glb_fab_po_bill_addr','2');
			define('glb_fab_po_recAddr','1');
			define('glb_acc_po_bill_addr','1');
			define('glb_acc_po_recAddr','1');
			define('glb_companyprofileID','1');
			define('glb_order_start_no','0');

			define('glb_fin_company_list','F01,F3');
			define('glb_fin_def_company','F3');// order owner also use
			define('glb_main_manufactorer','F01');
			
			define('glb_shippingemail','UYMENGENG@LUSHBAX.COM');
			define('glb_mainproduct','BAG');
			define('glb_productname', "PRODUCT");//Bag
			
			define('glb_artdesc','Style Collection');
			define('glb_colordesc','Exclusive Tag#');
			define('glb_mingm','Min Thickness');
			define('glb_maxgm','Max Thickness');
			define('glb_gauge','');
			define('glb_yarn','Material Description');
			define('glb_topyarn','Material Description');
			define('glb_topbtmyarn','Material Description');
			define('glb_intwd','Cuttable Width');
			define('glb_extwd','Full Width');
			define('glb_sizeht','Thickness (MM)');
			define('glb_multiplier','Skin Size');
			define('glb_measurement','Grading Ratio');
			define('glb_trimtype','Material Type');
			define('glb_acccode','MM Code');
			define('glb_productdesc','Content');//Material Description
			define('glb_topqty','Product Approval Qty');
			define('glb_addpurchaseqty','Product Approval Semi Finished Qty');
			
			//fabric audit
			define('glb_defect_point', "Total Penalty Points");
			define('glb_inspect_qty', "Total Inspected MTR 总查米数");
			define('glb_cuttable_width', "Cuttable Width 宽度 (mtr)");
			define('glb_shipment_point', "Total Penalty Points at 100m2");
			define('glb_fabric_audit', "Textile, PVC, PU Audit");
			
            // APD
			// define('glb_cutting',$hdlang["Cutting"]);
			// define('glb_sewing',$hdlang["lusbax_sewing"]);
			// define('glb_finishing',$hdlang["lushbax_finishing"]);
			// define('glb_packing',$hdlang["Check & Pack"]);
			// define('glb_sewo',$hdlang["Assemble"]);
			// define('glb_sewf',$hdlang["Pre-assemble"]);
			// define('glb_lcutting',$hdlang["Leather Cutting"]);
			// define('glb_bcutting',$hdlang["Bind"]);
			// define('glb_scutting',$hdlang["apdword_d"]);

			// "Fabric" or "Material" (2023-12-20 w)
			define('glb_fabric_word',"Material");

            // quotation
			// define('glb_alltoone',$hdlang["all_to_one_lb"]);
			// define('glb_onetoone',$hdlang["one_to_one_lb"]);
			// define('glb_flat_cost',$hdlang["product_flat_cost"]);
			// define('glb_fob_Cost',$hdlang["product_fob_Cost"]);
			
			// carton calculator
			define('glb_fold_product_piece_length',"Product Piece Length");
			define('glb_fold_product_piece_width',"Product Piece Width");
			define('glb_fold_product_piece_height',"Product Piece Height");
            
			define('dir_folder','LBAX');
			define('CM_Prefix','LBAX');
			define('glb_home_bg_color','#08738A');

			define('glb_api_key','helloEziAPI');
            define('glb_quo_ManCountry','1');

            // quotation admin cost %
            define('glb_quo_rd_cost','0');
            define('glb_quo_bd_cost','1.5');
            define('glb_quo_pd_cost','3');
            define('glb_quo_ad_cost','1.5');
			
			// Production Sew/Assembly
            define('glb_sew','Assembly');
			define('glb_prod','Production');
			
			define('glb_local_currency','KHR');

			define('main_URL','https://www.apparelezi.com/lushbax');

			define('company_name_HQ','LUSH BAX PTE LTD');
			
			break;
		
		case "iapparelintl":
			define('DB_SERVER', '127.0.0.1');
			define('DB_USERNAME', 'apparele_superb');
			define('DB_PASSWORD', '31sEtIAtrOpIkA02');
			define('DB_DATABASE', 'apparelezi_dberp');
			define('DB_SERVER_log', '127.0.0.1');
			define('DB_USERNAME_log', 'apparelezi_logmanager');
			define('DB_PASSWORD_log', '9@&m3^+3&PZOlb109');
			define('DB_DATABASE_log', 'apparelezi_dberplog');

			define('glb_profile','iapparelintl');
			define('glb_profile_logo','IAIG.png');//media/logo
			
			define('glb_binv_prefix','BINV');
			define('glb_binv_length','6');
			define('glb_order_prefix','IA');
			define('glb_NE_prefix','NE');
			define('glb_bulk_acc_move','MV.A');
			define('glb_bulk_fab_move','MV.F');
			define('glb_fab_po_type','7,25,26,27');
			define('glb_bulk_fab_po','IA.F');
			define('glb_bulk_acc_po','IA.A');
			define('glb_smpl_fab_po','SA.F');
			define('glb_smpl_acc_po','SA.A');
			define('glb_bulk_acc_issue','DO.AI');
			define('glb_leftover_acc_issue','DO.AL');
			define('glb_bulk_fab_issue','DO.FI');
			define('glb_leftover_fab_issue','DO.FL');
            define('glb_smpl_mo_po','MO.F');
			define('glb_prebook','PB');
			define('glb_pre_fab_po','PB.F');
			define('glb_pre_acc_po','PB.A');
			define('glb_fab_po_bill_addr','3');
			define('glb_fab_po_recAddr','3');
			define('glb_acc_po_bill_addr','6');
			define('glb_acc_po_recAddr','8');
			define('glb_companyprofileID','4');
			define('glb_order_start_no','8000');

			define('glb_fin_company_list','G02,G00,F22,F141,F151,F138');
			define('glb_fin_def_company','G02'); // order owner also use
			define('glb_main_manufactorer',"G00','F151','F145");
			
			define('glb_shippingemail','KH_SHIPPING@IAPPARELINTL.COM');
			define('glb_mainproduct','GARMENT');
			define('glb_productname', "PRODUCT");//Garment
			
			define('glb_artdesc','Art Description');
			define('glb_colordesc','Color Description');
			define('glb_mingm','Min Weight Gm/m2');
			define('glb_maxgm','Max Weight Gm/m2');
			define('glb_yarn','Yarn');
			define('glb_gauge','Gauge');
			define('glb_topyarn','Top Yarn');
			define('glb_topbtmyarn','Top/Bottom Yarn');
			define('glb_intwd','Internal Width');
			define('glb_extwd','External Width');
			define('glb_sizeht','Size Height');
			define('glb_multiplier','Multiplier');
			define('glb_measurement','Measurement');
			define('glb_trimtype','Trim Type');
			define('glb_acccode','Acc Code');
			define('glb_productdesc','Content');
			define('glb_topqty','Top Request Qty');
			define('glb_addpurchaseqty','Additional Purchase Qty');
			
			//fabric audit
			define('glb_defect_point', "Total defect points");
			define('glb_inspect_qty', "Total inspected yds 总查码数");
			define('glb_cuttable_width', "Cuttable Width 宽度 (inches)");
			define('glb_shipment_point', "Shipment Points 出货点数");
			define('glb_fabric_audit', "Fabric Audit");
			
            // APD
            // define('glb_cutting',$hdlang["Cutting"]);
			// define('glb_sewing',$hdlang["Sewing"]);
			// define('glb_finishing',$hdlang["finishing"]);
			// define('glb_packing',$hdlang["ppack"]);
			// define('glb_sewo',$hdlang["sewo"]);
			// define('glb_sewf',$hdlang["sewf"]);
			// define('glb_lcutting',$hdlang["Leather Cutting"]);
			// define('glb_bcutting',$hdlang["Bind"]);
			// define('glb_scutting',$hdlang["apdword_d"]);
			
			// "Fabric" or "Material" (2023-12-20 w)
			define('glb_fabric_word',"Fabric");

            // quotation
			// define('glb_alltoone',$hdlang["all_to_one_ia"]);
			// define('glb_onetoone',$hdlang["one_to_one_ia"]);
			// define('glb_flat_cost',$hdlang["garment_flat_cost"]);
			// define('glb_fob_Cost',$hdlang["garment_fob_Cost"]);
			
			// carton calculator
			define('glb_fold_product_piece_length',"Fold Product Piece Length");
			define('glb_fold_product_piece_width',"Fold Product Piece Width");
			define('glb_fold_product_piece_height',"Fold Product Piece Height");

			define('dir_folder','IAIG');
			define('CM_Prefix','IAIG');
			define('glb_home_bg_color','#055E20');

			define('glb_api_key','helloEziAPI');
            define('glb_quo_ManCountry','12');

            // quotation admin cost %
            define('glb_quo_rd_cost','0');
            define('glb_quo_bd_cost','3');
            define('glb_quo_pd_cost','2');
            define('glb_quo_ad_cost','3');
			
			// Production Sew/Assembly
            define('glb_sew','Sew');
			// define('glb_prod',$hdlang["Sewing"]);

			define('glb_local_currency','KHR');

			define('main_URL','https://www.iapparelintl.apparelezi.com');

			define('company_name_HQ','I APPAREL INTERNATIONAL GROUP PTE LTD');

			break;	
		
	}
	
// Clear Table Required
// MPO = tblmpo_header, tblmpo_detail, tblmpo_detail_log, tblmpo_header_log, tblmpolog, tblmpo_remarks, tblmpo_savelog, tblmpo_mail

// APO = tblapo_header, tblapo_header_log, tblapo_detail, tblapo_detail_log, tblapo_mail, tblapo_remarks, tblapo_savelog, tblapolog

// Accessory Master = tblamaterial, tblasizecolor, tblatype, tblasubtype, tblacontent, tblprodamade

// Buyer Master = tblbuyer, tblconsignee, tblpayee, tblpoissue


?>