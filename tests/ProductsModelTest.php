<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\User;
use App\Products;

class ProductsModelTest extends TestCase
{

	protected $User;

	public function createProduct($sku)
	{
		$user = $this->generateUserTest();

		$this->User = User::where('token', $user->response->original['access_token'])->first();

		$headers['Authorization'] = 'Bearer ' . $user->response->original['access_token'];

		$url = '/v1/products';
		$data = [
			'sku' => $sku,
			'stock' => 10,
			'price' => 2.50,
			'name' => 'Produto de teste'
		];
		
		$json = $this->post($url, $data, $headers);
	}

	public function testUpdateStockDecrease()
	{
		$sku = uniqid();
		
		$this->createProduct($sku);

		Products::updateStock($sku, $this->User->id, 'decrease', 1);
        
        $product = Products::getItemBySKUAndUserId($sku, $this->User->id);

		$this->assertEquals(9, $product['estoque']);
	}

	public function testUpdateStockDecreaseTenItens()
	{
		$sku = uniqid();
		
		$this->createProduct($sku);

		Products::updateStock($sku, $this->User->id, 'decrease', 10);
        
        $product = Products::getItemBySKUAndUserId($sku, $this->User->id);

		$this->assertEquals(0, $product['estoque']);
	}
	
	public function testUpdateStockDecreaseFiftyItens()
	{
		$sku = uniqid();
		
		$this->createProduct($sku);

		Products::updateStock($sku, $this->User->id, 'decrease', 15);
        
        $product = Products::getItemBySKUAndUserId($sku, $this->User->id);

		$this->assertEquals(-5, $product['estoque']);
	}

	public function testUpdateStockIncrease()
	{
		$sku = uniqid();

		$this->createProduct($sku);

		Products::updateStock($sku, $this->User->id, 'increase', 1);
        
        $product = Products::getItemBySKUAndUserId($sku, $this->User->id);

		$this->assertEquals(11, $product['estoque']);
	}

}