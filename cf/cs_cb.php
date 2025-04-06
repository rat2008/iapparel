<?php
class cs_cb
{
	public $conn;
	private $table_name = "cs_tblbuyer_cost_head";
	public $handle_misc;
	public $test = "0";

	public $LPODID = null;
	public $LPOHID = null;
	public $shipmentpriceID = null;
	public $THID = null;
	public $invoice_no = null;
	public $invoice_date = null;
	public $container_no = null;
	public $shipped_qty = null;
	public $total_cbm = null;
	public $total_carton = null;
	public $freight_charge = null;
	public $handling_charge = null;
	public $extra_charge = null;
	public $freight_untaxed_charge = null;
	public $handling_untaxed_charge = null;
	public $extra_untaxed_charge = null;
	public $untaxed_charge = null;
	public $flag_freight_charge = null;
	public $flag_handling_charge = null;
	public $flag_extra_charge = null;
	public $total_charge = null;
	public $total_charge_per_gmt = null;
	public $del = null;
	public $del_real = null;

	function __construct($conn, $misc)
	{
		$this->conn = $conn;
		$this->handle_misc = $misc;
	}

	public function insert()
	{
		$LPODID = $this->handle_misc->funcMaxID($this->table_name, "LPODID");
		$this->LPODID = $LPODID;

		$insert = $this->conn->prepare("INSERT INTO " . $this->table_name . " 
				(
				LPODID,
				LPOHID,
				shipmentpriceID,
				THID,
				invoice_no,
				truck_no,
				invoice_date,
				container_no,
				shipped_qty,
				total_cbm,
				total_carton,
				freight_charge,
				handling_charge,
				extra_charge,
				freight_untaxed_charge,
				handling_untaxed_charge,
				extra_untaxed_charge,
				untaxed_charge,
				flag_freight_charge,
				flag_handling_charge,
				flag_extra_charge,
				total_charge,
				total_charge_per_gmt,
				del
				)
				VALUES
				(
				:LPODID,
				:LPOHID,
				:shipmentpriceID,
				:THID,
				:invoice_no,
				:truck_no,
				:invoice_date,
				:container_no,
				:shipped_qty,
				:total_cbm,
				:total_carton,
				:freight_charge,
				:handling_charge,
				:extra_charge,
				:freight_untaxed_charge,
				:handling_untaxed_charge,
				:extra_untaxed_charge,
				:untaxed_charge,
				:flag_freight_charge,
				:flag_handling_charge,
				:flag_extra_charge,
				:total_charge,
				:total_charge_per_gmt,
				:del
				)
			");
		$insert->bindParam(":LPODID", $this->LPODID);
		$insert->bindParam(":LPOHID", $this->LPOHID);
		$insert->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$insert->bindParam(":THID", $this->THID);
		$insert->bindParam(":invoice_no", $this->invoice_no);
		$insert->bindParam(":truck_no", $this->truck_no);
		$insert->bindParam(":invoice_date", $this->invoice_date);
		$insert->bindParam(":container_no", $this->container_no);
		$insert->bindParam(":shipped_qty", $this->shipped_qty);
		$insert->bindParam(":total_cbm", $this->total_cbm);
		$insert->bindParam(":total_carton", $this->total_carton);
		$insert->bindParam(":freight_charge", $this->freight_charge);
		$insert->bindParam(":handling_charge", $this->handling_charge);
		$insert->bindParam(":extra_charge", $this->extra_charge);
		$insert->bindParam(":freight_untaxed_charge", $this->freight_untaxed_charge);
		$insert->bindParam(":handling_untaxed_charge", $this->handling_untaxed_charge);
		$insert->bindParam(":extra_untaxed_charge", $this->extra_untaxed_charge);
		$insert->bindParam(":untaxed_charge", $this->untaxed_charge);
		$insert->bindParam(":flag_freight_charge", $this->flag_freight_charge);
		$insert->bindParam(":flag_handling_charge", $this->flag_handling_charge);
		$insert->bindParam(":flag_extra_charge", $this->flag_extra_charge);
		$insert->bindParam(":total_charge", $this->total_charge);
		$insert->bindParam(":total_charge_per_gmt", $this->total_charge_per_gmt);
		$insert->bindParam(":del", $this->del);


		if ($insert->execute() == true) {
			return $LPODID;
		}
	}

	public function update()
	{
		$arr_col = [
			"LPOHID",
			"shipmentpriceID",
			"THID",
			"invoice_no",
			"truck_no",
			"invoice_date",
			"container_no",
			"shipped_qty",
			"total_cbm",
			"total_carton",
			"freight_charge",
			"handling_charge",
			"extra_charge",
			"freight_untaxed_charge",
			"handling_untaxed_charge",
			"extra_untaxed_charge",
			"untaxed_charge",
			"flag_freight_charge",
			"flag_handling_charge",
			"flag_extra_charge",
			"total_charge",
			"total_charge_per_gmt",
			"del",
			"del_real"
		];
		$LPODID = $this->LPODID;

		$set_update = "";
		for ($i = 0; $i < sizeof($arr_col); $i++) {
			if ($this->{$arr_col[$i]} !== null) {
				$set_update .= $arr_col[$i] . "='" . $this->{$arr_col[$i]} . "',";
			}
		}
		$set_update = rtrim($set_update, ',');

		$query = "UPDATE " . $this->table_name . " SET $set_update WHERE LPODID='$LPODID'";
		// echo "<pre>$query</pre>";
		$update_sql = $this->conn->prepare($query);
		$update_sql->execute();
	}

	public function clear()
	{
		$this->LPODID = null;
		$this->LPOHID = null;
		$this->shipmentpriceID = null;
		$this->THID = null;
		$this->invoice_no = null;
		$this->invoice_date = null;
		$this->container_no = null;
		$this->shipped_qty = null;
		$this->total_cbm = null;
		$this->total_carton = null;
		$this->freight_charge = null;
		$this->handling_charge = null;
		$this->extra_charge = null;
		$this->freight_untaxed_charge = null;
		$this->handling_untaxed_charge = null;
		$this->extra_untaxed_charge = null;
		$this->untaxed_charge = null;
		$this->flag_freight_charge = null;
		$this->flag_handling_charge = null;
		$this->flag_extra_charge = null;
		$this->total_charge = null;
		$this->total_charge_per_gmt = null;
		$this->del = null;
	}

	public function update_del_real()
	{
		// echo "UPDATE tblforwarder_po_detail SET del_real='1' WHERE LPOHID='$this->LPOHID' AND invoice_no='$this->invoice_no' AND container_no='$this->container_no' <BR><BR>";

		$update = $this->conn->prepare("UPDATE tblforwarder_po_detail SET del_real='1' WHERE LPOHID='$this->LPOHID' AND invoice_no='$this->invoice_no' AND container_no='$this->container_no'");

		return $update->execute();
	}

	public function select_buyer_po($invID)
	{
		// if($where==''){
		// 	$where='1';
		// }

		$query = "SELECT sp.ID as shipmentpriceID, sp.Orderno as orderno, sp.BuyerPO, sp.GTN_buyerpo, sp.GTN_styleno, od.proddesc
			FROM `tblbuyer_invoice_payment` bip 
			INNER JOIN tblbuyer_invoice_payment_category bic ON bic.invID = bip.ID
			INNER JOIN tblbuyer_invoice_payment_detail bipd ON bipd.invID = bic.invID AND bipd.BICID = bic.BICID
			INNER JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
			INNER JOIN tblorder od ON od.Orderno = sp.Orderno
			WHERE bip.ID = $invID AND bic.del=0 AND bipd.del=0 AND bipd.group_number>0 
			group by sp.ID";

		if ($this->test == "1") {
			// echo "<pre>$query</pre>";
		}

		$sel = $this->conn->prepare($query);
		$sel->execute();

		$row = $sel->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}

	public function select_shipping_marking($invID, $shipmentpriceID)
	{

		$query = "SELECT bipd.shipmentpriceID, bih.ht_code, bih.shipping_marking
			FROM tblbuyer_invoice_payment bip
			INNER JOIN tblbuyer_invoice_payment_category bic ON bic.invID = bip.ID
			INNER JOIN tblbuyer_invoice_payment_detail bipd ON bipd.invID = bic.invID AND bipd.BICID = bic.BICID
			INNER JOIN `tblbuyer_invoice_payment_hts` bih ON bih.invID = bic.invID AND bih.BICID = bipd.BICID AND bih.shipmentpriceID = bipd.shipmentpriceID
			WHERE bih.invID = $invID AND bic.del=0 AND bipd.del=0 AND bipd.group_number>0 AND bipd.shipmentpriceID = $shipmentpriceID
			group by bipd.shipmentpriceID, bih.garmentID";

		if ($this->test == "1") {
			// echo "<pre>$query</pre>";
		}

		$sel = $this->conn->prepare($query);
		$sel->execute();

		$row = $sel->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}

	public function select_po_color($invID, $shipmentpriceID)
	{

		$query = "SELECT  bic.BICID, sgc.colorID, bipd.qty, c.ColorName as color, sgc.group_number
			FROM `tblshipmentprice` sp 
			INNER JOIN tblbuyer_invoice_payment_detail bipd oN bipd.shipmentpriceID = sp.ID
			INNER JOIN tblbuyer_invoice_payment_category bic ON bic.BICID = bipd.BICID 
							AND bic.invID = bipd.invID
			INNER JOIN tblbuyer_invoice_payment bi ON bi.ID = bipd.invID
			INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = sp.ID  
						AND sgc.group_number = bipd.group_number   
						AND sgc.statusID=1
			INNER JOIN tblorder od ON od.Orderno = sp.Orderno
			INNER JOIN tblcolor c ON c.ID = sgc.colorID
			WHERE  bipd.del=0 AND bic.del=0 AND bipd.shipmentpriceID = $shipmentpriceID AND bipd.invID = $invID
			group by sp.ID, sgc.group_number, sgc.colorID 
			order by sp.ID desc";

		if ($this->test == "1") {
			// echo "<pre>$query</pre>";
		}

		$sel = $this->conn->prepare($query);
		$sel->execute();

		$row = $sel->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}

	public function select_cost_head($invID, $shipmentpriceID)
	{
		// if($where==''){
		// 	$where='1';
		// }

		$query = "SELECT *
            FROM `tblbuyer_invoice_payment_cost_head` 
            WHERE invID = $invID AND shipmentpriceID = $shipmentpriceID AND del=0";

		if ($this->test == "1") {
			// echo "<pre>$query</pre>";
		}

		$sel = $this->conn->prepare($query);
		$sel->execute();

		$row = $sel->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}

	public function select_cost_detail($INVCHID)
	{
		// if($where==''){
		// 	$where='1';
		// }

		$query = "SELECT *
            FROM `tblbuyer_invoice_payment_cost_detail` 
            WHERE INVCHID = $INVCHID AND del=0";

		if ($this->test == "1") {
			// echo "<pre>$query</pre>";
		}

		$sel = $this->conn->prepare($query);
		$sel->execute();

		$row = $sel->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}
}
