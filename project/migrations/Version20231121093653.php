<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231121093653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add agency, user and messenger_messages tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE agency (
                    id INT AUTO_INCREMENT NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    PRIMARY KEY(id)
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE user (
                    id INT AUTO_INCREMENT NOT NULL,
                    agency_id INT NOT NULL,
                    email VARCHAR(180) NOT NULL,
                    roles JSON NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    first_name VARCHAR(100) NOT NULL,
                    last_name VARCHAR(100) NOT NULL,
                    enabled TINYINT(1) NOT NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
                    INDEX IDX_8D93D649CDEADB2A (agency_id),
                    PRIMARY KEY(id)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE messenger_messages (
                    id BIGINT AUTO_INCREMENT NOT NULL,
                    body LONGTEXT NOT NULL,
                    headers LONGTEXT NOT NULL,
                    queue_name VARCHAR(190) NOT NULL,
                    created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                    available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                    delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                    INDEX IDX_75EA56E0FB7336F0 (queue_name),
                    INDEX IDX_75EA56E0E3BD61CE (available_at),
                    INDEX IDX_75EA56E016BA31DB (delivered_at),
                    PRIMARY KEY(id)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE user ADD CONSTRAINT FK_8D93D649CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CDEADB2A');
        $this->addSql('DROP TABLE agency');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
