<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserTable extends Migration
{
    public function up()
    {
        // Define the table structure
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true
            ],
            'email' => [
                'type' => 'TEXT',
            ],
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 155,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 155,
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 155,
            ],
            'dob' => [
                'type' => 'date'
            ],
            'first_login' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'is_reset' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'is_locked' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'locked_on' => [
                'type' => 'datetime',
                'null' => true,
                'default' => null
            ],
            'unlock_on' => [
                'type' => 'datetime',
                'null' => true,
                'default' => null
            ],
            'reset_token' => [
                'type' => 'TEXT',
                'default' => null
            ],
            'is_two_factor_auth' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'two_factor_code' => [
                'type' => 'TEXT',
                'default' => null,
            ],
            'two_factor_validity' => [
                'type' => 'datetime',
                'default' => null,
            ],
            'login_attempts' => [
                'type' => 'INT',
                'constraint' => 20,
                'default' => 0,
            ],
            'added_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => null
            ],
            'added_on' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => null
            ],
            'updated_on' => [
                'type' => 'datetime',
                'null' => true,
                'default' => null
            ],
            'deleted_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => null
            ],
            'deleted_on' => [
                'type' => 'datetime',
                'null' => true,
                'default' => null
            ],
            'status' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
        ]);

        // Add primary key and key
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('added_by', 'main_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'main_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('deleted_by', 'main_user', 'id', 'CASCADE', 'CASCADE');
        // Create the table
        $this->forge->createTable('main_user');

        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => false,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45, // IPv6 support
                'null' => true,
            ],
            'access_token' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'refresh_token' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'access_token_expiry' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'refresh_token_expiry' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'last_activity' => [
                'type' => 'datetime',
                'null' => true,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'main_user', 'id');
        $this->forge->createTable('main_session_token');


        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => false,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45, // IPv6 support
                'null' => true,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'main_user', 'id');
        $this->forge->createTable('main_session');

        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'null' => false,
            ],
            'last_attempt_timestamp' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45, // IPv6 support
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'main_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('main_login_attempt');

        $main_session = [
            'session_id' => ['after' => 'id', 'type' => 'TEXT', 'null' => false]
        ];
        $this->forge->addColumn('main_session', $main_session);


        $main_session_token = [
            'session_id' => ['after' => 'user_id', 'type' => 'BIGINT', 'constraint' => 20, 'null' => false]
        ];
        $this->forge->addColumn('main_session_token', $main_session_token);
        $this->db->query("ALTER TABLE `main_session_token` ADD CONSTRAINT `main_session_token_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `main_session`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;");
    }

    public function down()
    {
        $this->forge->dropTable('main_session');
        $this->forge->dropTable('main_user');
        $this->forge->dropTable('main_login_attempt');
        $this->forge->dropColumn('main_session', 'session_id');
        $this->forge->dropColumn('main_session_token', 'session_id');
    }
}
