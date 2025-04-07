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

	public function update_del_real()
	{
		// echo "UPDATE tblforwarder_po_detail SET del_real='1' WHERE LPOHID='$this->LPOHID' AND invoice_no='$this->invoice_no' AND container_no='$this->container_no' <BR><BR>";

		$update = $this->conn->prepare("UPDATE tblforwarder_po_detail SET del_real='1' WHERE LPOHID='$this->LPOHID' AND invoice_no='$this->invoice_no' AND container_no='$this->container_no'");

		return $update->execute();
	}

	public function select_buyer_po($invID)
	{
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

	public function select_total_carton_and_nnw($invID, $shipmentpriceID, $BICID)
	{
		$query = "SELECT  sum(ch.total_ctn) as total_ctn, sum(ch.net_net_weight * ch.total_ctn) as total_nnw, cd.group_number, ch.weight_unitID
			FROM `tblcarton_inv_payment_head` ch 
			INNER JOIN tblcarton_inv_payment_detail cd ON cd.CIHID = ch.CIHID
			WHERE ch.invID = $invID AND ch.del=0 AND ch.shipmentpriceID = $shipmentpriceID AND ch.BICID = $BICID AND cd.del=0 AND cd.group_number=1";

		if ($this->test == "1") {
			// echo "<pre>$query</pre>";
		}

		$sel = $this->conn->prepare($query);
		$sel->execute();

		$row = $sel->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}
}
