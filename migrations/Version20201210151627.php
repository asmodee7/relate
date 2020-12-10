<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201210151627 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school ADD roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE student ADD roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE age age INT DEFAULT NULL');
        $this->addSql('ALTER TABLE teacher ADD roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school DROP roles');
        $this->addSql('ALTER TABLE student DROP roles, CHANGE age age INT NOT NULL');
        $this->addSql('ALTER TABLE teacher DROP roles');
    }
}
