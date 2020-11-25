<?php
if (!defined('_PS_VERSION_')) exit;

class Sergiosgc_PSModule extends Module {
    public static $singleton = null;
    public function __construct() {
        $this->name = 'sergiosgc_psmodule';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Sergiosgc';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Sample PSModule');
        $this->description = $this->l('Empty start project for a PSModule.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        static::$singleton = $this;
    }
    public function install()
    {
        return parent::install()
            && $this->installTab()
        ;
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallTab()
        ;
    }

    public function enable($force_all = false)
    {
        return parent::enable($force_all)
            && $this->installTab()
        ;
    }

    public function disable($force_all = false)
    {
        return parent::disable($force_all)
            && $this->uninstallTab()
        ;
    }
    
    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('PSModule');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'PSModule';
        $tab->route_name = 'psmodule_admin_sample_route';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('PSModule tab title');
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('PSModule');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitPSModuleModule')) == true) {
            $this->postProcess();
        }
        
        $helper = new \HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = \Context::getContext()->language->id;
        $helper->allow_employee_form_lang = \Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPSModuleModule';
        $helper->currentIndex = \Context::getContext()->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => [
                'PSMODULE_LIVE_MODE' => Configuration::get('PSMODULE_LIVE_MODE', true),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm([
            [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'PSMODULE_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                ],
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ],
            ]
        ]);
    }
    protected function postProcess()
    {
        foreach (['PSMODULE_LIVE_MODE'] as $key) Configuration::updateValue($key, Tools::getValue($key));
    }
}
