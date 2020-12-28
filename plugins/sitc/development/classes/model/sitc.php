<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 27/11/2014
 * Time: 12:16
 */
class Model_SITC extends Model implements Interface_Ideabubble
{
    /*** CLASS CONSTANTS ***/
    const FILE_PRODUCT          = 'Products.csv';
    const FILE_STOCK_AND_PRICE  = 'StockAndPrices.csv';
    const FILE_MANUFACTURER     = 'Manufacturers.csv';
    const FILE_CATEGORIES       = 'Categories.csv';
    const FILE_DISTRIBUTORS     = 'Distributors.csv';
	const FILE_PICTURES         = 'ProductPictures.csv';

    /*** PRIVATE MEMBER DATA ***/
    private static $sitc_files  = array(
        self::FILE_PRODUCT,
        self::FILE_STOCK_AND_PRICE,
        self::FILE_MANUFACTURER,
        self::FILE_CATEGORIES,
        self::FILE_DISTRIBUTORS,
		self::FILE_PICTURES
    );
    private static function get_sitc_path(){
        $project_media_folder = Kohana::$config->load('config')->project_media_folder ? Kohana::$config->load('config')->project_media_folder : 'media';
        $sitc_dir = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$project_media_folder.'/sitc/';

        if( ! is_dir($sitc_dir)){
            mkdir($sitc_dir);
        }
        return $sitc_dir;
    }

    /**
     * @param $data
     * @return $this
     */
    public function set($data)
    {
        foreach($data as $key=>$item)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $item;
            }
        }

        return $this;
    }

    /**
     * @param $autoload
     * @return array
     */
    public function get($autoload)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    public static function create($id = null)
    {
        return new self($id);
    }

    public static function get_all_products(){
        return DB::select()
            ->from('plugin_sict_product')
            ->join('plugin_sict_stock_and_price')
            ->on('plugin_sict_product.product_id', '=', 'plugin_sict_stock_and_price.product_id')
            ->execute();
    }

	public static function update_from_ftp()
	{
		set_time_limit(0);
		proc_nice(19);
		DB::query(Database::SELECT, "SELECT GET_LOCK('sitc_csv_import', 1000)")->execute();
		try{
            Database::instance()->begin();
			Model_SITC::upload_files_from_ftp();
			Model_SITC::update_sitc_categories();
			Model_SITC::update_sitc_distributors();
			Model_SITC::update_sitc_manufacturer();
			Model_SITC::update_sitc_product();
			Model_SITC::update_sitc_stock_and_price();
			Model_SITC::transfer_to_product_table();
            Database::instance()->commit();
            DB::query(Database::SELECT, "SELECT RELEASE_LOCK('sitc_csv_import')")->execute();
		} catch(Exception $exc){
            Database::instance()->rollback();
            DB::query(Database::SELECT, "SELECT RELEASE_LOCK('sitc_csv_import')")->execute();
            throw $exc;
		}
	}

    public static function transfer_to_product_table()
    {
		DB::query(null, "SET foreign_key_checks=0")->execute(); // to avoid parent id cyclic reference errors
        foreach (self::get_all_products() as $sitc_product) {
            // save product
            $data = array(
                'title' => $sitc_product['name'],
                'url_title' => self::format_url_title($sitc_product['name'], $sitc_product['sku']),
                'category_id' => null,
                'price' => $sitc_product['price'],
                'disable_purchase' => 0,
                'display_price' => 1,
                'offer_price' => '',
                'display_offer' => 0,
                'featured' => 0,
                'brief_description' => '',
                'description' => $sitc_product['description'],
                'product_code' => $sitc_product['sku'],
                'ref_code' => '',
                'weight' => '',
                'seo_title' => '',
                'seo_keywords' => '',
                'seo_description' => '',
                'seo_footer' => '',
                'postal_format_id' => 1,
                'out_of_stock' => 0, //$sitc_product['stock'],
                'out_of_stock_msg' => '',
                'size_guide' => '',
                'document' => '',
                'min_width' => '',
                'min_height' => '',
                'order' => '',
                'publish' => 1,
                'quantity_enabled' => $sitc_product['stock'],
                'quantity' => $sitc_product['stock'],
                'builder' => 0,
                'matrix' => 'null',
                'over_18' => 0,
                'sign_builder_layers' => '[]',
				'manufacturer_id' => $sitc_product['manufacturer_id'],
				'distributor_id' => $sitc_product['distributor_id'],
            );
			if($existing_product = DB::select()->from('plugin_products_product')->where('product_code','=',$data['product_code'])->execute()->as_array()){
				if($existing_product[0]['title'] != $sitc_product['name']
                    ||
                    $existing_product[0]['price'] != $sitc_product['price']
                    ||
                    $existing_product[0]['description'] != $sitc_product['description']
                    ||
                    $existing_product[0]['quantity_enabled'] != $sitc_product['stock']
                    ||
                    $existing_product[0]['quantity'] != $sitc_product['stock']){
					echo "Update product " . $data['product_code'] . "\n";
					$existing_product[0]['title'] = $sitc_product['name'];
					$existing_product[0]['price'] = $sitc_product['price'];
					$existing_product[0]['description'] = $sitc_product['description'];
					$existing_product[0]['quantity_enabled'] = $sitc_product['stock'];
					$existing_product[0]['quantity'] = $sitc_product['stock'];
					$existing_product[0]['date_modified'] = date('Y-m-d H:i:s');
					DB::update('plugin_products_product')->set($existing_product[0])->where('product_code', '=', $existing_product[0]['product_code'])->execute();
				}
				$product_id = $existing_product[0]['id'];
			} else {
				echo "Insert product " . $data['product_code'] . "\n";
				$id = DB::insert('plugin_products_product', array_keys($data))->values($data)->execute();
				$product_id = $id[0];
			}
			DB::delete('plugin_sict_product_relation')->where('product_id', '=', $product_id);
			DB::insert('plugin_sict_product_relation', array('product_id', 'sict_product_id'))->values(array('product_id' => $product_id, 'sict_product_id' => $sitc_product['product_id']))->execute();
        }
		DB::query(null, "DELETE plugin_products_product_categories
							FROM plugin_products_product_categories
								INNER JOIN plugin_products_product on plugin_products_product_categories.product_id = plugin_products_product.id
								INNER JOIN plugin_sict_product on plugin_products_product.product_code = plugin_sict_product.sku")->execute();
		DB::query(null, "INSERT INTO plugin_products_product_categories
							(product_id, category_id)
							(SELECT plugin_products_product.id, plugin_sict_product_category.category_id
									FROM plugin_products_product
										INNER JOIN plugin_sict_product on plugin_products_product.product_code = plugin_sict_product.sku
										INNER JOIN plugin_sict_product_category on plugin_sict_product.product_id = plugin_sict_product_category.sitc_id)")->execute();
        DB::query(null, "UPDATE plugin_products_product
								INNER JOIN plugin_sict_product on plugin_products_product.product_code = plugin_sict_product.sku
							SET plugin_products_product.publish = 1
							WHERE plugin_sict_product.id IS NOT NULL")->execute(); // publish products that's in sitc table
        DB::query(null, "UPDATE plugin_products_product
								LEFT JOIN plugin_sict_product on plugin_products_product.product_code = plugin_sict_product.sku
							SET plugin_products_product.publish = 0
							WHERE plugin_sict_product.id IS NULL")->execute(); // unpublish products that's no longer in sitc table
		DB::query(null, "SET foreign_key_checks=1")->execute();
    }

    public static function update_sitc_product()
    {
        DB::delete('plugin_sict_product_relation')->execute();
        DB::delete('plugin_sict_product')->execute();
		DB::delete('plugin_sict_product_category')->execute();
        $file_path  = self::get_sitc_path().self::FILE_PRODUCT;
        $table_name = 'plugin_sict_product';
        $file = fopen($file_path,"r");
        $row = 0;
        while (($data = fgetcsv($file, 0, "|")) !== FALSE) {
            // skip first row, which contains column title
            if($row == 0) {
                $row++;
                continue;
            }
			$sitc_product = array('product_id' => $data[1],
									'sku' => $data[3],
									'name' => $data[4],
									'manufacturer_id' => $data[5],
									'img_url' => $data[7] ? $data[7] : $data[14], //some products has medium image url but main image url
									'thumb_url' => $data[14],
									'description' => $data[10],
									'specification' => $data[9]);
			if(count(DB::select()->from('plugin_sict_product')->where('sku','=',$sitc_product['sku'])->execute()->as_array()) == 0){
				//echo "Insert sitc " . $sitc_product['sku'] . "\n";
	            DB::insert('plugin_sict_product', array_keys($sitc_product))->values($sitc_product)->execute();
			}
			DB::insert('plugin_sict_product_category',array('sitc_id', 'category_id'))->values(array('sitc_id' => $data[1], 'category_id' => $data[6]))->execute();
        }
        fclose($file);
    }

    public static function update_sitc_stock_and_price()
    {
        $file_path  = self::get_sitc_path().self::FILE_STOCK_AND_PRICE;
        $table_name = 'plugin_sict_stock_and_price';
        $column_name = array(
            'product_id',
            'stock',
            'price',
            'cost',
            'distributor_id',
        );
        // column from csv file count form 0
        $column_number = array(
            0, // product_id
            1, // stock
            2, // price
            3, // cost
            4, // distributor_id
        );

        self::save_in_db($file_path, $table_name, $column_number, $column_name);
    }

    public static function get_distributor_list()
	{
		$distributors = array();
		foreach(DB::select()->from('plugin_sict_distributors')->execute()->as_array() as $distributor){
			$distributors[$distributor['id']] = $distributor['name'];
		}
		return $distributors;
	}
	
	public static function update_sitc_distributors()
    {
        $file_path  = self::get_sitc_path().self::FILE_DISTRIBUTORS;
        $table_name = 'plugin_sict_distributors';
        $file = fopen($file_path,"r");
        $row = 0;
        while (($data = fgetcsv($file, 0, "|")) !== FALSE) {
            // skip first row, which contains column title
            if($row == 0) {
                $row++;
                continue;
            }
			$distributor = array('id' => $data[0],
								'name' => $data[1],
								'website' => $data[2]);
			if($existing_distributor = DB::select()->from('plugin_sict_distributors')->where('id','=',$distributor['id'])->execute()->as_array()){
				if($existing_distributor[0]['website'] != $distributor['website']){
					echo "Update distributor " . $distributor['name'] . "\n";
					DB::update('plugin_sict_distributors')->set(array('website' =>$distributor['website']))->execute();
				} else {
					echo "Skip distributor " . $distributor['name'] . "\n";
				}
			} else {
				echo "Insert distributor " . $distributor['name'] . "\n";
	            DB::insert('plugin_sict_distributors', array_keys($distributor))->values($distributor)->execute();
			}
        }
        fclose($file);
    }

    public static function get_manufacturer_list()
	{
		$manufacturers = array();
		foreach(DB::select()->from('plugin_sict_manufacturer')->execute()->as_array() as $manufacturer){
			$manufacturers[$manufacturer['id']] = $manufacturer['name'];
		}
		return $manufacturers;
	}
	
	public static function update_sitc_manufacturer()
    {
        $file_path  = self::get_sitc_path().self::FILE_MANUFACTURER;
        $table_name = 'plugin_sict_manufacturer';
        $file = fopen($file_path,"r");
        $row = 0;
        while (($data = fgetcsv($file, 0, "|")) !== FALSE) {
            // skip first row, which contains column title
            if($row == 0) {
                $row++;
                continue;
            }
			$manufacturer = array('id' => $data[0],
									'manufacturer_id' => $data[0],
									'name' => $data[1],
									'img_url' => $data[2]);
			if($existing_manufacturer = DB::select()->from('plugin_sict_manufacturer')->where('name','=',$manufacturer['name'])->or_where('manufacturer_id','=',$manufacturer['manufacturer_id'])->execute()->as_array()){
				if($existing_manufacturer[0]['img_url'] != $manufacturer['img_url'] || $existing_manufacturer[0]['name'] != $manufacturer['name']){
					echo "Update manufacturer " . $manufacturer['name'] . "\n";
					DB::update('plugin_sict_manufacturer')->set(array('img_url' =>$manufacturer['img_url'], 'name' => $manufacturer['name']))->where('manufacturer_id','=',$manufacturer['manufacturer_id'])->execute();
				} else {
					echo "Skip manufacturer " . $manufacturer['name'] . "\n";
				}
			} else {
				echo "Insert manufacturer " . $manufacturer['name'] . "\n";
	            DB::insert('plugin_sict_manufacturer', array_keys($manufacturer))->values($manufacturer)->execute();
			}
        }
        fclose($file);
    }

    public static function get_category_list()
	{
		$categories = array();
		foreach(DB::select()->from('plugin_products_category')->execute()->as_array() as $category){
			$categories[$category['id']] = $category['category'];
		}
		return $categories;
	}

    public static function update_sitc_categories()
    {
        $file_path  = self::get_sitc_path().self::FILE_CATEGORIES;
		DB::query(null, "SET foreign_key_checks=0")->execute(); // to avoid parent id cyclic reference errors
		$file = fopen($file_path,"r");
        $row = 0;
        while (($data = fgetcsv($file, 0, "|")) !== FALSE) {
            // skip first row, which contains column title
            if($row == 0) {
                $row++;
                continue;
            }
			$category = array('id' => $data[0],
								'parent_id' => $data[1] ? $data[1] : null,
								'category' => $data[2],
								'order' => $data[3],
								'publish' => $data[4] == 'False' ? 1 : 0,
								'image' => $data[7],
								'date_entered' => date('Y-m-d H:i:s'),
								'date_modified' => date('Y-m-d H:i:s'),
								'deleted' => 0);
			if(DB::select(array(DB::expr('count(*)'), 'cnt'))->from('plugin_products_category')->where('id','=',$category['id'])->execute()->get('cnt') > 0){
				echo "Existing category " . $category['category'] . "\n";
			} else {
				echo "Insert category " . $category['category'] . "\n";
	            DB::insert('plugin_products_category', array_keys($category))->values($category)->execute();
			}
        }
        fclose($file);
		DB::query(null, "SET foreign_key_checks=1")->execute();
    }

    private static function save_in_db($file_path, $table_name, $column_number, $column_name, $column_filed = array(), $value_filed = array())
    {
        self::truncate_table($table_name);
        $file = fopen($file_path,"r");
        $row = 0;
        $column_name = array_merge($column_name, $column_filed);

        while (($data = fgetcsv($file, 0, "|")) !== FALSE) {
            // skip first row, which contains column title
            if($row == 0) {
                $row++;
                continue;
            }
            $values = array();
            $num = count($data);

            for ($column = 0; $column < $num; $column++) {
                if (in_array($column, $column_number)) {
                    $values[] = $data[$column];
                }
            }
            // add to the end value field, column name added in the start of method
            $values = array_merge($values, $value_filed);
            
            DB::insert($table_name, $column_name)->values($values)->execute();
        }
        fclose($file);
    }

    private static function truncate_table($table_name){
        try {
            DB::delete($table_name)->execute(); // delete all previous data
        } catch (Exception $e) {
            $entries = DB::select()->from($table_name)->execute();
            foreach ($entries as $entry){
                DB::delete($table_name)->where('id', '=', $entry['id'])->execute();
            }
        }
    }

    public static function upload_files_from_ftp()
    {
        $ftp_server = Settings::instance()->get('sict_ftp');
        $ftp_user = Settings::instance()->get('sict_user_name');
        $ftp_pass = Settings::instance()->get('sict_password');

        // try to connect
        $conn_id = ftp_connect($ftp_server) or die("Cannot connect to ".$ftp_server);

        // try to login
        ftp_login($conn_id, $ftp_user, $ftp_pass) or die("Cannot login by ".$ftp_user);

        // try to save files
        foreach (self::$sitc_files as $file_name) {
            $local_file = self::get_sitc_path().$file_name;
            try {
                ftp_get($conn_id, $local_file, $file_name, FTP_BINARY);
            } catch (Exception $e) {
                die("Cannot copy file $file_name, from ftp ".$ftp_server);
            }
        }

        // close the connection
        ftp_close($conn_id);
    }

	///ProductPictures.csv does not seem to contain pictures of most of the products.
	///Keep this incase it's needed in the future.
	/*
	public static function download_sitc_pictures()
	{
		$project_media_folder = Kohana::$config->load('config')->project_media_folder ? Kohana::$config->load('config')->project_media_folder : 'media';
		$picture_folder = $project_media_folder . '/products';
		$file_path  = self::get_sitc_path().self::FILE_PICTURES;
        $table_name = 'plugin_sict_product_pictures';
        $file = fopen($file_path,"r");
        $row = 0;
        while (($data = fgetcsv($file, 0, "|")) !== FALSE) {
            // skip first row, which contains column title
            if($row == 0) {
                $row++;
                continue;
            }
			$sitc_picture = array('sitc_id' => $data[0],
									'picture_url' => $data[1],
									'thumb_url' => $data[2]);
									
			if(count(DB::select()->from('plugin_sict_product_pictures')->where('sitc_id', '=', $sitc_picture['sitc_id'])->and_where('picture_url', '=', $sitc_picture['picture_url'])->execute()->as_array()) == 0){
				DB::insert('plugin_sict_product_pictures',array_keys($sitc_picture))->values($sitc_picture)->execute();
				$filename = basename($sitc_picture['picture_url']);
				$picture_folder_final = $picture_folder . '/' . substr($filename, 0, 3) . '/' . substr($filename, 3, 3);
				if(!file_exists($picture_folder_final)){
					mkdir($picture_folder_final, 0777, true);
				}
				$filename = $picture_folder_final . '/' . $filename;

				if(!file_exists($filename)){
					try{
						echo "Download product picture " . $sitc_picture['picture_url'] . " => " . $filename . "\n";
						file_put_contents($filename, file_get_contents($sitc_picture['picture_url']));
					} catch(Exception $exc){
						echo "Unable to download " . $sitc_picture['picture_url'] . "\n";
					}
				}
				$product_id = DB::select('plugin_products_product.id')->from('plugin_products_product')->join('plugin_sict_product')->on('plugin_products_product.product_code', '=', 'plugin_sict_product.sku')->where('plugin_sict_product.product_id', '=', $sitc_picture['sitc_id'])->execute()->get('id');
				if($product_id){
					$image_exists = DB::select('plugin_products_product_images.id')
										->from('plugin_products_product_images')
										->where('plugin_products_product_images.product_id', '=', $product_id)
										->and_where('plugin_products_product_images.file_name', '=', $filename)
										->execute()
										->get('id');
					if(!$image_exists){
						DB::insert('plugin_products_product_images', array('product_id', 'file_name'))->values(array('product_id' => $product_id, 'file_name' => $filename))->execute();
					}
				}
			}
        }
        fclose($file);
	}
	*/
	
	///download pictures in plugin_sict_product.img_url and plugin_sict_product.thumb_url if they are links to http://images.stockinthechannel.com.
	///
	public static function download_sitc_pictures()
	{
		$project_media_folder = Kohana::$config->load('config')->project_media_folder ? Kohana::$config->load('config')->project_media_folder : 'media';
		$picture_folder = $project_media_folder . '/products';
		$sitc_products_with_images = DB::select()->from('plugin_sict_product')->where('img_url', '<>', '')->execute()->as_array();
		foreach($sitc_products_with_images as $i => $sitc_product){
			if(strpos($sitc_product['img_url'], 'images.stockinthechannel.com')){
				$download_failed = false;
				$filename = basename($sitc_product['img_url']);
				$picture_folder_final = $picture_folder . '/' . substr($filename, 0, 3) . '/' . substr($filename, 3, 3);
				if(!file_exists($picture_folder_final)){
					mkdir($picture_folder_final, 0777, true);
				}
				$filename = $picture_folder_final . '/' . $filename;

				if(!file_exists($filename)){
					try{
						echo "Download product picture " . $sitc_product['img_url'] . " => " . $filename . "\n";
						file_put_contents($filename, file_get_contents($sitc_product['img_url']));
					} catch(Exception $exc){
						echo "Unable to download " . $sitc_product['img_url'] . "\n";
						$download_failed = true;
					}
				}
				$sitc_product['img_url'] = '/' . $filename;
				
				$thumb_filename = basename($sitc_product['thumb_url']);
				$thumb_picture_folder_final = $picture_folder . '/' . substr($thumb_filename, 0, 3) . '/' . substr($thumb_filename, 3, 3);
				if(!file_exists($thumb_picture_folder_final)){
					mkdir($thumb_picture_folder_final, 0777, true);
				}
				$thumb_filename = $thumb_picture_folder_final . '/' . $thumb_filename;

				if(!file_exists($thumb_filename)){
					try{
						echo "Download product picture " . $sitc_product['thumb_url'] . " => " . $thumb_filename . "\n";
						file_put_contents($thumb_filename, file_get_contents($sitc_product['thumb_url']));
					} catch(Exception $exc){
						echo "Unable to download " . $sitc_product['thumb_url'] . "\n";
						$download_failed = true;
					}
				}
				$sitc_product['thumb_url'] = '/' . $thumb_filename;
				
				if(!$download_failed){
					DB::update('plugin_sict_product')->set($sitc_product)->where('id', '=', $sitc_product['id'])->execute();
					$product_id = DB::select('plugin_products_product.id')->from('plugin_products_product')->join('plugin_sict_product')->on('plugin_products_product.product_code', '=', 'plugin_sict_product.sku')->where('plugin_sict_product.product_id', '=', $sitc_product['product_id'])->execute()->get('id');
					if($product_id){
						$image_exists = DB::select('plugin_products_product_images.id')
											->from('plugin_products_product_images')
											->where('plugin_products_product_images.product_id', '=', $product_id)
											->and_where('plugin_products_product_images.file_name', '=', $filename)
											->execute()
											->get('id');
						if(!$image_exists){
							DB::insert('plugin_products_product_images', array('product_id', 'file_name'))->values(array('product_id' => $product_id, 'file_name' => $filename))->execute();
						}
					}
				}
			}
		}
	}

	public static function render_category($class, $search_data){
        $view = '';
        $result = DB::select('id', 'category')->from('plugin_products_category')->where('parent_id', 'is', null)->execute();
        foreach($result as $row){
            $sub_categories = self::render_sub_category($class, 'sub-category', $row['id']);
            $view .= self::render_li_for_menu($class, $search_data, $row['id'], $row['category'], $sub_categories);
        }
        return $view;
    }

    public static function render_sub_category($class, $search_data, $parent_id){
        $view = '';
        $result = DB::select('id', 'category')->from('plugin_products_category')->where('parent_id', '=', $parent_id)->execute();
        if($result->count()){
            $view .= '<span class="display-sub-category"></span>';
            $view .= '<ul class="sub-category">';
            foreach($result as $row){
                $view .= self::render_li_for_menu($class, $search_data, $row['id'], $row['category']);
            }
            $view .= '</ul>';
        }
        return $view;
    }

    public static function render_brands($class, $search_data){
        $view = '';
        $result = DB::select('manufacturer_id', 'name')->from('plugin_sict_manufacturer')->execute();
        foreach($result as $row){
            $view .= self::render_li_for_menu($class, $search_data, $row['manufacturer_id'], $row['name']);
        }
        return $view;
    }

    public static function render_distributors($class, $search_data){
        $view = '';
        $result = DB::select('id', 'name')->from('plugin_sict_distributors')->execute();
        foreach($result as $row){
            $view .= self::render_li_for_menu($class, $search_data, $row['id'], $row['name']);
        }
        return $view;
    }

    private static function render_li_for_menu($class, $search_data, $id, $value, $ul = ''){
        return
            '<li class="'.$class.'" data-id="'.$id.'" data-search="'.$search_data.'" data-value="'.$value.'">'.
                '<span class="filter-checkbox"></span>'.
                $value.$ul.
            '</li>';
    }

    public static function search ($limit = 12, $featured_only = FALSE)
    {
        $category       = Request::initial()->query('category');
        $sub_category   = Request::initial()->query('sub-category');
        $brand          = Request::initial()->query('brand');
        $distributor    = Request::initial()->query('distributor');
        $price          = Request::initial()->query('price');

        $current_page   = Request::initial()->query('page');
        $price          = $price ? $price : 9999;
        $sub_category   = $sub_category ? $sub_category : array();
        $category       = $category ? $category : array();
        $brand          = $brand ? $brand : array();
        $distributor    = $distributor ? $distributor : array();
        $items_per_page = 12;

        $q = DB::select(
            array('product.price', 'price'),
            array('product.title', 'title'),
			'product.url_title',
            array('product.display_price', 'display_price'),
            array('product.disable_purchase', 'disable_purchase'),
            array('product.offer_price', 'offer_price'),
            array('product.display_offer', 'display_offer'),
            array('sict_product.thumb_url', 'thumb_url')
        )
            ->from(array('plugin_sict_product', 'sict_product'))
            ->join(array('plugin_sict_product_relation', 'sict_product_relation'))
            ->on('sict_product.product_id', '=', 'sict_product_relation.sict_product_id')
            ->join(array('plugin_products_product', 'product'))
            ->on('product.id', '=', 'sict_product_relation.product_id');

        $whereCondition = true;

        // search by sub category
		if (count($sub_category) > 1)
		{
			$q->and_where_open();
		}
        foreach ($sub_category as $category_id => $sub_category_data)
		{
            // remove filter by parent category, if exist sub category
            if (isset($category[$category_id]))
			{
                unset($category[$category_id]);
            }
            foreach ($sub_category_data as $sub_category_id => $sub_category_name)
			{
                if ($whereCondition)
				{
                    $q->where('sict_product.category_id', '=', $sub_category_id);
                    $whereCondition = false;
                }
				else
				{
                    $q->or_where('sict_product.category_id', '=', $sub_category_id);
                }
            }
        }
		if (count($sub_category) > 1)
		{
			$q->and_where_close();
		}


		// search by category
		if (count($category) > 1)
		{
			$q->and_where_open();
		}

		foreach ($category as $category_id => $category_name)
		{
            $sub_category_data = DB::select()
                ->from('plugin_products_category')
                ->where('parent_id', '=', $category_id)
                ->execute();
            foreach ($sub_category_data as $row)
			{
                if ($whereCondition)
				{
                    $q->where('sict_product.category_id', '=', $row['id']);
                    $whereCondition = false;
                }
				else
				{
                    $q->or_where('sict_product.category_id', '=', $row['id']);
                }
            }
        }

		if (count($category) > 1)
		{
			$q->and_where_close();
		}

        // search by brand
		if (count($brand) > 1)
		{
			$q->and_where_open();
		}

        $whereCondition = true;
        foreach ($brand as $brand_id => $brand_name)
		{
            if ($whereCondition)
			{
                $q->where('sict_product.manufacturer_id', '=', $brand_id);
                $whereCondition = false;
            }
			else
			{
                $q->or_where('sict_product.manufacturer_id', '=', $brand_id);
            }
        }

		if (count($brand) > 1)
		{
			$q->and_where_close();
		}

        // search by distributor
        foreach ($distributor as $distributor_id => $distributor_name)
		{
            $q->where('sict_product.id', '>', 0);
        }

        // search by price
        $q->where('product.price', '<=', $price);

        $q->limit($limit * 3);

        if ($current_page) {
            $q->offset($current_page * $items_per_page);
        };

		// Only show featured products
		if ($featured_only)
		{
            $q->and_where('sict_product.thumb_url', 'is not', null)
                ->and_where('sict_product.thumb_url', '<>', '');
			$check_for_featured = clone($q);
			if ($check_for_featured->where('product.featured', '=', 1)->execute()->count() > 0)
			{
				$q->where('product.featured', '=', 1);
			}
			else
			{
				// If there are no featured products, show the twelve most recent products
				$q->order_by('product.date_entered', 'desc')->order_by('product.id', 'desc');
				$featured_only = FALSE;
			}
		}

        $q->and_where('product.deleted', '=', 0);
        $q->and_where('product.publish', '=', 1);
        $result = $q->execute();

        $data['product_categories'] = self::render_search_result($result, $items_per_page);

        $paging_button = View::factory('front_end/paging/paging_button')
            ->set('current_page', $current_page)
            ->set('featured_only', $featured_only)
            ->set('number_of_pages', $result->count() / $items_per_page);

        // add paging
        $data['product_categories'] .= View::factory('front_end/paging/paging')
            ->set('categories', $category)
            ->set('sub_categories', $sub_category)
            ->set('brands', $brand)
            ->set('distributors', $distributor)
            ->set('price', $price)
            ->set('featured_only', $featured_only)
            ->set('paging_button', $paging_button);

        // Set the CSS and JS Files for this View
        $data['view_css_files'][] = '<link href="' . URL::get_engine_plugin_assets_base('products') . 'css/front_end/products_front_end_general.css' . '" rel="stylesheet">';
        
        //Add the items to the container view and return the HTML
        return View::factory('front_end/product_category_list_html', $data);

    }

    private static function render_search_result($result, $items_per_page)
    {
		$vat_rate = Settings::instance()->get('vat_rate') ? (float)Settings::instance()->get('vat_rate') : 0;
        $product_categories = '';

        if($result->count()) {
            $max_title_length = 20;
            $count = 0;

            $product_categories .= '<div class="pagedemo  _current">';

            foreach ($result as $product_data) {
				$product_data['price_with_vat'] = Model_Product::calculate_total_price($product_data['price'], $vat_rate);
                if ($count >= $items_per_page) {
                    break;
                }

                $product_data['truncated_title'] = (strlen($product_data['title']) >= $max_title_length + 4)
                    ? trim(substr($product_data['title'], 0, $max_title_length)) . "..."
                    : $product_data['title'];

                $product_categories .= View::factory('front_end/' . ((
                        Settings::instance()->get('product_listing_display') == 'vertical')
                        ? 'product_item_vertical_html'
                        : 'product_item_html'
                    ), $product_data);

                $count++;
            }

            $product_categories .= '</div>';
        }

        return $product_categories;
    }

    public static function get_subcategory($category_id){
        $result = '';
        $sub_category_db = DB::select('category')
            ->from('plugin_products_category')
            ->where('parent_id', '=', $category_id)
            ->execute();

        if($sub_category_db->count()){
            $result .= '<ul class="main-nav-drop-down">';
			$link = Model_Product::get_products_plugin_page();
            foreach($sub_category_db as $sub_category_row){
                $result .= '<li>';
                    $result .= '<a href="/'.$link.'/'.$sub_category_row['category'].'">';
                        $result .= '<span class="orange-points">&#187;</span>';
                        $result .= $sub_category_row['category'];
                    $result .= '</a>';
                $result .= '</li>';
            }
            $result .= '</ul>';
        }

        return $result;
    }

    public static function render_top_category($category_id){
        $html = '';

        $categories = self::get_main_category();
        foreach($categories as $category)
		{
			$selected = ($category['id'] == $category_id) ? ' selected="selected"' : '';
            $html .= '<option value="'.$category['id'].'"'.$selected.'>'.$category['category'].'</option>';
        }

        return $html;
    }

	public static function render_sub_category2($parent_id, $selected_id = null){
        $view = '';
        $result = DB::select('id', 'category')->from('plugin_products_category')->where('parent_id', '=', $parent_id)->execute();
		 foreach($result as $sub_category){
             $view .= '<option value="'.$sub_category['id'].'"' . ($selected_id == $sub_category['id'] ? ' selected="selected"' : '' ) . '>'.$sub_category['category'].'</option>';
        }
        return $view;
    }

    private static function get_main_category(){
        return DB::select('id', 'category')
            ->from('plugin_products_category')
            ->where('parent_id', 'is', null)
            ->execute();
    }

    public static function format_url_title($title, $sku)
    {
        $url = $title . '-' . $sku;
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', $url));
    }

    public static function fix_url_titles()
    {
        try {
            set_time_limit(0);
            Database::instance()->begin();
            $products = DB::select('id', 'title', 'product_code', 'url_title')
                ->from(Model_Product::MAIN_TABLE)
                ->where('deleted', '=', 0)
                ->execute()
                ->as_array();
            foreach ($products as $product) {
                $new_url_title = self::format_url_title($product['title'], $product['product_code']);
                if ($new_url_title != $product['url_title']) {
                    Model_PageRedirect::save_redirect($product['url_title'], $new_url_title, 301, 1);
                    DB::update(Model_Product::MAIN_TABLE)
                        ->set(array('url_title' => $new_url_title))
                        ->where('id', '=', $product['id'])
                        ->execute();
                }
            }
            Database::instance()->commit();
            return true;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}