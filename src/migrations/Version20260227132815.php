<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227132815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial database schema: person, location_type, location, and menu tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE person (
                id INT AUTO_INCREMENT NOT NULL,
                firstname VARCHAR(255) NOT NULL,
                surname VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE location_type (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE menu (
                id INT AUTO_INCREMENT NOT NULL,
                child_of_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                sort_order INT NOT NULL,
                is_active TINYINT(1) DEFAULT 1 NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                INDEX idx_menu_child_of (child_of_id),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            CREATE TABLE location (
                id INT AUTO_INCREMENT NOT NULL,
                person_id INT NOT NULL,
                location_type_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                INDEX idx_location_person (person_id),
                INDEX idx_location_type (location_type_id),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        ');

        $this->addSql('
            ALTER TABLE menu
            ADD CONSTRAINT fk_menu_parent
            FOREIGN KEY (child_of_id) REFERENCES menu (id)
            ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE location
            ADD CONSTRAINT fk_location_person
            FOREIGN KEY (person_id) REFERENCES person (id)
        ');

        $this->addSql('
            ALTER TABLE location
            ADD CONSTRAINT fk_location_type
            FOREIGN KEY (location_type_id) REFERENCES location_type (id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY fk_location_person');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY fk_location_type');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY fk_menu_parent');

        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE location_type');
        $this->addSql('DROP TABLE person');
    }
}
