<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitializeDB extends Migration
{
	public function up()
	{
		$fields = [
			'short' => [
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true
			],
			'full' => [
				'type' => 'VARCHAR',
				'constraint' => '10000',
				'null' => true
			],
			'clicks' => [
				'type' => 'INT',
				'constraint' => '11',
				'default' => '0'
			],
			'nsfw' => [
				'type' => 'TINYINT',
				'constraint' => '1',
				'default' => '0'
			]
		];
		$this->forge->addField('id');
		$this->forge->addField($fields);
		$this->forge->createTable('urls');
	}

	public function down()
	{
		$this->forge->dropTable('urls');
	}
}
