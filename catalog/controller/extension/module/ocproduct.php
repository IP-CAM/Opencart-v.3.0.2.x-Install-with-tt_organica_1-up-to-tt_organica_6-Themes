<?php
class ControllerExtensionModuleOcProduct extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/ocproducts');
		
		$this->load->model('catalog/product');
		$this->load->model('extension/module/ocproduct');

		$this->load->model('tool/image');

		$data['products'] = array();

		$this->load->model('localisation/language');
		
		$data['code'] = $this->session->data['language'];

		$store_id = $this->config->get('config_store_id');
		
		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}
		
		$title2 = explode(' ',$setting['title_lang'][$data['code']]['title'],3); 
		$headingtitle = "";
		for($i=0;$i<count($title2);$i++){ 						
			$headingtitle .= $i>0 ? "<span class='word2'>".$title2[$i]."&nbsp;</span>" : "<span>".$title2[$i]."&nbsp;</span>";
		}
		$results = array();
		if($setting['option'] == 0) {
			if (!empty($setting['product'])) {
				
				$products = array_slice($setting['product'], 0, (int)$setting['limit']);
				foreach ($products as $product_id) {
					$product_info = $this->model_catalog_product->getProduct($product_id);
					if($product_info) {
						$results[] = $product_info;
					}
				}
			}
			
		} else if ($setting['option']==1){
			if($setting['productfrom']==1){
				$data['filter_category_id'] = $setting['cate_id'];
				$results = $this->model_catalog_product->getProducts($data);
				
			} else if($setting['productfrom']==0) {
				if (!empty($setting['productcate'])) {
					$products = array_slice($setting['productcate'], 0, (int)$setting['limit']);
					foreach ($products as $product_id) {
						$product_info = $this->model_catalog_product->getProduct($product_id);
						if($product_info) {
							$results[] = $product_info;
						}
					}
				}			
			} else {
				if ($setting['input_specific_product']==0){
					$data['products'] = array();
					$filter_data = array(
						'filter_category_id' => $setting['cate_id'],
						'sort'  => 'p.date_added',
						'order' => 'DESC',
						'start' => 0,
						'limit' => $setting['limit'],
					);
					$results = $this->model_catalog_product->getProducts($filter_data);
						
				} else if ($setting['input_specific_product']==1){
					$filter_data = array(
					'sort'  => 'pd.name',
					'order' => 'ASC',
					'start' => 0,
					'limit' => $setting['limit']
					);
					$results = $this->model_extension_module_ocproduct->getProductSpecialsCategory($filter_data, $setting['cate_id']);		
				} else if ($setting['input_specific_product']==2){
					$data['products'] = array();
					$results = $this->model_extension_module_ocproduct->getBestSellerProductsCategory($setting['limit'], $setting['cate_id']);				
				} else{
					$data['products'] = array();
					$results = $this->model_extension_module_ocproduct->getMostViewedProductsCategory($setting['limit'],  $setting['cate_id']);		
				}
			}
	
		} else {
			if ($setting['autoproduct']==0){
				$data['products'] = array();

				$filter_data = array(
					'sort'  => 'p.date_added',
					'order' => 'DESC',
					'start' => 0,
					'limit' => $setting['limit']
				);
				$results = $this->model_catalog_product->getProducts($filter_data);
					
			} else if ($setting['autoproduct']==1){
				$filter_data = array(
				'sort'  => 'pd.name',
				'order' => 'ASC',
				'start' => 0,
				'limit' => $setting['limit']
				);

				$results = $this->model_catalog_product->getProductSpecials($filter_data);
					
			} else if ($setting['autoproduct']==2){
					$data['products'] = array();

				$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
				
			} else if ($setting['autoproduct']==3){
					$data['products'] = array();

				$results = $this->model_catalog_product->getPopularProducts($setting['limit']);
				
			}else{
				$data['products'] = array();

				$results = $this->model_extension_module_ocproduct->getDealProducts($setting['limit']);		
				
			}		
		}

		$data['use_quickview'] = (int) $this->config->get('module_octhemeoption_quickview')[$store_id];
		$data['use_catalog'] = (int) $this->config->get('module_octhemeoption_catalog')[$store_id];

		$product_rotator_status = (int) $this->config->get('module_octhemeoption_rotator')[$store_id];

		/* Get new product */
		$this->load->model('catalog/product');

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);

		$new_results = $this->model_catalog_product->getProducts($filter_data);
		/* End */
		// echo "<pre>"; var_dump($results);die;
		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
				$date_end = false;
				if ($setting['countdown']){
					$date_end = $this->model_extension_module_ocproduct->getSpecialCountdown($result['product_id']);
					if ($date_end === '0000-00-00') {
						$date_end = false;
					}
				}
				/* Product Rotator */
				if($product_rotator_status == 1) {
				  $this->load->model('catalog/ocproductrotator');
				  $this->load->model('tool/image');

				  $product_id = $result['product_id'];
				  $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($product_id);

				  if($product_rotator_image) {
				    $rotator_image_width = $setting['width'];
				    $rotator_image_height = $setting['height'];
				    $data['rotator_image'] = $this->model_tool_image->resize($product_rotator_image, $rotator_image_width, $rotator_image_height);  
				  } else {
				    $data['rotator_image'] = false;
				  } 
				} else {
				  $data['rotator_image'] = false;       
				}
				/* End Product Rotator */

				$is_new = false;
				if ($new_results) { 
					foreach($new_results as $new_r) {
						if($result['product_id'] == $new_r['product_id']) {
							$is_new = true;
						}
					}
				}
				
				$c_words = 50;
				$result['name'] = strlen($result['name']) > $c_words ? substr($result['name'],0,$c_words)."..." : $result['name'];
				
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'date_end'    => $date_end,
					'is_new'      => $is_new,
					'rotator_image' => $data['rotator_image'],
					'manufacturer' => $result['manufacturer'],
					'manufacturers' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
				);
			}
		}
		$number_random = rand ( 1 , 1000 );
		$data['config_module'] = array(
				'name' => $setting['name'],
				'class' => $setting['class'],
				'type' => (int) $setting['type'],
				'slider' => (int) $setting['slider'],
				'auto' => (int) $setting['auto'],
				'loop' => (int) $setting['loop'],
				'margin' => (int) $setting['margin'],
				'nrow' => (int) $setting['nrow'],
				'items' => (int) $setting['items'],
				'time' => (int) $setting['time'],
				'speed' => (int) $setting['speed'],
				'row' => (int) $setting['row'],
				'navigation' => (int) $setting['navigation'],
				'pagination' => (int) $setting['pagination'],
				'desktop' => (int) $setting['desktop'],
				'tablet' => (int) $setting['tablet'],
				'mobile' => (int) $setting['mobile'],
				'smobile' => (int) $setting['smobile'],
				'title_lang' => $headingtitle,
				'description' => (int) $setting['description'],
				'countdown' => (int) $setting['countdown'],
				'rotator'  => (int) $setting['rotator'],
				'newlabel'  => (int) $setting['newlabel'],
				'salelabel'  => (int) $setting['salelabel'],
				'module_id' => $number_random
			);
			if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
				$data['module_description'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
				if ($data['module_description'] == '<p><br><p>') $data['module_description']= '';
		   }
			//echo '<pre>'; print_r($data['config_module']); die;

		// if ($data['products']) {
			return $this->load->view('extension/module/ocproduct', $data);
		// }
		
	}
	
	
}