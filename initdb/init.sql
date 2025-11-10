-- Criação da tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    apelido VARCHAR(100),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de arquivos
CREATE TABLE IF NOT EXISTS uploaded_files (
    id SERIAL PRIMARY KEY,
    file_name TEXT NOT NULL,
    file_path TEXT NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uploaded_by INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    is_shared BOOLEAN DEFAULT FALSE
);

-- Criação da tabela de pastas
CREATE TABLE IF NOT EXISTS folders (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id INTEGER REFERENCES folders(id) ON DELETE CASCADE,
    owner_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Adiciona o vínculo de pasta nos arquivos
ALTER TABLE uploaded_files
ADD COLUMN IF NOT EXISTS folder_id INTEGER REFERENCES folders(id) ON DELETE SET NULL;

-- Inserir usuário admin (senha: password)
-- Hash bcrypt padrão do 'password'
INSERT INTO users (username, password_hash, apelido, email, created_at, updated_at)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrador',
    'admin@example.com',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
)
ON CONFLICT (username) DO NOTHING;
