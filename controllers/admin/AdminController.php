<?php
namespace sergiosgc\psmodule;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminController extends FrameworkBundleAdminController {
    public function __construct() {
        parent::__construct();
    }
    public function adminSampleRoute() {
        print($this->renderForm());
    }
}
