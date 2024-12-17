<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217141840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE forum ADD CONSTRAINT FK_852BBECDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_852BBECDA76ED395 ON forum (user_id)');
        $this->addSql('ALTER TABLE forum_comment ADD forum_id INT NOT NULL');
        $this->addSql('ALTER TABLE forum_comment ADD CONSTRAINT FK_65B81F1D29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('CREATE INDEX IDX_65B81F1D29CCBAD0 ON forum_comment (forum_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_comment DROP FOREIGN KEY FK_65B81F1D29CCBAD0');
        $this->addSql('DROP INDEX IDX_65B81F1D29CCBAD0 ON forum_comment');
        $this->addSql('ALTER TABLE forum_comment DROP forum_id');
        $this->addSql('ALTER TABLE forum DROP FOREIGN KEY FK_852BBECDA76ED395');
        $this->addSql('DROP INDEX IDX_852BBECDA76ED395 ON forum');
        $this->addSql('ALTER TABLE forum DROP user_id');
    }
}
