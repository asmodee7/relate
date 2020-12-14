<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201213101743 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classroom (id INT AUTO_INCREMENT NOT NULL, grade VARCHAR(255) NOT NULL, classroom_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom_teacher (classroom_id INT NOT NULL, teacher_id INT NOT NULL, INDEX IDX_3A0767FD6278D5A8 (classroom_id), INDEX IDX_3A0767FD41807E1D (teacher_id), PRIMARY KEY(classroom_id, teacher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom_duo (id INT AUTO_INCREMENT NOT NULL, classroom_1 VARCHAR(255) NOT NULL, classroom_2 VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom_duo_classroom (classroom_duo_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_B46232027DD8BC3C (classroom_duo_id), INDEX IDX_B46232026278D5A8 (classroom_id), PRIMARY KEY(classroom_duo_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) NOT NULL, school_name VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, user_lastname VARCHAR(255) NOT NULL, user_firstname VARCHAR(255) NOT NULL, user_position VARCHAR(255) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, age INT DEFAULT NULL, description LONGTEXT NOT NULL, photo VARCHAR(255) DEFAULT NULL, sport VARCHAR(255) NOT NULL, music VARCHAR(255) NOT NULL, other_hobbies VARCHAR(255) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_classroom (student_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_2E13F11DCB944F1A (student_id), INDEX IDX_2E13F11D6278D5A8 (classroom_id), PRIMARY KEY(student_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_duo (id INT AUTO_INCREMENT NOT NULL, student_1 VARCHAR(255) NOT NULL, student_2 VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_duo_student (student_duo_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_6999C8DB67E6A0A7 (student_duo_id), INDEX IDX_6999C8DBCB944F1A (student_id), PRIMARY KEY(student_duo_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teacher (id INT AUTO_INCREMENT NOT NULL, id_school_id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_number INT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_B0F6A6D5431FFBC9 (id_school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_duo_classroom ADD CONSTRAINT FK_B46232027DD8BC3C FOREIGN KEY (classroom_duo_id) REFERENCES classroom_duo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_duo_classroom ADD CONSTRAINT FK_B46232026278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_classroom ADD CONSTRAINT FK_2E13F11DCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_classroom ADD CONSTRAINT FK_2E13F11D6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_duo_student ADD CONSTRAINT FK_6999C8DB67E6A0A7 FOREIGN KEY (student_duo_id) REFERENCES student_duo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_duo_student ADD CONSTRAINT FK_6999C8DBCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D5431FFBC9 FOREIGN KEY (id_school_id) REFERENCES school (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD6278D5A8');
        $this->addSql('ALTER TABLE classroom_duo_classroom DROP FOREIGN KEY FK_B46232026278D5A8');
        $this->addSql('ALTER TABLE student_classroom DROP FOREIGN KEY FK_2E13F11D6278D5A8');
        $this->addSql('ALTER TABLE classroom_duo_classroom DROP FOREIGN KEY FK_B46232027DD8BC3C');
        $this->addSql('ALTER TABLE teacher DROP FOREIGN KEY FK_B0F6A6D5431FFBC9');
        $this->addSql('ALTER TABLE student_classroom DROP FOREIGN KEY FK_2E13F11DCB944F1A');
        $this->addSql('ALTER TABLE student_duo_student DROP FOREIGN KEY FK_6999C8DBCB944F1A');
        $this->addSql('ALTER TABLE student_duo_student DROP FOREIGN KEY FK_6999C8DB67E6A0A7');
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD41807E1D');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('DROP TABLE classroom_teacher');
        $this->addSql('DROP TABLE classroom_duo');
        $this->addSql('DROP TABLE classroom_duo_classroom');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE student_classroom');
        $this->addSql('DROP TABLE student_duo');
        $this->addSql('DROP TABLE student_duo_student');
        $this->addSql('DROP TABLE teacher');
    }
}
