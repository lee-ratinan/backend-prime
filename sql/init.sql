# 1. log_activity
create table log_activity
(
    id               bigint auto_increment
        primary key,
    activity_key     varchar(32)                         null,
    table_involved   varchar(128)                        null,
    table_id_updated int       default 0                 null,
    activity_detail  text                                null,
    done_by          int                                 null,
    done_at          timestamp default CURRENT_TIMESTAMP null,
    updated_at       datetime                            null
);

# 2. log_email
create table log_email
(
    id            bigint auto_increment
        primary key,
    email_subject varchar(128)                        null,
    email_to      varchar(128)                        null,
    email_status  text                                null comment 'Status from Mailgun',
    created_at    timestamp default CURRENT_TIMESTAMP null,
    updated_at    datetime                            null
);

# 3. organization_master
create table organization_master
(
    id                                      int auto_increment
        primary key,
    organization_name                       varchar(128)                        null comment 'This is the legal name',
    organization_address_1                  text                                null,
    organization_address_2                  text                                null,
    organization_address_3                  text                                null,
    organization_address_country_code       char(2)                             null comment 'ISO3166',
    organization_address_postal_code        varchar(16)                         null,
    organization_phone_country_calling_code varchar(8)                          null comment 'e.g. +1, +44',
    organization_phone_number               varchar(128)                        null,
    organization_email_address              varchar(128)                        null,
    organization_website_url                varchar(128)                        null,
    organization_social_links               text                                null comment 'Stored in JSON format',
    app_name                                varchar(128)                        null comment 'This is the name shown in the app',
    trade_name                              varchar(128)                        null comment 'This is the organization''s trade name',
    registration_number                     varchar(128)                        null,
    incorporation_date                      date                                null,
    created_by                              int                                 null,
    created_at                              timestamp default CURRENT_TIMESTAMP null,
    updated_at                              datetime                            null
);

INSERT INTO organization_master (id, organization_name, organization_address_1, organization_address_2, organization_address_3, organization_address_country_code, organization_address_postal_code, organization_phone_country_calling_code, organization_phone_number, organization_email_address, organization_website_url, organization_social_links, app_name, trade_name, registration_number, incorporation_date, created_by, created_at, updated_at) VALUES
(1, 'RatinanTech', '123 Example Street', 'Cincinnati OH', '', 'US', '45200', '+1', '1112223333', 'organization@ratinan.com', 'https://lee.ratinan.com', null, 'RatinanTech', null, null, null, 1, CURRENT_TIMESTAMP, null);

# 4. role_access
create table role_access
(
    id             int auto_increment
        primary key,
    role_id        int                                       null,
    access_feature varchar(32)                               null,
    access_level   enum ('1', '2') default '1'               null comment '1 means read_only, 2 means editable',
    created_by     int                                       null,
    created_at     timestamp       default CURRENT_TIMESTAMP null,
    updated_at     datetime                                  null
);

INSERT INTO role_access (id, role_id, access_feature, access_level, created_by, created_at, updated_at) VALUES
(1, 1, 'organization', '2', 1, CURRENT_TIMESTAMP, null),
(2, 1, 'log', '2', 1, CURRENT_TIMESTAMP, null),
(3, 2, 'user_master', '2', 1, CURRENT_TIMESTAMP, null),
(4, 2, 'role_master', '2', 1, CURRENT_TIMESTAMP, null);

# 5. role_master
create table role_master
(
    id               int auto_increment
        primary key,
    role_name        varchar(32)                         not null,
    role_description text                                null,
    created_by       int                                 null,
    created_at       timestamp default CURRENT_TIMESTAMP null,
    updated_at       datetime                            null,
    constraint role_master_pk_2
        unique (role_name)
);

INSERT INTO role_master (id, role_name, role_description, created_by, created_at, updated_at) VALUES
(1, 'super-admin', 'Super admin', 1, CURRENT_TIMESTAMP, null),
(2, 'master-admin', 'Master admin', 1, CURRENT_TIMESTAMP, null);

# 6. user_master
create table user_master
(
    id                             int auto_increment
        primary key,
    email_address                  varchar(128)                                         null comment 'This is used as the username',
    telephone_country_calling_code varchar(8)                                           null comment 'This field has to be unique, but it can also be null',
    telephone_number               varchar(128)                                         null comment 'This field has to be unique, but it can also be null',
    user_name_first                varchar(128)                                         null,
    user_name_family               varchar(128)                                         null,
    user_gender                    enum ('M', 'F', 'NB', 'U') default 'U'               null comment 'M = Male, F = Female, NB = Nonbinary, U = Unspecified',
    user_date_of_birth             date                                                 null,
    user_nationality               char(2)                                              null,
    user_profile_status            text                                                 null comment 'Just some status message for showing off',
    account_status                 enum ('A', 'B', 'T', 'P')  default 'P'               null comment 'The account might need to be approved when created; the account will be blocked when password is entered incorrectly 3+ times',
    account_password_hash          text                                                 null comment 'This is the hash value',
    account_password_expiry        date                                                 null comment 'It is normally 180 days after it is changed',
    account_type                   enum ('S', 'C')            default 'S'               null comment 'Staff, Client',
    employee_id                    varchar(128)                                         null comment 'Used only if it''s employee',
    employee_start_date            date                                                 null comment 'First date of work for employee',
    employee_end_date              date                                                 null comment 'Last date of work for employee',
    employee_title                 varchar(128)                                         null,
    preferred_language             enum ('en', 'th')          default 'en'              null,
    user_created_by                int                                                  null comment 'user_id of the person who created this account',
    user_created_at                timestamp                  default CURRENT_TIMESTAMP null,
    user_updated_at                datetime                                             null,
    constraint user_email_address
        unique (email_address),
    constraint user_telephone_number
        unique (telephone_country_calling_code, telephone_number)
)
    comment 'This is the main user table';

INSERT INTO user_master (id, email_address, telephone_country_calling_code, telephone_number, user_name_first, user_name_family, user_gender, user_date_of_birth, user_nationality, user_profile_status, account_status, account_password_hash, account_password_expiry, account_type, employee_id, employee_start_date, employee_end_date, employee_title, preferred_language, user_created_by, user_created_at, user_updated_at) VALUES
(1, 'john.doe@ratinan.com', '+65', '00000000', 'John', 'Doe', 'U', '1990-01-01', 'TH', 'Te manulele e tataki e', 'A', null, '2025-12-31', 'S', null, null, null, null, 'en', 1, CURRENT_TIMESTAMP, null);

# 7. user_role
create table user_role
(
    id              int auto_increment
        primary key,
    user_id         int                                       null,
    role_name       varchar(32)                               null,
    is_default_role enum ('N', 'Y') default 'N'               null comment 'One user has one default role',
    role_created_by int                                       null,
    role_created_at timestamp       default CURRENT_TIMESTAMP null,
    role_updated_at datetime                                  null
);

INSERT INTO user_role (id, user_id, role_name, is_default_role, role_created_by, role_created_at, role_updated_at) VALUES
(1, 1, 'super-admin', 'N', 1, CURRENT_TIMESTAMP, null),
(2, 1, 'master-admin', 'Y', 1, CURRENT_TIMESTAMP, null);

# FOREIGN KEYS
# 1. log_activity
alter table log_activity
    add constraint log_activity_user_master_id_fk
        foreign key (done_by) references user_master (id);
# 2. log_email
# 3. organization_master
alter table organization_master
    add constraint organization_master_user_master_id_fk
        foreign key (created_by) references user_master (id);
# 4. role_access
alter table role_access
    add constraint role_access_role_master_id_fk
        foreign key (role_id) references role_master (id);
alter table role_access
    add constraint role_access_user_master_id_fk
        foreign key (created_by) references user_master (id);
# 5. role_master
alter table role_master
    add constraint role_master_user_master_id_fk
        foreign key (created_by) references user_master (id);
# 6. user_master
alter table user_master
    add constraint user_master_user_master_id_fk
        foreign key (user_created_by) references user_master (id);
# 7. user_role
alter table user_role
    add constraint user_role_user_master_id_fk
        foreign key (user_id) references user_master (id);
alter table user_role
    add constraint user_role_user_master_id_fk_2
        foreign key (role_created_by) references user_master (id);

