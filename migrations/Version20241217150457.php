<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217150457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création Relations User BlogComment ForumComment';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_comment ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE blog_comment ADD CONSTRAINT FK_7882EFEFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7882EFEFA76ED395 ON blog_comment (user_id)');
        $this->addSql('ALTER TABLE forum_comment ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE forum_comment ADD CONSTRAINT FK_65B81F1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_65B81F1DA76ED395 ON forum_comment (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_comment DROP FOREIGN KEY FK_65B81F1DA76ED395');
        $this->addSql('DROP INDEX IDX_65B81F1DA76ED395 ON forum_comment');
        $this->addSql('ALTER TABLE forum_comment DROP user_id');
        $this->addSql('ALTER TABLE blog_comment DROP FOREIGN KEY FK_7882EFEFA76ED395');
        $this->addSql('DROP INDEX IDX_7882EFEFA76ED395 ON blog_comment');
        $this->addSql('ALTER TABLE blog_comment DROP user_id');
    }
}
