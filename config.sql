CREATE DATABASE gestionzoo;

USE gestionzoo;

CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50),
    mot_de_passe VARCHAR(50),
    role VARCHAR(50)
);

CREATE TABLE enclos (
    id_enclos INT AUTO_INCREMENT PRIMARY KEY,
    nom_enclos VARCHAR(50),
    capacite INT
);

CREATE TABLE animal (
    id_animal INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    espece VARCHAR(50),
    id_enclos INT,
    FOREIGN KEY (id_enclos) REFERENCES enclos(id_enclos)
);

CREATE TABLE soin (
    id_soin INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT,
    type_soin VARCHAR(50),
    date_soin DATE,
    FOREIGN KEY (id_animal) REFERENCES animal(id_animal)
);