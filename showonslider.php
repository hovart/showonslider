<?php

if (!defined('_PS_VERSION_'))
	exit;

class showOnSlider extends Module
{
	/* @var boolean error */
	protected $_errors = false;
	
	public function __construct()
	{
		$this->name = 'showonslider';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'ArT Hovakimyan';
		$this->need_instance = 0;

	 	parent::__construct();

		$this->displayName = $this->l('Show on Slider');
		$this->description = $this->l('Add checkbox in product and show on homepage slider');
	}
	
	public function install()
	{
		if (!parent::install() OR
			!$this->alterTable('add') OR			
			!$this->registerHook('displayHome') OR			
			!$this->registerHook('actionAdminControllerSetMedia') OR			
			!$this->registerHook('actionProductUpdate') OR
			!$this->registerHook('actionProductSave') OR
			!$this->registerHook('displayAdminProductsExtra'))
			return false;
		return true;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall() OR !$this->alterTable('remove'))
			return false;
		return true;
	}


	public function alterTable($method)
	{
		switch ($method) {
			case 'add':
				$sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product ADD `show_on_slider` INT(1) NOT NULL DEFAULT 0';
				break;
			
			case 'remove':
				$sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product DROP COLUMN `show_on_slider`';
				break;
		}
		
		if(!Db::getInstance()->Execute($sql))
			return false;
		return true;
	}

	public function prepareNewTab()
	{

		$this->context->smarty->assign(array(
			'product' => $this->getCustomField((int)Tools::getValue('id_product')),
			
		));

	}

	public function hookDisplayAdminProductsExtra($params)
	{
		
		$this->prepareNewTab();
		return $this->display(__FILE__, 'showonslider.tpl');
		
	}

	
	public function hookActionAdminControllerSetMedia($params)
	{

		// add necessary javascript to products back office
		if($this->context->controller->controller_name == 'AdminProducts')
		{
			$this->context->controller->addJS($this->_path.'/js/iphone-style-checkboxes.js');
			$this->context->controller->addJS($this->_path.'/js/script.js');
			$this->context->controller->addCSS($this->_path.'/css/style.css');
			$this->context->controller->addCSS($this->_path.'/css/jquery.bxslider.css');
		}

	}

	public function hookActionProductUpdate($params)
	{
		
		$id_product = (int)Tools::getValue('id_product');
		 	if(!Db::getInstance()->update('product', array('show_on_slider'=> Tools::getValue('show_on_slider')) ,'id_product = ' .$id_product ))
		 		$this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
		

	}
	public function hookDisplayHome($params)
	{
		$this->context->smarty->assign(array(
			'images' => $this->getProducts(),
			
		));
		
		$this->context->controller->addJS($this->_path.'/js/script.js');
		return $this->display(__FILE__, 'slider.tpl');
		

	}
	
	public function hookActionProductSave($params)
	{

	 	if(!Db::getInstance()->insert('product', array('show_on_slider'=> Tools::getValue('show_on_slider'))))
	 		$this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
		

	}

	public function getCustomField($id_product)
	{
		
		$result = Db::getInstance()->ExecuteS('SELECT show_on_slider FROM '._DB_PREFIX_.'product WHERE id_product = ' . (int)$id_product);
		if(!$result)
			return array();

		  foreach ($result as $field) {

		  	$fields['show_on_slider'] = $field['show_on_slider'];
		  }

		return $fields;
	}

	public function getProducts()
	{
		$result = Db::getInstance()->ExecuteS('SELECT id_product FROM '._DB_PREFIX_.'product WHERE show_on_slider = 1');
		if(!$result)
			return false;

		  foreach ($result as $field) {
		  	$id_product = $field['id_product'];
		  	$id_image = Product::getCover($id_product);
			$product =  new Product($id_product, 
               true,
               $this->context->language->id, 
               $this->context->shop->id);

		  
		  	$image_url = Link::getImageLink($product->link_rewrite, $id_image['id_image'], 'home_default');

		  	if($id_image){
		  		$products[] = array('image_url'=>'http://'.$image_url);
		  	}
		  	
		  	
		  }
		return $products;
	}

	
}
