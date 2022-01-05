DROP TABLE IF EXISTS utente CASCADE;
DROP TABLE IF EXISTS moto CASCADE;
DROP TABLE IF EXISTS noleggio CASCADE;
DROP TABLE IF EXISTS data_disponibile CASCADE;
DROP TABLE IF EXISTS ingressi_entrata CASCADE;
DROP TABLE IF EXISTS pista CASCADE;
DROP TABLE IF EXISTS lezione CASCADE;
DROP TABLE IF EXISTS ingressi_lezione CASCADE;


CREATE TABLE utente (
    cf CHAR(16),
    cognome VARCHAR(25),
    nome VARCHAR(25),
    data_nascita DATE,
    telefono VARCHAR(10),
    mail VARCHAR(35),
    password VARCHAR(255),

    PRIMARY KEY (cf)
);

CREATE TABLE moto (
    numero SMALLINT,
    cilindrata SMALLINT,
    marca VARCHAR(20),
    modello VARCHAR(20),

    PRIMARY KEY (numero)
);

CREATE TABLE noleggio (
    codice INT,
    data DATE,
    attrezzatura BOOLEAN,

    utente VARCHAR(20),
    moto SMALLINT,

    PRIMARY KEY (codice),

    CONSTRAINT fk_noleggio
    FOREIGN KEY (utente) REFERENCES utente(cf) ON DELETE CASCADE,
    FOREIGN KEY (moto) REFERENCES moto(numero) ON DELETE CASCADE
);

CREATE TABLE data_disponibile (
    data DATE,
    posti SMALLINT,

    PRIMARY KEY (data)
);

CREATE TABLE ingressi_entrata (
    codice INT,

    utente VARCHAR(20),
    data DATE,

    PRIMARY KEY (codice),

    CONSTRAINT fk_ingressi_entrata
    FOREIGN KEY (utente) REFERENCES utente(cf) ON DELETE CASCADE,
    FOREIGN KEY (data) REFERENCES data_disponibile(data) ON DELETE CASCADE
);

CREATE TABLE pista (
    id SMALLINT,
    lunghezza VARCHAR(5),
    descrizione TEXT,
    terreno VARCHAR(20),
    apertura TIME,
    chiusura TIME,

    PRIMARY KEY (id)
);

CREATE TABLE lezione (
    codiceLezione INT,
    data DATE,
    postiDisponibili SMALLINT,
    descrizione TEXT,
    corso VARCHAR(20),
    istruttore VARCHAR(20),

    pista SMALLINT,

    PRIMARY KEY (codiceLezione),

    CONSTRAINT fk_lezione
    FOREIGN KEY (pista) REFERENCES pista(id) ON DELETE CASCADE
);

CREATE TABLE ingressi_lezione (
    codice INT,

    utente VARCHAR(20),
    lezione INT,

    PRIMARY KEY (codice),

    CONSTRAINT fk_ingressi_lezione
    FOREIGN KEY (utente) REFERENCES utente(cf) ON DELETE CASCADE,
    FOREIGN KEY (lezione) REFERENCES lezione(codiceLezione) ON DELETE CASCADE
);
