<?php
require_once __DIR__ . '/../classes/vendor_class.php';

class VendorController
{
    private $vendor;

    public function __construct()
    {
        $this->vendor = new VendorClass();
    }

    public function fetch_vendor_ctr($customer_id)
    {
        return $this->vendor->getVendorByCustomerId($customer_id);
    }

    public function update_vendor_ctr($data)
    {
        return $this->vendor->updateVendorAndCustomer($data);
    }

    public function fetch_all_vendors_ctr()
    {
        return $this->vendor->getAllVendors();
    }
}

// procedural wrappers (for consistency with other controllers)
function fetch_vendor_ctr($customer_id)
{
    $c = new VendorController();
    return $c->fetch_vendor_ctr($customer_id);
}

function fetch_all_vendors_ctr()
{
    $c = new VendorController();
    return $c->fetch_all_vendors_ctr();
}

function update_vendor_ctr($data)
{
    $c = new VendorController();
    return $c->update_vendor_ctr($data);
}

?>
