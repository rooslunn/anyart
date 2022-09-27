drop table if exists properties;

create table properties (
    id bigint not null primary key auto_increment,
    county varchar(255),
    country varchar(255) not null,
    town varchar(255) not null,
    description mediumtext,
    address varchar(255) not null,
    image_full varchar(255),
    image_thumbnail varchar(255),
    latitude decimal(13, 10),
    longitude decimal(13, 10),
    num_bedrooms int,
    num_bathrooms int,
    price int not null,
    property_type_title varchar(255),
    property_type_description mediumtext,
    sale_or_rent varchar(8)
);