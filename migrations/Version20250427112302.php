<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427112302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, content LONGTEXT NOT NULL, rating INT NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526C16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD author_id INT NOT NULL, ADD language_id INT NOT NULL, ADD category_id INT NOT NULL, ADD title VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL, ADD available TINYINT(1) NOT NULL, ADD stock INT NOT NULL, CHANGE book description LONGTEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD CONSTRAINT FK_CBE5A331F675F31B FOREIGN KEY (author_id) REFERENCES author (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD CONSTRAINT FK_CBE5A33182F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD CONSTRAINT FK_CBE5A33112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CBE5A331F675F31B ON book (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CBE5A33182F1BAF4 ON book (language_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CBE5A33112469DE2 ON book (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD user_id INT NOT NULL, ADD book_id INT NOT NULL, ADD reserved_at DATETIME NOT NULL, ADD status VARCHAR(20) NOT NULL, ADD quantity INT NOT NULL, DROP reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C8495516A2B381 FOREIGN KEY (book_id) REFERENCES book (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C8495516A2B381 ON reservation (book_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD email VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33112469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33182F1BAF4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526C16A2B381
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE language
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CBE5A331F675F31B ON book
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CBE5A33182F1BAF4 ON book
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CBE5A33112469DE2 ON book
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP author_id, DROP language_id, DROP category_id, DROP title, DROP slug, DROP available, DROP stock, CHANGE description book LONGTEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495516A2B381
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C84955A76ED395 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C8495516A2B381 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD reservation VARCHAR(255) NOT NULL, DROP user_id, DROP book_id, DROP reserved_at, DROP status, DROP quantity
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_IDENTIFIER_EMAIL ON `user`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP email, DROP roles, DROP password
        SQL);
    }
}
