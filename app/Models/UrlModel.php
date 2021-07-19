<?php

namespace App\Models;

use CodeIgniter\Model;

class UrlModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'urls';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['short','full','clicks','nsfw'];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];



	public function insertUrl(string $fullUrl, bool $nsfw=false): string 
	{
		$shortUrl = '';
		// check if url is valid
		if (! filter_var($fullUrl, FILTER_VALIDATE_URL)) 
		{
			throw new \Exception("Invalid URL");
		}
		
		else 
		{
			$initialData = Array(
				'full' => $fullUrl
			);

			$this->insert($initialData);
			$id = $this->insertID();

			$shortUrl = $this->idToShortUrl($id);
			
			$updateData = Array(
				'short' => $shortUrl,
			);

			if ($nsfw) $updateData['nsfw'] = '1';

			$this->update($id,$updateData);
			
		}

		return $shortUrl;
	}

	public function getTopClicks(int $count): array
	{
		$data = Array();
		$this->orderBy('clicks', 'DESC');
		$this->select('short as short_url, full as full_url, clicks');
		$data = $this->get($count)->getResultArray();
		foreach($data as &$row) 
		{
			$row['short_url'] = base_url() . '/' . $row['short_url'];
		}
		return $data;
	}

	/*public function getFullUrl(string $shortUrl, $getNsfwFlag = false): array 
	{
		$return = Array();
		$id = $this->shortUrlToId($shortUrl);
		$data = $this->find($id);
		if (isset($data['full'])) 
		{
			$return[] = $data['full'];
			$this->addClick($id);
			if ()
		}
		return $return;
	}*/
	
	public function addClick(int $id): void 
	{
		$this->where('id', $id);
		$this->increment('clicks');
	}




	public function idToShortUrl(int $id): string 
	{
		$shortUrl = '';
		// convert to base 62 to generate our shortened url
		$base62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	
		while($id > 0) {
			$remainder = $id % 62;
			$shortUrl = $base62[$remainder] . $shortUrl;
			$id = floor($id / 62);
		}
		
		return $shortUrl;
	}

	public function shortUrlToId(string $shortUrl): int 
	{
		$id = 0;
	
		// convert back to base 10
		$shortUrlArray = str_split($shortUrl);
		foreach ($shortUrlArray as $char) {
			$unicode = ord($char);
			if (($unicode >= ord('0')) && ($unicode <= ord('9'))) $id = $id*62 + ($unicode - ord('0'));
			elseif (($unicode >= ord('A')) && ($unicode <= ord('Z'))) $id = $id*62 + ($unicode - ord('A')) + 10;
			elseif (($unicode >= ord('a')) && ($unicode <= ord('z'))) $id = $id*62 + ($unicode - ord('a')) + 36;
		}
		return $id;
	}
}
