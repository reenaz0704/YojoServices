-- =====================================================================
-- Yojo Solutions — Audit, GRC & Cybersecurity platform
-- MariaDB 10.6+ / MySQL 5.7+  ·  utf8mb4  ·  InnoDB with foreign keys
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS hero_slides, frameworks, faq_categories, activity_log, login_attempts, assessment_frameworks, assessments, enquiry_notes, enquiry_services, enquiries,
  post_tag, tags, posts, post_categories, case_studies, testimonials, trust_items, stats, faqs, industry_service, industries,
  service_faqs, services, service_categories, pages, settings, users, roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------- Access control ----------
CREATE TABLE roles (
  id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(60) NOT NULL,
  level TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  role_id TINYINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  designation VARCHAR(120) NULL,
  bio TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  ip VARCHAR(45) NOT NULL,
  email VARCHAR(190) NOT NULL,
  success TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_login_ip (ip, created_at),
  INDEX idx_login_email (email, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_log (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  action VARCHAR(60) NOT NULL,
  entity VARCHAR(60) NOT NULL DEFAULT '',
  entity_id INT UNSIGNED NULL,
  details VARCHAR(500) NULL,
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_activity_created (created_at),
  CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Site configuration ----------
CREATE TABLE settings (
  setting_key VARCHAR(80) PRIMARY KEY,
  setting_value TEXT NULL,
  setting_group VARCHAR(40) NOT NULL DEFAULT 'general',
  label VARCHAR(120) NOT NULL,
  input_type VARCHAR(20) NOT NULL DEFAULT 'text',
  sort_order SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pages (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(120) NOT NULL UNIQUE,
  title VARCHAR(190) NOT NULL,
  content MEDIUMTEXT NULL,
  meta_description VARCHAR(300) NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Services ----------
CREATE TABLE service_categories (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  tagline VARCHAR(255) NULL,
  summary TEXT NULL,
  description TEXT NULL,
  engagement_note TEXT NULL,
  page_type ENUM('compliance','security','assurance','advisory') NOT NULL DEFAULT 'compliance',
  icon VARCHAR(40) NOT NULL DEFAULT 'shield',
  sort_order SMALLINT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  title VARCHAR(190) NOT NULL,
  menu_group ENUM('governance','cyber','it_audit','risk','privacy','advisory') NOT NULL DEFAULT 'governance',
  engagement_type VARCHAR(160) NULL,
  icon VARCHAR(40) NOT NULL DEFAULT 'shield',
  summary VARCHAR(400) NOT NULL,
  intro TEXT NULL,
  meaning TEXT NULL,
  why_needed TEXT NULL,
  requirements TEXT NULL,      -- one item per line, "Title :: detail"
  methodology TEXT NULL,
  scope TEXT NULL,
  security_areas TEXT NULL,
  deliverables TEXT NULL,
  reporting TEXT NULL,
  remediation TEXT NULL,
  benefits TEXT NULL,
  meta_title VARCHAR(190) NULL,
  meta_description VARCHAR(300) NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_services_cat (category_id, is_published, sort_order),
  INDEX idx_services_menu (menu_group),
  FULLTEXT KEY ft_services (title, summary),
  CONSTRAINT fk_services_cat FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE service_faqs (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  service_id INT UNSIGNED NOT NULL,
  question VARCHAR(300) NOT NULL,
  answer TEXT NOT NULL,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  INDEX idx_sfaq_service (service_id),
  CONSTRAINT fk_sfaq_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Industries ----------
CREATE TABLE industries (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  icon VARCHAR(40) NOT NULL DEFAULT 'building',
  summary VARCHAR(400) NOT NULL,
  overview TEXT NULL,
  risks TEXT NULL,
  compliance TEXT NULL,
  challenges TEXT NULL,
  outcomes TEXT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  sort_order SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE industry_service (
  industry_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (industry_id, service_id),
  CONSTRAINT fk_is_industry FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE CASCADE,
  CONSTRAINT fk_is_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Trust & credibility ----------
CREATE TABLE faqs (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  question VARCHAR(300) NOT NULL,
  answer TEXT NOT NULL,
  topic VARCHAR(60) NOT NULL DEFAULT 'General',
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  INDEX idx_faq_pub (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE stats (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  label VARCHAR(120) NOT NULL,
  value INT UNSIGNED NOT NULL DEFAULT 0,
  suffix VARCHAR(10) NULL,
  description VARCHAR(200) NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  sort_order SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE trust_items (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  type ENUM('certification','association','partner','client','framework') NOT NULL,
  name VARCHAR(160) NOT NULL,
  logo VARCHAR(255) NULL,
  url VARCHAR(255) NULL,
  note VARCHAR(255) NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  permission_on_file TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  INDEX idx_trust (type, is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE testimonials (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  client_name VARCHAR(120) NOT NULL,
  designation VARCHAR(120) NULL,
  company VARCHAR(160) NULL,
  content TEXT NOT NULL,
  image VARCHAR(255) NULL,
  rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE case_studies (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  industry_id INT UNSIGNED NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  title VARCHAR(190) NOT NULL,
  client_label VARCHAR(160) NULL,
  challenge TEXT NOT NULL,
  assessment TEXT NOT NULL,
  improvements TEXT NOT NULL,
  remediation TEXT NOT NULL,
  outcome TEXT NOT NULL,
  image VARCHAR(255) NULL,
  is_authorized TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cs_industry FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Resources / blog ----------
CREATE TABLE post_categories (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(300) NULL,
  sort_order SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  author_id INT UNSIGNED NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  title VARCHAR(220) NOT NULL,
  excerpt VARCHAR(400) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  thumbnail VARCHAR(255) NULL,
  reading_minutes TINYINT UNSIGNED NOT NULL DEFAULT 5,
  meta_title VARCHAR(190) NULL,
  meta_description VARCHAR(300) NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  views INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_posts_pub (status, published_at),
  INDEX idx_posts_cat (category_id),
  FULLTEXT KEY ft_posts (title, excerpt, content),
  CONSTRAINT fk_posts_cat FOREIGN KEY (category_id) REFERENCES post_categories(id) ON DELETE RESTRICT,
  CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tags (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_tag (
  post_id INT UNSIGNED NOT NULL,
  tag_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (post_id, tag_id),
  INDEX idx_pt_tag (tag_id),
  CONSTRAINT fk_pt_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_pt_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Leads: enquiries, consultations, proposals ----------
CREATE TABLE enquiries (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  reference VARCHAR(24) NOT NULL UNIQUE,
  type ENUM('contact','consultation','proposal','assessment') NOT NULL DEFAULT 'contact',
  full_name VARCHAR(120) NOT NULL,
  company VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  industry VARCHAR(120) NULL,
  company_size VARCHAR(40) NULL,
  message TEXT NULL,
  preferred_date DATE NULL,
  preferred_time VARCHAR(5) NULL,
  meeting_mode VARCHAR(30) NULL,
  source_page VARCHAR(255) NULL,
  consent TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('new','in_review','contacted','proposal_sent','won','closed','spam') NOT NULL DEFAULT 'new',
  assigned_to INT UNSIGNED NULL,
  fingerprint CHAR(64) NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_enq_status (status, created_at),
  INDEX idx_enq_email (email),
  INDEX idx_enq_fp (fingerprint, created_at),
  INDEX idx_enq_ip (ip_address, created_at),
  CONSTRAINT fk_enq_assignee FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enquiry_services (
  enquiry_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (enquiry_id, service_id),
  CONSTRAINT fk_es_enquiry FOREIGN KEY (enquiry_id) REFERENCES enquiries(id) ON DELETE CASCADE,
  CONSTRAINT fk_es_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enquiry_notes (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  enquiry_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  note TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_en_enquiry FOREIGN KEY (enquiry_id) REFERENCES enquiries(id) ON DELETE CASCADE,
  CONSTRAINT fk_en_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Audit readiness self-assessment ----------
CREATE TABLE assessments (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  reference VARCHAR(24) NOT NULL UNIQUE,
  organization_type VARCHAR(60) NOT NULL,
  industry VARCHAR(120) NOT NULL,
  company_size VARCHAR(40) NOT NULL,
  country VARCHAR(80) NOT NULL,
  other_requirement VARCHAR(255) NULL,
  answers JSON NOT NULL,
  domain_scores JSON NOT NULL,
  score TINYINT UNSIGNED NOT NULL,
  readiness_band VARCHAR(40) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  company VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(30) NULL,
  designation VARCHAR(120) NULL,
  consent TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('new','reviewed','contacted','closed') NOT NULL DEFAULT 'new',
  reviewer_notes TEXT NULL,
  ip_address VARCHAR(45) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_assess_status (status, created_at),
  INDEX idx_assess_ip (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE assessment_frameworks (
  assessment_id INT UNSIGNED NOT NULL,
  framework VARCHAR(30) NOT NULL,
  PRIMARY KEY (assessment_id, framework),
  INDEX idx_af_framework (framework),
  CONSTRAINT fk_af_assessment FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
