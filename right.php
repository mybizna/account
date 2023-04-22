<?php
/*
account,invoice,administrator,1,1,1,1
account,invoice,manager,1,1,1,1
account,invoice,supervisor,1,1,1,1
account,invoice,staff,1,1,1,0
account,invoice,registered,1,1,0,0
account,invoice,guest,1,0,0,0

account_invoice_edit=Yes

 */

$this->add_right("account", "invoice", "administrator", view:true, add:true, edit:true, delete:true);
$this->add_right("account", "invoice", "manager", view:true, add:true, edit:true, delete:true);
$this->add_right("account", "invoice", "supervisor", view:true, add:true, edit:true, delete:true);
$this->add_right("account", "invoice", "staff", view:true, add:true, edit:true);
$this->add_right("account", "invoice", "registered", view:true, add:true);
$this->add_right("account", "invoice", "guest", view:true, );
