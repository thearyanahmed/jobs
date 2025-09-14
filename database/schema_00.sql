CREATE table users (
    id bigint unsigned primary key auto_increment,
    name varchar(255) not null,
    email varchar(255) not null unique,
    password varchar(255),

    created_at timestamp null,
    updated_at timestamp null,
    deleted_at timestamp null,

);
