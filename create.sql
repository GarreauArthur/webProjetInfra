# Postgresql ne gère pas la case, sauf si on entoure les mots par des guillemets
# Ici on met les majuscules pour faire le parallèle avec la partie java, mais 
# elles ne sont pas utiles.

DROP TABLE Reservations;
DROP TABLE Chambres;
DROP TABLE Hotels;
DROP TABLE Clients;


CREATE TABLE Clients (

	id        serial,
	prenom    VARCHAR(50),
	nom       VARCHAR(100) NOT NULL,
	mail      VARCHAR(256),
	telephone VARCHAR(20),

	CONSTRAINT pk_Clients PRIMARY KEY(id)

);


CREATE TABLE Hotels (

	id        serial,
	nom       VARCHAR(100),
	adresse   text,
	nbChambre smallint,

	CONSTRAINT pk_Hotels PRIMARY KEY(id)

);


CREATE TABLE Chambres (

	numeroChambre integer,
	hotel         integer,
	nbLitSimple   smallint DEFAULT 0,
	nbLitDouble   smallint DEFAULT 0,
	prix          money,
	gammeChambre  VARCHAR(100),
	etage         smallint,

	CONSTRAINT pk_Chambres_id PRIMARY KEY(numeroChambre,hotel),
	CONSTRAINT fk_Hotels FOREIGN KEY (hotel) REFERENCES Hotels(id)

);

CREATE TABLE Reservations (

	id        serial,
	dateDebut timestamp,
	dateFIn   timestamp,
	client    integer,
	chambre   integer,
	hotel     integer,

	CONSTRAINT pk_Reservations_id PRIMARY KEY(id),

	CONSTRAINT fk_Clients  FOREIGN KEY (client)         REFERENCES Clients(id),
	CONSTRAINT fk_Chambres FOREIGN KEY (chambre, hotel) REFERENCES Chambres(numeroChambre, hotel)

);
