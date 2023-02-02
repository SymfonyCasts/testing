<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202215434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'The first dino migration!';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE dinosaur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dinosaur (id INT NOT NULL, name VARCHAR(255) NOT NULL, genus VARCHAR(255) NOT NULL, length INT NOT NULL, enclosure VARCHAR(255) NOT NULL, health VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE dinosaur_id_seq CASCADE');
        $this->addSql('DROP TABLE dinosaur');
    }
}
