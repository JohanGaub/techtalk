<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231207091110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add meetup, topic, user_topic_vote, meetup_user_participant tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE meetup (id INT AUTO_INCREMENT NOT NULL, agency_id INT DEFAULT NULL, organizer_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, start_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', capacity INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9377E28CDEADB2A (agency_id), INDEX IDX_9377E28876C4DDA (organizer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meetup_user_participant (user_id INT NOT NULL, meetup_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_19E2BE6DA76ED395 (user_id), INDEX IDX_19E2BE6D591E2316 (meetup_id), PRIMARY KEY(user_id, meetup_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, user_proposer_id INT NOT NULL, user_reviewer_id INT DEFAULT NULL, user_presenter_id INT DEFAULT NULL, meetup_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, duration VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', duration_type VARCHAR(255) DEFAULT NULL, current_place VARCHAR(255) NOT NULL, reviewed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9D40DE1B18246FA1 (user_proposer_id), INDEX IDX_9D40DE1BD94C8F83 (user_reviewer_id), INDEX IDX_9D40DE1B5D28BA37 (user_presenter_id), INDEX IDX_9D40DE1B591E2316 (meetup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_topic_vote (topic_id INT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C59C223D1F55203D (topic_id), INDEX IDX_C59C223DA76ED395 (user_id), PRIMARY KEY(topic_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meetup ADD CONSTRAINT FK_9377E28CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id)');
        $this->addSql('ALTER TABLE meetup ADD CONSTRAINT FK_9377E28876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meetup_user_participant ADD CONSTRAINT FK_19E2BE6DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meetup_user_participant ADD CONSTRAINT FK_19E2BE6D591E2316 FOREIGN KEY (meetup_id) REFERENCES meetup (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B18246FA1 FOREIGN KEY (user_proposer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BD94C8F83 FOREIGN KEY (user_reviewer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B5D28BA37 FOREIGN KEY (user_presenter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B591E2316 FOREIGN KEY (meetup_id) REFERENCES meetup (id)');
        $this->addSql('ALTER TABLE user_topic_vote ADD CONSTRAINT FK_C59C223D1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE user_topic_vote ADD CONSTRAINT FK_C59C223DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E28CDEADB2A');
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E28876C4DDA');
        $this->addSql('ALTER TABLE meetup_user_participant DROP FOREIGN KEY FK_19E2BE6DA76ED395');
        $this->addSql('ALTER TABLE meetup_user_participant DROP FOREIGN KEY FK_19E2BE6D591E2316');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B18246FA1');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BD94C8F83');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B5D28BA37');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B591E2316');
        $this->addSql('ALTER TABLE user_topic_vote DROP FOREIGN KEY FK_C59C223D1F55203D');
        $this->addSql('ALTER TABLE user_topic_vote DROP FOREIGN KEY FK_C59C223DA76ED395');
        $this->addSql('DROP TABLE meetup');
        $this->addSql('DROP TABLE meetup_user_participant');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE user_topic_vote');
    }
}
