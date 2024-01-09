<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231217225434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add meetup, topic, user_topic_vote, meetup_user_participant tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE meetup (
                id INT AUTO_INCREMENT NOT NULL,
                agency_id INT DEFAULT NULL,
                user_organiser_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                description LONGTEXT DEFAULT NULL,
                start_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                capacity INT DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_9377E28CDEADB2A (agency_id),
                INDEX IDX_9377E2820AF6010 (user_organiser_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE meetups_users_participant (
                meetup_id INT NOT NULL,
                user_id INT NOT NULL,
                INDEX IDX_B17FF4FC591E2316 (meetup_id),
                INDEX IDX_B17FF4FCA76ED395 (user_id),
                PRIMARY KEY(meetup_id, user_id)
             ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE topic (
                id INT AUTO_INCREMENT NOT NULL,
                user_proposer_id INT NOT NULL,
                user_publisher_id INT DEFAULT NULL,
                user_presenter_id INT DEFAULT NULL,
                meetup_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                description LONGTEXT DEFAULT NULL,
                duration VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\',
                duration_category VARCHAR(255) DEFAULT NULL,
                current_place VARCHAR(255) NOT NULL,
                in_review_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                published_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_9D40DE1B18246FA1 (user_proposer_id),
                INDEX IDX_9D40DE1BC00413CC (user_publisher_id),
                INDEX IDX_9D40DE1B5D28BA37 (user_presenter_id),
                INDEX IDX_9D40DE1B591E2316 (meetup_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE topics_users_vote (
                topic_id INT NOT NULL,
                user_id INT NOT NULL,
                INDEX IDX_309737131F55203D (topic_id),
                INDEX IDX_30973713A76ED395 (user_id),
                PRIMARY KEY(topic_id, user_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE meetup
                    ADD CONSTRAINT FK_9377E28CDEADB2A
                    FOREIGN KEY (agency_id)
                    REFERENCES agency (id)'
        );
        $this->addSql(
            'ALTER TABLE meetup
                    ADD CONSTRAINT FK_9377E2820AF6010
                    FOREIGN KEY (user_organiser_id)
                    REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE meetups_users_participant
                    ADD CONSTRAINT FK_B17FF4FC591E2316
                    FOREIGN KEY (meetup_id)
                    REFERENCES meetup (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE meetups_users_participant
                    ADD CONSTRAINT FK_B17FF4FCA76ED395
                    FOREIGN KEY (user_id)
                    REFERENCES user (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE topic
                    ADD CONSTRAINT FK_9D40DE1B18246FA1
                    FOREIGN KEY (user_proposer_id)
                    REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE topic
                    ADD CONSTRAINT FK_9D40DE1BC00413CC
                    FOREIGN KEY (user_publisher_id)
                    REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE topic
                    ADD CONSTRAINT FK_9D40DE1B5D28BA37
                    FOREIGN KEY (user_presenter_id)
                    REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE topic
                    ADD CONSTRAINT FK_9D40DE1B591E2316
                    FOREIGN KEY (meetup_id)
                    REFERENCES meetup (id)'
        );
        $this->addSql(
            'ALTER TABLE topics_users_vote
                    ADD CONSTRAINT FK_309737131F55203D
                    FOREIGN KEY (topic_id)
                    REFERENCES topic (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE topics_users_vote
                    ADD CONSTRAINT FK_30973713A76ED395
                    FOREIGN KEY (user_id)
                    REFERENCES user (id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E28CDEADB2A');
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E2820AF6010');
        $this->addSql('ALTER TABLE meetups_users_participant DROP FOREIGN KEY FK_B17FF4FC591E2316');
        $this->addSql('ALTER TABLE meetups_users_participant DROP FOREIGN KEY FK_B17FF4FCA76ED395');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B18246FA1');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BC00413CC');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B5D28BA37');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B591E2316');
        $this->addSql('ALTER TABLE topics_users_vote DROP FOREIGN KEY FK_309737131F55203D');
        $this->addSql('ALTER TABLE topics_users_vote DROP FOREIGN KEY FK_30973713A76ED395');
        $this->addSql('DROP TABLE meetup');
        $this->addSql('DROP TABLE meetups_users_participant');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE topics_users_vote');
    }
}
