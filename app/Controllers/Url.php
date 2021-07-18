<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UrlModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Response;
use CodeIgniter\API\ResponseTrait;

class Url extends BaseController
{
	use ResponseTrait;

	public function addUrl(): Response 
	{

		try 
		{
			if ($this->validate([
				'url' => 'required|min_length[3]|max_length[10000]|valid_url'
			])) 
			{
				$model = new UrlModel();
				$fullUrl = prep_url($this->request->getVar('url'));
				$shortUrl = $model->insertUrl($fullUrl);
				$shortUrl = base_url($shortUrl);

			}
			else throw new \Exception("Invalid URL");
		}
		catch (\Exception $e) 
		{
			return $this->fail($e->getMessage());
		}

		return $this->respondCreated(Array('short_url' => $shortUrl), 'shortened Url successfully created');
		
	}

	public function getTop100(): Response
	{
		try 
		{
			$model = new UrlModel();
			$data = '';
			$data = $model->getTopClicks(100);
		}
		catch (\Exception $e)
		{
			return $this->fail($e->getMessage());
		}

		return $this->respond($data);

	}

	public function redirect(): RedirectResponse
	{
		$path = $this->request->getPath();

		if (ctype_alnum($path)) {
			$model = new UrlModel();
			try
			{
				$fullUrl = $model->getFullUrl($path);
			}
			catch (\Exception $e) {}

			if ($fullUrl) return redirect()->to($fullUrl, 301);
			else return redirect()->to(base_url(), 301);
		}

		return redirect()->to(base_url(), 301);
		
	}
}
