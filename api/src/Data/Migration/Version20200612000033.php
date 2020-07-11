<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200612000033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE page (id UUID NOT NULL, parent_id UUID DEFAULT NULL, short_name VARCHAR(255) DEFAULT NULL, full_name VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, is_visible BOOLEAN NOT NULL, note TEXT DEFAULT NULL, slug1 VARCHAR(255) DEFAULT NULL, slug2 VARCHAR(255) DEFAULT NULL, slug3 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_140AB620727ACA70 ON page (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB62020F87258B9F123E2CEF61374 ON page (slug1, slug2, slug3)');
        $this->addSql('COMMENT ON COLUMN page.id IS \'(DC2Type:page_page_id)\'');
        $this->addSql('COMMENT ON COLUMN page.parent_id IS \'(DC2Type:page_page_id)\'');
        $this->addSql('COMMENT ON COLUMN page.short_name IS \'(DC2Type:page_page_short_name)\'');
        $this->addSql('COMMENT ON COLUMN page.full_name IS \'(DC2Type:page_page_full_name)\'');
        $this->addSql('COMMENT ON COLUMN page.title IS \'(DC2Type:page_page_title)\'');
        $this->addSql('COMMENT ON COLUMN page.note IS \'(DC2Type:page_page_note)\'');
        $this->addSql('COMMENT ON COLUMN page.slug1 IS \'(DC2Type:nullable_string)\'');
        $this->addSql('COMMENT ON COLUMN page.slug2 IS \'(DC2Type:nullable_string)\'');
        $this->addSql('COMMENT ON COLUMN page.slug3 IS \'(DC2Type:nullable_string)\'');
        $this->addSql(
            'ALTER TABLE page ADD CONSTRAINT FK_140AB620727ACA70 FOREIGN KEY (parent_id) REFERENCES page (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page DROP CONSTRAINT FK_140AB620727ACA70');
        $this->addSql('DROP TABLE page');
    }
}
