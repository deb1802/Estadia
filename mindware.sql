DROP DATABASE IF EXISTS mindware;
CREATE DATABASE IF NOT EXISTS mindware;
USE mindware;

CREATE TABLE Usuarios (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellido VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    fechaNacimiento DATE,
    sexo ENUM('masculino', 'femenino', 'otro'),
    telefono VARCHAR(20),
    tipoUsuario ENUM('administrador', 'medico', 'paciente') NOT NULL,
    estadoCuenta ENUM('activo', 'inactivo') DEFAULT 'activo',
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP  -- 🔹 Nueva columna, se llena sola
);

DROP TABLE IF EXISTS Medicos;
CREATE TABLE Medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,           -- ID propio del médico
    usuario_id INT NOT NULL UNIQUE,              -- FK hacia Usuarios.idUsuario
    cedulaProfesional VARCHAR(20),
    especialidad VARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(idUsuario)
);


CREATE TABLE Pacientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL UNIQUE,
  medico_id INT NOT NULL,
  padecimientos TEXT,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(idUsuario)
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pacientes_medico
    FOREIGN KEY (medico_id) REFERENCES Medicos(id)
      ON UPDATE CASCADE ON DELETE RESTRICT
);


CREATE TABLE Tutores (
    idTutor INT PRIMARY KEY AUTO_INCREMENT,
    nombreCompleto VARCHAR(100),
    parentesco VARCHAR(50),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion TEXT,
    observaciones TEXT,
    fkPaciente INT,
    FOREIGN KEY (fkPaciente) REFERENCES Pacientes(id)
);

INSERT INTO Usuarios (nombre, apellido, email, contrasena, tipoUsuario, estadoCuenta) VALUES
('Debanni', 'Morales', 'modo220339@upemor.edu.mx', '$2y$12$h8sHmrEh34sdcB9QgfzrzuRb5qaBYuj0oWzGMfnhMVbdgkfqa2JxW', 'administrador', 'activo'),
('Patrick', 'Perez', 'prpo221479@upemor.edu.mx', '$2y$12$h8sHmrEh34sdcB9QgfzrzuRb5qaBYuj0oWzGMfnhMVbdgkfqa2JxW', 'administrador', 'activo'),
('Debanni', 'Morales', 'deb@medico.com', '$2y$12$h8sHmrEh34sdcB9QgfzrzuRb5qaBYuj0oWzGMfnhMVbdgkfqa2JxW', 'medico', 'activo'),
('Patrick', 'Pérez', 'pat@medico.com', '$2y$12$h8sHmrEh34sdcB9QgfzrzuRb5qaBYuj0oWzGMfnhMVbdgkfqa2JxW', 'medico', 'activo'),
('Debanni', 'Morales', 'deb@paciente.com', '$2y$12$h8sHmrEh34sdcB9QgfzrzuRb5qaBYuj0oWzGMfnhMVbdgkfqa2JxW', 'paciente', 'activo'),
('Patrick', 'Pérez', 'pat@paciente.com', '$2y$12$h8sHmrEh34sdcB9QgfzrzuRb5qaBYuj0oWzGMfnhMVbdgkfqa2JxW', 'paciente', 'activo');

INSERT INTO Medicos (usuario_id, cedulaProfesional, especialidad)
VALUES
(3, 'MED12345', 'Psicología Clínica'),
(4, 'MED67890', 'Psiquiatría');

INSERT INTO Pacientes (usuario_id, medico_id, padecimientos)
VALUES
(5, 1, 'Ansiedad generalizada'),
(6, 2, 'Estrés laboral');

CREATE TABLE Medicamentos (
  idMedicamento INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100),
  presentacion VARCHAR(50),
  indicaciones TEXT,
  efectosSecundarios TEXT,
  imagenMedicamento VARCHAR(255)
);

CREATE TABLE RecetasMedicas (
  idReceta INT PRIMARY KEY AUTO_INCREMENT,
  fecha DATE NOT NULL,
  observaciones TEXT,
  fkMedico INT NOT NULL,
  fkPaciente INT NOT NULL,
  FOREIGN KEY (fkMedico)
    REFERENCES Medicos(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  FOREIGN KEY (fkPaciente)
    REFERENCES Pacientes(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);

CREATE TABLE Detalle_Medicamento (
  idDetalleMedicamento INT PRIMARY KEY AUTO_INCREMENT,
  fkReceta INT NOT NULL,
  fkMedicamento INT NULL,
  dosis VARCHAR(100) NOT NULL,
  frecuencia VARCHAR(100) NOT NULL,
  duracion VARCHAR(100) NOT NULL,
  FOREIGN KEY (fkReceta)
    REFERENCES RecetasMedicas(idReceta)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (fkMedicamento)
    REFERENCES Medicamentos(idMedicamento)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT uq_detalle_receta_medicamento UNIQUE (fkReceta, fkMedicamento)
);


/* =========================================================
   1. TABLA: Tests
   ---------------------------------------------------------
   Contiene la información general de cada test creado
   por un médico (nombre, tipo, descripción, etc.)
   ========================================================= */
 CREATE TABLE Tests (
  idTest INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  tipoTrastorno VARCHAR(120),
  descripcion TEXT,
  estado ENUM('activo','inactivo') DEFAULT 'activo',
  fechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fkMedico INT NOT NULL,
  FOREIGN KEY (fkMedico) REFERENCES Medicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* =========================================================
   2. TABLA: PreguntasTest
   ---------------------------------------------------------
   Contiene las preguntas que pertenecen a un test.
   ========================================================= */
CREATE TABLE PreguntasTest (
  idPregunta INT PRIMARY KEY AUTO_INCREMENT,
  fkTest INT NOT NULL,
  texto TEXT NOT NULL,
  tipo ENUM('opcion_unica','opcion_multiple','abierta') DEFAULT 'opcion_unica',
  orden INT NOT NULL DEFAULT 1,
  FOREIGN KEY (fkTest) REFERENCES Tests(idTest) ON DELETE CASCADE,
  INDEX (fkTest, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* =========================================================
   3. TABLA: OpcionesPregunta
   ---------------------------------------------------------
   Guarda las opciones posibles por cada pregunta y su puntaje.
   ========================================================= */
CREATE TABLE OpcionesPregunta (
  idOpcion INT PRIMARY KEY AUTO_INCREMENT,
  fkPregunta INT NOT NULL,
  etiqueta VARCHAR(200) NOT NULL,
  valor VARCHAR(100),
  puntaje INT NOT NULL DEFAULT 0,
  orden INT NOT NULL DEFAULT 1,
  FOREIGN KEY (fkPregunta) REFERENCES PreguntasTest(idPregunta) ON DELETE CASCADE,
  INDEX (fkPregunta, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* =========================================================
   4. TABLA: RangosTest
   ---------------------------------------------------------
   Define los intervalos de puntaje total y diagnóstico sugerido.
   ========================================================= */
CREATE TABLE RangosTest (
  idRango INT PRIMARY KEY AUTO_INCREMENT,
  fkTest INT NOT NULL,
  minPuntaje INT NOT NULL,
  maxPuntaje INT NOT NULL,
  diagnostico VARCHAR(150) NOT NULL,
  descripcion TEXT,
  FOREIGN KEY (fkTest) REFERENCES Tests(idTest) ON DELETE CASCADE,
  INDEX (fkTest, minPuntaje, maxPuntaje)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* =========================================================
   5. TABLA: AsignacionesTest
   ---------------------------------------------------------
   Vincula un test con un paciente. Aquí se guarda el puntaje
   total, el diagnóstico sugerido y el diagnóstico confirmado
   por el médico.
   ========================================================= */
CREATE TABLE AsignacionesTest (
  idAsignacionTest INT PRIMARY KEY AUTO_INCREMENT,
  fkTest INT NOT NULL,
  fkPaciente INT NOT NULL,
  fechaAsignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fechaRespuesta DATETIME NULL,
  puntajeTotal INT NULL,
  diagnosticoSugerido VARCHAR(150) NULL,
  diagnosticoConfirmado VARCHAR(150) NULL,
  confirmadoPor INT NULL,
  fechaConfirmacion DATETIME NULL,
  notasClinicas TEXT NULL,
  subescalas JSON NULL,
  FOREIGN KEY (fkTest) REFERENCES Tests(idTest),
  FOREIGN KEY (fkPaciente) REFERENCES Pacientes(id),
  FOREIGN KEY (confirmadoPor) REFERENCES Medicos(id),
  INDEX (fkTest, fkPaciente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* =========================================================
   6. TABLA: RespuestasTest
   ---------------------------------------------------------
   Guarda las respuestas que el paciente da a cada pregunta
   de su test asignado, con el puntaje obtenido en cada una.
   ========================================================= */
CREATE TABLE RespuestasTest (
  idRespuesta INT PRIMARY KEY AUTO_INCREMENT,
  fkAsignacionTest INT NOT NULL,
  fkPregunta INT NOT NULL,
  fkOpcion INT NULL,
  respuestaAbierta TEXT NULL,
  puntajeObtenido INT NOT NULL DEFAULT 0,
  FOREIGN KEY (fkAsignacionTest) REFERENCES AsignacionesTest(idAsignacionTest) ON DELETE CASCADE,
  FOREIGN KEY (fkPregunta) REFERENCES PreguntasTest(idPregunta),
  FOREIGN KEY (fkOpcion) REFERENCES OpcionesPregunta(idOpcion),
  INDEX (fkAsignacionTest),
  INDEX (fkPregunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE Actividades (
    idActividad INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(100),
    tipoContenido ENUM('audio', 'video', 'lectura', 'ejercicio'),
    categoriaTerapeutica VARCHAR(100),
    diagnosticoDirigido VARCHAR(100),
    nivelSeveridad VARCHAR(50),
    recurso TEXT,
    fkMedico INT,
    FOREIGN KEY (fkMedico) REFERENCES Medicos(idMedico)
);

CREATE TABLE AsignacionActividad (
    idAsignacionActividad INT PRIMARY KEY AUTO_INCREMENT,
    fkActividad INT NOT NULL,
    fkPaciente INT NOT NULL,
    fkMedico INT NOT NULL,
    fechaAsignacion DATE NOT NULL,
    fechaFinalizacion DATE NULL,
    estado ENUM('pendiente', 'completada') DEFAULT 'pendiente',
    indicaciones TEXT NULL,
    FOREIGN KEY (fkActividad) REFERENCES Actividades(idActividad),
    FOREIGN KEY (fkPaciente) REFERENCES Pacientes(id),
    FOREIGN KEY (fkMedico) REFERENCES Medicos(id)
);


CREATE TABLE Citas (
    idCita INT PRIMARY KEY AUTO_INCREMENT,
    fkMedico INT,
    fkPaciente INT,
    fechaHora DATETIME NOT NULL,
    motivo TEXT NOT NULL,
    ubicacion VARCHAR(150) NOT NULL,
    estado ENUM('programada', 'realizada', 'cancelada') DEFAULT 'programada',
    FOREIGN KEY (fkMedico) REFERENCES Medicos(id),
    FOREIGN KEY (fkPaciente) REFERENCES Pacientes(id)
);


CREATE TABLE Emociones (
    idEmocion INT PRIMARY KEY AUTO_INCREMENT,
    fkActividad INT,
    fkPaciente INT,
    fechaHoraRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    emocionesExperimentadas TEXT, -- ejemplo: JSON con ["feliz", "confundido"]
    intensidad INT, -- escala 1-5
    comentario TEXT,
    FOREIGN KEY (fkActividad) REFERENCES Actividades(idActividad),
    FOREIGN KEY (fkPaciente) REFERENCES Pacientes(idPaciente)
);

CREATE TABLE Expedientes (
    idExpediente INT PRIMARY KEY AUTO_INCREMENT,
    fkPaciente INT,
    antecedentes TEXT,
    diagnosticos TEXT,
    notasClinicas TEXT,
    historialCitas TEXT,
    testsAplicados TEXT,
    actividadesAsignadas TEXT,
    respuestasEmocionales TEXT,
    medicamentosPrescritos TEXT,
    observaciones TEXT,
    fechaActualizacion DATE,
    FOREIGN KEY (fkPaciente) REFERENCES Pacientes(idPaciente)
);


--testimonios y sus repsuestas 
CREATE TABLE Testimonios (
    idTestimonio INT PRIMARY KEY AUTO_INCREMENT,
    fkPaciente INT NOT NULL,
    fecha DATE DEFAULT CURRENT_DATE,
    contenido TEXT NOT NULL,
    FOREIGN KEY (fkPaciente) REFERENCES Pacientes(idPaciente)
      ON UPDATE CASCADE ON DELETE RESTRICT
);



CREATE TABLE RespuestasTestimonio (
  idRespuesta INT AUTO_INCREMENT PRIMARY KEY,
  fkTestimonio INT NOT NULL,
  fkPaciente INT NOT NULL,
  contenido TEXT NOT NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (fkTestimonio) REFERENCES Testimonios(idTestimonio)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (fkPaciente) REFERENCES Pacientes(idPaciente)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX (fkTestimonio),
  INDEX (fkPaciente)
);




CREATE TABLE Notificaciones (
    idNotificacion INT PRIMARY KEY AUTO_INCREMENT,
    fkUsuario INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo ENUM('sistema', 'correo') DEFAULT 'sistema',
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    leida TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (fkUsuario) REFERENCES Usuarios(idUsuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

