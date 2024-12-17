<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217141007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création Relations Blog';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE blog ADD CONSTRAINT FK_C0155143A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C0155143A76ED395 ON blog (user_id)');
        $this->addSql('ALTER TABLE blog_comment ADD blog_id INT NOT NULL');
        $this->addSql('ALTER TABLE blog_comment ADD CONSTRAINT FK_7882EFEFDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('CREATE INDEX IDX_7882EFEFDAE07E97 ON blog_comment (blog_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_comment DROP FOREIGN KEY FK_7882EFEFDAE07E97');
        $this->addSql('DROP INDEX IDX_7882EFEFDAE07E97 ON blog_comment');
        $this->addSql('ALTER TABLE blog_comment DROP blog_id');
        $this->addSql('ALTER TABLE blog DROP FOREIGN KEY FK_C0155143A76ED395');
        $this->addSql('DROP INDEX IDX_C0155143A76ED395 ON blog');
        $this->addSql('ALTER TABLE blog DROP user_id');
    }
}
