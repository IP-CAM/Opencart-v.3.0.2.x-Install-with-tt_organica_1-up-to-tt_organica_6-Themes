<?php
class ControllerExtensionModuleOCvermegamenu extends Controller {
	public function index($setting) { 
		$this->language->load('extension/module/ocvermegamenu');
      	$data['heading_title'] = $this->language->get('heading_title');
		$this->load->model('catalog/category'); 
		$this->load->model('tool/image');
		$this->load->model('vermegamenu/menu');
			$mobile = $this->model_vermegamenu_menu->getblockCategTree(); 
			
			$html  = null; 
			$html .='<ul id="vermegamenu-mobilemenu" class="ver_mobilemenu nav-collapse collapse">';

			foreach($mobile['children'] as $m) {
				//echo "<pre>"; print_r($m); echo "</pre>";
				if(!isset($m["name"])) $m["name"] = 'Root';
				//echo "<pre>"; print_r($m); echo "</pre>";
				$child_class = '';
				if(count($m['children'])>0) { $child_class = 'collapse1';} else {
					$child_class = 'no-close';
				}

				$html .='<li><span class=" button-view1 '.$child_class.'"><a href="'. $this->url->link('product/category', "path=".$m['id']).'">'.$m["name"].'</a></span>';

						if(isset($m['children'])) {
							//echo "<pre>"; print_r($m); echo "</pre>";
							$sub1 = $m['children'] ;
							$html .='<ul class="level2">';
								if(isset($sub1)) {
									foreach($sub1 as $child1) {
										if(count($child1['children'])>0) { $child_class = 'collapse1';} else {
											$child_class = 'no-close';
										}
										$html .='<li><span class="button-view2   '.$child_class.'"><a href="'. $this->url->link('product/category', "path=".$child1['id']).'">'.$child1["name"].'</a></span>';
										if(isset($child1['children'])) {
										  $html .='<ul class="level3">';

											$sub2 = $child1['children'] ;
											foreach($sub2 as $child2) {
												if(count($child2['children'])>0) { $child_class = 'collapse1';} else {
													$child_class = 'no-close';
												}
												$html .='<li><span class="  '.$child_class.'"><a href="'. $this->url->link('product/category', "path=".$child2['id']).'">'.$child2["name"].'</a></span></li>';
											}
										  $html .='</ul>';

										}
										$html .='</li>';
									}
								}
							$html .='</ul>';

						}
				$html .='</li>';
			}
			$html .= $this->model_vermegamenu_menu->getMobileLink($setting);
		$html .='</ul>'	; 
				
        
		$lang_id = (int)$this->config->get('config_language_id');
		$top_menu = $this->model_vermegamenu_menu->getMenuCustomerLink($lang_id,$setting) ;
		$data['vermegamenu'] = $top_menu;
		$data['top_offset'] = 20; 
		$data['effect'] = 1; 
		$data['_menu'] = $html; 
		
		$this->document->addScript('catalog/view/javascript/opentheme/vermegamenu/ver_menu.js');
		$this->document->addScript('catalog/view/javascript/opentheme/vermegamenu/ver_mobile_menu.js');
		if (file_exists(DIR_TEMPLATE . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/opentheme/vermegamenu/css/ocvermegamenu.css')) {
			$this->document->addStyle('catalog/view/theme/'.$this->config->get('theme_' . $this->config->get('config_theme') . '_directory').'/stylesheet/opentheme/vermegamenu/css/ocvermegamenu.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/opentheme/vermegamenu/css/ocvermegamenu.css');
		}
		return $this->load->view('extension/module/ocvermegamenu', $data);
	}
}
?>