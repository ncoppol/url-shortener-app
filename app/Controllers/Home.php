<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		$data = [
			'title' => 'Generate Short URL'
		];

		return view('home_view', $data);
	}
}
