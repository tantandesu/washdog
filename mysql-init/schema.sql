
create table user (
  id                integer       not null  auto_increment
, email             varchar(255)  not null  unique
, registrationKey   varchar(255)  not null
, password          varchar(255)  not null
, verified          boolean       not null  default FALSE
, primary key (id)
);

-- void the API key to logout
create table access (
  apiKey            varchar(255)  not null
, userId            integer       not null
, lastSeen          timestamp     not null  default current_timestamp
, void              boolean       not null  default FALSE
, primary key (apiKey)
, foreign key (userId) references user(id)
);

-- id = CPU serial number?
-- null IP = offline?
create table coordinator (
  id                varchar(255)  not null
, ipAddress         varchar(255)
, location          varchar(255)  not null
, version           varchar(255)  not null
, lastSeen          timestamp     not null  default current_timestamp
, primary key (id)
);

-- id = Bluetooth MAC?
create table device (
  id                varchar(255)  not null
, name              varchar(255)  not null
, coordinatorId     varchar(255)  not null
, status            enum(
                      'ready',
                      'washing',
                      'out-of-service'
                    )             not null
, version           varchar(255)  not null
, lastSeen          timestamp     not null  default current_timestamp
, primary key (id)
, foreign key (coordinatorId) references coordinator(id)
);

create table job (
  id                integer       not null  auto_increment
, userId            integer       not null
, deviceId          varchar(255)  not null
, status            enum(
                      'waiting',
                      'in-progress',
                      'canceled',
                      'finished'
                    )             not null
, assignedAt        timestamp     not null  default current_timestamp
, startedAt         timestamp
, primary key (id)
, foreign key (userId) references user(id)
, foreign key (deviceId) references device(id)
);
