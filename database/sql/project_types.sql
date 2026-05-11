-- Project Types and Projects Manual Query (if needed for direct database manipulation)
-- This file documents project_types, project_type_chapters, projects, and project_chapters table structures

CREATE TABLE project_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description LONGTEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_project_types_name (name),
    INDEX idx_project_types_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_type_chapters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_type_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NULL,
    admin_default_text LONGTEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 1,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    is_user_editable TINYINT(1) NOT NULL DEFAULT 1,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_project_type_chapters_project_type_id
        FOREIGN KEY (project_type_id) REFERENCES project_types(id)
        ON DELETE CASCADE,
    INDEX idx_project_type_chapters_project_type_id (project_type_id),
    INDEX idx_project_type_chapters_sort_order (sort_order),
    INDEX idx_project_type_chapters_is_required (is_required),
    INDEX idx_project_type_chapters_is_user_editable (is_user_editable),
    INDEX idx_project_type_chapters_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_type_id BIGINT UNSIGNED NOT NULL,
    created_by_user_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_projects_project_type_id
        FOREIGN KEY (project_type_id) REFERENCES project_types(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_projects_created_by_user_id
        FOREIGN KEY (created_by_user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    INDEX idx_projects_project_type_id (project_type_id),
    INDEX idx_projects_created_by_user_id (created_by_user_id),
    INDEX idx_projects_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_chapters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    project_type_chapter_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NULL,
    admin_default_text LONGTEXT NULL,
    user_content LONGTEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 1,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    is_user_editable TINYINT(1) NOT NULL DEFAULT 1,
    status ENUM('partial_completed', 'completed') NOT NULL DEFAULT 'partial_completed',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_project_chapters_project_id
        FOREIGN KEY (project_id) REFERENCES projects(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_project_chapters_project_type_chapter_id
        FOREIGN KEY (project_type_chapter_id) REFERENCES project_type_chapters(id)
        ON DELETE SET NULL,
    INDEX idx_project_chapters_project_id (project_id),
    INDEX idx_project_chapters_project_type_chapter_id (project_type_chapter_id),
    INDEX idx_project_chapters_sort_order (sort_order),
    INDEX idx_project_chapters_is_required (is_required),
    INDEX idx_project_chapters_is_user_editable (is_user_editable),
    INDEX idx_project_chapters_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_type_chapter_blocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_type_chapter_id BIGINT UNSIGNED NOT NULL,
    block_type ENUM('text', 'image') NOT NULL DEFAULT 'text',
    editable_by ENUM('admin', 'user', 'both') NOT NULL DEFAULT 'user',
    prompt_text LONGTEXT NULL,
    admin_content LONGTEXT NULL,
    caption_position ENUM('above', 'below') NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 1,
    is_required TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_project_type_chapter_blocks_chapter
        FOREIGN KEY (project_type_chapter_id) REFERENCES project_type_chapters(id)
        ON DELETE CASCADE,
    INDEX idx_ptcb_chapter_id (project_type_chapter_id),
    INDEX idx_ptcb_sort_order (sort_order),
    INDEX idx_ptcb_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_chapter_blocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_chapter_id BIGINT UNSIGNED NOT NULL,
    project_type_chapter_block_id BIGINT UNSIGNED NULL,
    block_type ENUM('text', 'image') NOT NULL DEFAULT 'text',
    editable_by ENUM('admin', 'user', 'both') NOT NULL DEFAULT 'user',
    prompt_text LONGTEXT NULL,
    admin_content LONGTEXT NULL,
    user_text LONGTEXT NULL,
    image_path VARCHAR(255) NULL,
    caption_position ENUM('above', 'below') NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 1,
    is_required TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    status ENUM('partial_completed', 'completed') NOT NULL DEFAULT 'partial_completed',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_project_chapter_blocks_chapter
        FOREIGN KEY (project_chapter_id) REFERENCES project_chapters(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_project_chapter_blocks_template_block
        FOREIGN KEY (project_type_chapter_block_id) REFERENCES project_type_chapter_blocks(id)
        ON DELETE SET NULL,
    INDEX idx_pcb_chapter_id (project_chapter_id),
    INDEX idx_pcb_sort_order (sort_order),
    INDEX idx_pcb_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data
INSERT INTO project_types (name, description, is_active, created_at, updated_at)
VALUES ('ISO 9001', 'Quality Management System template', 1, NOW(), NOW());

INSERT INTO project_type_chapters (project_type_id, title, description, admin_default_text, sort_order, is_required, is_user_editable, is_active, created_at, updated_at)
VALUES
    (1, '1. Scope', 'Define scope details for this company implementation.', 'The organization establishes and documents the scope of its quality management system, including products and services covered.', 1, 1, 1, 1, NOW(), NOW()),
    (1, '2. Normative References', 'List applicable standards and legal references.', 'This section identifies standards, regulations, and internal policies referenced by the management system.', 2, 1, 0, 1, NOW(), NOW()),
    (1, '3. Terms and Definitions', 'Document project-specific terms where needed.', 'Terms and definitions used in this project context are listed below.', 3, 0, 1, 1, NOW(), NOW());
