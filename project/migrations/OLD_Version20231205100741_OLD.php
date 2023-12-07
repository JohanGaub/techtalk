<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class OLDVersion20231205100741OLD extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add topic and user_topic_vote tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, user_proposer_id INT NOT NULL, user_validator_id INT DEFAULT NULL, user_presenter_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, current_place VARCHAR(255) NOT NULL, validated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', presented_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9D40DE1B18246FA1 (user_proposer_id), INDEX IDX_9D40DE1B30A836EE (user_validator_id), INDEX IDX_9D40DE1B5D28BA37 (user_presenter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_topic_vote (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C59C223D1F55203D (topic_id), INDEX IDX_C59C223DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B18246FA1 FOREIGN KEY (user_proposer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B30A836EE FOREIGN KEY (user_validator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B5D28BA37 FOREIGN KEY (user_presenter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_topic_vote ADD CONSTRAINT FK_C59C223D1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE user_topic_vote ADD CONSTRAINT FK_C59C223DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B18246FA1');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B30A836EE');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B5D28BA37');
        $this->addSql('ALTER TABLE user_topic_vote DROP FOREIGN KEY FK_C59C223D1F55203D');
        $this->addSql('ALTER TABLE user_topic_vote DROP FOREIGN KEY FK_C59C223DA76ED395');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE user_topic_vote');
    }
}
