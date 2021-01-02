<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200101000000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE datinglibre.users (
    id UUID NOT NULL PRIMARY KEY,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    roles JSONB NOT NULL,
    ip TEXT,
    enabled boolean NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL,
    last_login TIMESTAMP WITH TIME ZONE);');

        $this->addSql('CREATE TABLE datinglibre.countries (
    id UUID NOT NULL PRIMARY KEY,
    name TEXT UNIQUE NOT NULL
);');
        $this->addSql('CREATE TABLE datinglibre.images (
    id UUID NOT NULL PRIMARY KEY,
    type TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL,
    secure_url TEXT,
    secure_url_expiry TIMESTAMP WITH TIME ZONE,
    is_profile BOOLEAN NOT NULL,
    user_id UUID REFERENCES datinglibre.users ON DELETE SET NULL,
    state TEXT NOT NULL
);');
        // enforce one default profile image per user
        $this->addSql('CREATE UNIQUE INDEX profile_image ON datinglibre.images (user_id)
    WHERE is_profile IS TRUE;');
        $this->addSql('CREATE TABLE datinglibre.regions (
    id UUID NOT NULL PRIMARY KEY,
    name TEXT NOT NULL,
    country_id UUID NOT NULL REFERENCES datinglibre.countries ON DELETE CASCADE
);');
        $this->addSql('CREATE TABLE datinglibre.cities (
    id UUID NOT NULL PRIMARY KEY,
    geoname_id INTEGER,
    country_id UUID NOT NULL REFERENCES datinglibre.countries ON DELETE CASCADE,
    region_id UUID NOT NULL REFERENCES datinglibre.regions ON DELETE CASCADE,
    name TEXT NOT NULL,
    longitude DOUBLE PRECISION NOT NULL,
    latitude DOUBLE PRECISION NOT NULL
);');
        $this->addSql('CREATE INDEX city_location_index ON datinglibre.cities USING gist (
    public.geography(public.st_makepoint(longitude, latitude))
);');

        $this->addSql('CREATE TABLE datinglibre.profiles (
    user_id UUID NOT NULL PRIMARY KEY REFERENCES datinglibre.users ON DELETE CASCADE,
    username TEXT UNIQUE,
    dob DATE,
    about TEXT,
    meta JSONB,
    city_id UUID REFERENCES datinglibre.cities,
    state TEXT DEFAULT \'CREATED\'::TEXT NOT NULL,
    status TEXT,
    sort_id BIGSERIAL,
    updated_at TIMESTAMP WITH TIME ZONE
);');
        $this->addSql('CREATE INDEX profile_sort_order ON datinglibre.profiles(sort_id);');
        $this->addSql('CREATE TABLE datinglibre.categories (
    id UUID NOT NULL PRIMARY KEY,
    name TEXT UNIQUE
);');
        $this->addSql('CREATE TABLE datinglibre.attributes (
    id UUID NOT NULL PRIMARY KEY,
    name TEXT UNIQUE,
    category_id UUID NOT NULL REFERENCES datinglibre.categories
);');
        $this->addSql('CREATE TABLE datinglibre.requirements (
    user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    attribute_id UUID NOT NULL REFERENCES datinglibre.attributes,
    UNIQUE(user_id, attribute_id)
);');

        $this->addSql('CREATE TABLE datinglibre.user_attributes (
    user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    attribute_id UUID NOT NULL REFERENCES datinglibre.attributes,
    UNIQUE(user_id, attribute_id)
);');
        $this->addSql('CREATE TABLE datinglibre.block_reasons (
    id UUID NOT NULL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
)');

        $this->addSql('CREATE TABLE datinglibre.blocks (
    id UUID NOT NULL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    blocked_user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    reason_id UUID NOT NULL REFERENCES datinglibre.block_reasons,
    state TEXT,
    UNIQUE (user_id, blocked_user_id)
);');

        $this->addSql('CREATE TABLE datinglibre.filters (
    user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    region_id UUID REFERENCES datinglibre.regions ON DELETE CASCADE,
    distance integer CHECK (distance > 0),
    min_age integer CHECK (min_age >= 18 AND min_age <= max_age),
    max_age integer CHECK (max_age >= 18 AND max_age >= min_age)
)');
        $this->addSql('CREATE TABLE datinglibre.messages (
    id UUID NOT NULL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    sender_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    content TEXT,
    thread_id UUID NOT NULL, 
    type TEXT,
    sent_time TIMESTAMP WITH TIME ZONE
);');

        $this->addSql('CREATE TABLE datinglibre.tokens (
    id UUID NOT NULL PRIMARY KEY,
    secret TEXT,
    type TEXT,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL,
    user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    UNIQUE (user_id, type)
);');

        $this->addSql('CREATE TABLE datinglibre.emails (
    id UUID NOT NULL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES datinglibre.users ON DELETE CASCADE,
    type TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL
);');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE datinglibre.profiles');
        $this->addSql('DROP TABLE datinglibre.images');
        $this->addSql('DROP TABLE datinglibre.users;');
        $this->addSql('DROP TABLE datinglibre.cities');
        $this->addSql('DROP TABLE datinglibre.regions');
        $this->addSql('DROP TABLE datinglibre.user_attributes');
        $this->addSql('DROP TABLE datinglibre.attributes');
        $this->addSql('DROP TABLE datinglibre.blocks');
        $this->addSql('DROP TABLE datinglibre.block_reasons');
        $this->addSql('DROP TABLE datinglibre.searches');
        $this->addSql('DROP TABLE datinglibre.messages');
        $this->addSql('DROP TABLE datinglibre.tokens');
        $this->addSql('DROP TABLE datinglibre.emails');
        $this->addSql('DROP TABLE datinglibre.filters');
    }
}
