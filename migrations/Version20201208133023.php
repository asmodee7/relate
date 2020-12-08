<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201208133023 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classroom_duo (id INT AUTO_INCREMENT NOT NULL, classroom_1 VARCHAR(255) NOT NULL, classroom_2 VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom_duo_classroom (classroom_duo_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_B46232027DD8BC3C (classroom_duo_id), INDEX IDX_B46232026278D5A8 (classroom_id), PRIMARY KEY(classroom_duo_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_duo (id INT AUTO_INCREMENT NOT NULL, student_1 VARCHAR(255) NOT NULL, student_2 VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_duo_student (student_duo_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_6999C8DB67E6A0A7 (student_duo_id), INDEX IDX_6999C8DBCB944F1A (student_id), PRIMARY KEY(student_duo_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classroom_duo_classroom ADD CONSTRAINT FK_B46232027DD8BC3C FOREIGN KEY (classroom_duo_id) REFERENCES classroom_duo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_duo_classroom ADD CONSTRAINT FK_B46232026278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_duo_student ADD CONSTRAINT FK_6999C8DB67E6A0A7 FOREIGN KEY (student_duo_id) REFERENCES student_duo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_duo_student ADD CONSTRAINT FK_6999C8DBCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classroom_duo_classroom DROP FOREIGN KEY FK_B46232027DD8BC3C');
        $this->addSql('ALTER TABLE student_duo_student DROP FOREIGN KEY FK_6999C8DB67E6A0A7');
        $this->addSql('DROP TABLE classroom_duo');
        $this->addSql('DROP TABLE classroom_duo_classroom');
        $this->addSql('DROP TABLE student_duo');
        $this->addSql('DROP TABLE student_duo_student');
    }
}
