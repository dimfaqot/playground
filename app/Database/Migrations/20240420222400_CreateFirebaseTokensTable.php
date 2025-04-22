<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFirebaseTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'fcm_token' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('firebase_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('firebase_tokens');
    }
}
