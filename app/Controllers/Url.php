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
				$nsfw = false;
				if ($this->request->getVar('nsfw')) $nsfw = true;

				$model = new UrlModel();
				$fullUrl = prep_url($this->request->getVar('url'));
				$shortUrl = $model->insertUrl($fullUrl, $nsfw);
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



	public function redirect()
	{
		$path = $this->request->getPath();

		if (ctype_alnum($path)) {
			$model = new UrlModel();
			try
			{
				$id = $model->shortUrlToId($path);
				$data = $model->find($id);
				$fullUrl = $data['full'];
				$nsfw = $data['nsfw'];
				$model->addClick($id);
			}
			catch (\Exception $e) {}

			if ($fullUrl) 
			{
				if ($nsfw) 
				{
					$nsfwData = [
						'fullUrl' => $fullUrl
					];
					return view('nsfw_view', $nsfwData);
				}
				else return redirect()->to($fullUrl, 301);
			}
			// just redirect back to home page if we can't find a fullUrl
			else return redirect()->to(base_url(), 301);
		}

		else return redirect()->to(base_url(), 301);
		
	}
}
