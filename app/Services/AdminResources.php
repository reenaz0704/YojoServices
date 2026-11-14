<?php
namespace App\Services;

/**
 * Declarative definitions for every content type managed through the generic admin CRUD.
 *
 * Field types: text, slug, email, url, number, textarea, lines, rich, select, bool, image,
 *              datetime, password, icon, tags (posts only), pivot (many-to-many).
 * Field options: label, required, max, help, options (array | 'sql:...'), from (slug source), list (show in table).
 */
class AdminResources
{
    public static function all(): array
    {
        $lines = 'One item per line. Use "Title :: description" for a titled item.';
        $themes = ['navy' => 'Navy', 'indigo' => 'Indigo', 'royal' => 'Royal blue', 'teal' => 'Teal', 'violet' => 'Violet'];
        return [
            'hero-slides' => [
                'label' => 'Homepage Slides', 'singular' => 'Slide', 'table' => 'hero_slides', 'icon' => 'layers', 'group' => 'Homepage',
                'order' => 'sort_order', 'search' => ['title', 'label'],
                'columns' => ['label' => 'Slide', 'title' => 'Heading', 'duration_seconds' => 'Seconds', 'is_published' => 'Visible'],
                'notice' => 'Slides play in the order shown (use the arrows to reorder). Links can be a site path such as services/cybersecurity-audit or a full https:// URL.',
                'fields' => [
                    'label' => ['type' => 'text', 'label' => 'Slide label (small text above the heading)', 'required' => true, 'max' => 120],
                    'title' => ['type' => 'text', 'label' => 'Main heading', 'required' => true, 'max' => 190],
                    'subtitle' => ['type' => 'text', 'label' => 'Subheading', 'max' => 255],
                    'description' => ['type' => 'textarea', 'label' => 'Supporting text', 'rows' => 3],
                    'cta_label' => ['type' => 'text', 'label' => 'Primary button text', 'required' => true, 'max' => 80],
                    'cta_url' => ['type' => 'text', 'label' => 'Primary button link', 'required' => true, 'max' => 255],
                    'cta2_label' => ['type' => 'text', 'label' => 'Secondary button text', 'max' => 80],
                    'cta2_url' => ['type' => 'text', 'label' => 'Secondary button link', 'max' => 255],
                    'image' => ['type' => 'image', 'label' => 'Background image (optional, 1920x1080 recommended)'],
                    'theme' => ['type' => 'select', 'label' => 'Colour theme', 'required' => true, 'options' => $themes],
                    'icon' => ['type' => 'icon', 'label' => 'Motion-graphic icon'],
                    'duration_seconds' => ['type' => 'number', 'label' => 'Seconds on screen (3-10)'],
                    'sort_order' => ['type' => 'number', 'label' => 'Order'],
                    'is_published' => ['type' => 'bool', 'label' => 'Visible', 'default' => 1],
                ],
            ],
            'frameworks' => [
                'label' => 'Frameworks', 'singular' => 'Framework', 'table' => 'frameworks', 'icon' => 'shield-check', 'group' => 'Homepage',
                'order' => 'sort_order', 'search' => ['name', 'short_name'], 'preview' => 'frameworks/{slug}',
                'columns' => ['name' => 'Name', 'region' => 'Region', 'is_published' => 'Published'],
                'notice' => 'Do not upload accreditation or certification-body logos unless you are authorised to use them.',
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 120],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'name', 'max' => 80],
                    'short_name' => ['type' => 'text', 'label' => 'Short name (card and scrolling text)', 'required' => true, 'max' => 40],
                    'region' => ['type' => 'text', 'label' => 'Region / issuer', 'max' => 80],
                    'overview' => ['type' => 'textarea', 'label' => 'Framework overview', 'required' => true],
                    'purpose' => ['type' => 'textarea', 'label' => 'Purpose'],
                    'requirements' => ['type' => 'lines', 'label' => 'Key requirements', 'help' => 'One per line'],
                    'why_needed' => ['type' => 'textarea', 'label' => 'Why organisations need it'],
                    'how_we_help' => ['type' => 'textarea', 'label' => 'How Yojo Solutions can support'],
                    'service_id' => ['type' => 'select', 'label' => 'Related service', 'options' => 'sql:SELECT s.id, s.title FROM services s WHERE s.is_published = 1 ORDER BY s.title'],
                    'icon' => ['type' => 'icon', 'label' => 'Icon'],
                    'image' => ['type' => 'image', 'label' => 'Image (optional)'],
                    'sort_order' => ['type' => 'number', 'label' => 'Display order'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published', 'default' => 1],
                ],
            ],
            'services' => [
                'label' => 'Services', 'singular' => 'Service', 'table' => 'services', 'icon' => 'layers', 'group' => 'Content',
                'order' => 'category_id, sort_order', 'search' => ['title', 'summary'], 'preview' => 'services/{category_slug}/{slug}',
                'list_join' => 'SELECT t.*, c.name AS category_name, c.slug AS category_slug FROM services t JOIN service_categories c ON c.id = t.category_id',
                'columns' => ['title' => 'Title', 'category_name' => 'Division', 'is_published' => 'Published'], 'sort_scope' => 'category_id',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'required' => true, 'max' => 190],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'title', 'max' => 140],
                    'category_id' => ['type' => 'select', 'label' => 'Division (main service category)', 'required' => true, 'options' => 'sql:SELECT id, name FROM service_categories ORDER BY is_published DESC, sort_order'],
                    'engagement_type' => ['type' => 'text', 'label' => 'Engagement type', 'help' => 'e.g. Consulting · Readiness · Internal audit', 'max' => 160],
                    'icon' => ['type' => 'icon', 'label' => 'Icon'],
                    'image' => ['type' => 'image', 'label' => 'Image (optional)'],
                    'summary' => ['type' => 'textarea', 'label' => 'Short description', 'required' => true, 'max' => 400, 'rows' => 2],
                    'intro' => ['type' => 'textarea', 'label' => 'Introduction / overview', 'required' => true],
                    'meaning' => ['type' => 'textarea', 'label' => 'What it means / involves'],
                    'why_needed' => ['type' => 'textarea', 'label' => 'Why organisations need it'],
                    'requirements' => ['type' => 'lines', 'label' => 'Key activities / major requirements', 'help' => $lines],
                    'scope' => ['type' => 'lines', 'label' => 'Scope of work / assessment scope', 'help' => $lines],
                    'methodology' => ['type' => 'lines', 'label' => 'Methodology steps', 'help' => $lines],
                    'security_areas' => ['type' => 'lines', 'label' => 'Security areas covered (VAPT)', 'help' => $lines],
                    'reporting' => ['type' => 'lines', 'label' => 'Reporting process (VAPT)', 'help' => $lines],
                    'remediation' => ['type' => 'lines', 'label' => 'Remediation guidance (VAPT)', 'help' => $lines],
                    'deliverables' => ['type' => 'lines', 'label' => 'Expected deliverables', 'help' => $lines],
                    'benefits' => ['type' => 'lines', 'label' => 'Benefits', 'help' => $lines],
                    'meta_title' => ['type' => 'text', 'label' => 'SEO title', 'max' => 190],
                    'meta_description' => ['type' => 'textarea', 'label' => 'SEO description', 'max' => 300, 'rows' => 2],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_featured' => ['type' => 'bool', 'label' => 'Featured'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published', 'default' => 1],
                ],
            ],
            'service-faqs' => [
                'label' => 'Service FAQs', 'singular' => 'Service FAQ', 'table' => 'service_faqs', 'icon' => 'info', 'group' => 'Content',
                'order' => 'service_id, sort_order', 'search' => ['question'],
                'list_join' => 'SELECT t.*, s.title AS service_title FROM service_faqs t JOIN services s ON s.id = t.service_id',
                'columns' => ['question' => 'Question', 'service_title' => 'Service'],
                'fields' => [
                    'service_id' => ['type' => 'select', 'label' => 'Service', 'required' => true, 'options' => 'sql:SELECT id, title FROM services ORDER BY title'],
                    'question' => ['type' => 'text', 'label' => 'Question', 'required' => true, 'max' => 300],
                    'answer' => ['type' => 'textarea', 'label' => 'Answer', 'required' => true],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                ],
            ],
            'service-categories' => [
                'label' => 'Service Divisions', 'singular' => 'Division', 'table' => 'service_categories', 'icon' => 'grid', 'group' => 'Content',
                'order' => 'sort_order', 'search' => ['name'], 'preview' => 'services/{slug}',
                'columns' => ['name' => 'Name', 'page_type' => 'Page type', 'is_published' => 'Published'],
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 160],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'name', 'max' => 120],
                    'tagline' => ['type' => 'text', 'label' => 'Tagline', 'max' => 255],
                    'icon' => ['type' => 'icon', 'label' => 'Icon'],
                    'image' => ['type' => 'image', 'label' => 'Image (optional)'],
                    'page_type' => ['type' => 'select', 'label' => 'Page type', 'options' => ['compliance' => 'Compliance', 'security' => 'Security testing', 'assurance' => 'Assurance / IT', 'advisory' => 'Advisory', 'legal' => 'Cyber legal (shows legal-advice disclaimer)']],
                    'summary' => ['type' => 'textarea', 'label' => 'Short summary', 'rows' => 2],
                    'description' => ['type' => 'textarea', 'label' => 'Description'],
                    'engagement_note' => ['type' => 'textarea', 'label' => 'Engagement / authorisation note', 'help' => 'Shown on every service page in this category.'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published', 'default' => 1],
                ],
            ],
            'industries' => [
                'label' => 'Industries', 'singular' => 'Industry', 'table' => 'industries', 'icon' => 'building', 'group' => 'Content',
                'order' => 'sort_order', 'search' => ['name'], 'preview' => 'industries/{slug}',
                'columns' => ['name' => 'Name', 'is_published' => 'Published'],
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 160],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'name', 'max' => 120],
                    'icon' => ['type' => 'icon', 'label' => 'Icon'],
                    'image' => ['type' => 'image', 'label' => 'Industry image (optional - an illustration is shown otherwise)'],
                    'summary' => ['type' => 'textarea', 'label' => 'Short description', 'required' => true, 'max' => 400, 'rows' => 2],
                    'risk_tags' => ['type' => 'text', 'label' => 'Risk indicators (card)', 'help' => 'Separate with a middle dot, e.g. Fraud · Ransomware', 'max' => 255],
                    'overview' => ['type' => 'textarea', 'label' => 'Industry overview'],
                    'risks' => ['type' => 'lines', 'label' => 'Common cybersecurity risks', 'help' => 'One per line'],
                    'governance' => ['type' => 'lines', 'label' => 'Governance challenges', 'help' => 'One per line'],
                    'compliance' => ['type' => 'lines', 'label' => 'Relevant compliance requirements', 'help' => 'One per line'],
                    'solutions' => ['type' => 'lines', 'label' => 'Cybersecurity solutions', 'help' => 'One per line'],
                    'approach' => ['type' => 'lines', 'label' => 'Consulting approach (steps)', 'help' => 'One per line'],
                    'challenges' => ['type' => 'lines', 'label' => 'Common audit challenges', 'help' => 'One per line'],
                    'outcomes' => ['type' => 'lines', 'label' => 'Expected business outcomes', 'help' => 'One per line'],
                    'services' => ['type' => 'pivot', 'label' => 'Recommended services', 'pivot' => ['industry_service', 'industry_id', 'service_id'], 'options' => 'sql:SELECT id, title FROM services ORDER BY title'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published', 'default' => 1],
                ],
            ],
            'posts' => [
                'label' => 'Articles', 'singular' => 'Article', 'table' => 'posts', 'icon' => 'file-text', 'group' => 'Latest Insights',
                'order' => 'COALESCE(published_at, created_at) DESC', 'search' => ['title', 'excerpt'], 'preview' => 'insights/{slug}',
                'list_join' => 'SELECT t.*, c.name AS category_name FROM posts t JOIN post_categories c ON c.id = t.category_id',
                'columns' => ['title' => 'Title', 'category_name' => 'Category', 'status' => 'Status', 'published_at' => 'Published', 'views' => 'Views'],
                'toggle' => ['status', 'published', 'draft'],
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'required' => true, 'max' => 220],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'title', 'max' => 190],
                    'category_id' => ['type' => 'select', 'label' => 'Category', 'required' => true, 'options' => 'sql:SELECT id, name FROM post_categories ORDER BY sort_order'],
                    'author_id' => ['type' => 'select', 'label' => 'Author', 'options' => 'sql:SELECT id, name FROM users WHERE is_active = 1 ORDER BY name'],
                    'excerpt' => ['type' => 'textarea', 'label' => 'Short description', 'required' => true, 'max' => 400, 'rows' => 2],
                    'content' => ['type' => 'rich', 'label' => 'Content', 'required' => true],
                    'thumbnail' => ['type' => 'image', 'label' => 'Thumbnail'],
                    'tags' => ['type' => 'tags', 'label' => 'Tags', 'help' => 'Comma-separated'],
                    'reading_minutes' => ['type' => 'number', 'label' => 'Reading time (minutes)'],
                    'meta_title' => ['type' => 'text', 'label' => 'SEO title', 'max' => 190],
                    'meta_description' => ['type' => 'textarea', 'label' => 'SEO description', 'max' => 300, 'rows' => 2],
                    'status' => ['type' => 'select', 'label' => 'Status', 'required' => true, 'options' => ['draft' => 'Draft', 'published' => 'Published']],
                    'published_at' => ['type' => 'datetime', 'label' => 'Publication date', 'help' => 'Future dates schedule the article.'],
                ],
            ],
            'post-categories' => [
                'label' => 'Article Categories', 'singular' => 'Category', 'table' => 'post_categories', 'icon' => 'tag', 'group' => 'Latest Insights',
                'order' => 'sort_order', 'search' => ['name'], 'columns' => ['name' => 'Name', 'slug' => 'Slug'],
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 120],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'name', 'max' => 120],
                    'description' => ['type' => 'textarea', 'label' => 'Description', 'max' => 300, 'rows' => 2],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                ],
            ],
            'case-studies' => [
                'label' => 'Case Studies', 'singular' => 'Case Study', 'table' => 'case_studies', 'icon' => 'briefcase', 'group' => 'Credibility',
                'order' => 'sort_order', 'search' => ['title'], 'preview' => 'case-studies/{slug}',
                'columns' => ['title' => 'Title', 'is_authorized' => 'Client-approved', 'is_published' => 'Published'],
                'notice' => 'Publish client details only with written authorisation. Unauthorised items are shown with an "Illustrative" label.',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'required' => true, 'max' => 190],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'title', 'max' => 140],
                    'industry_id' => ['type' => 'select', 'label' => 'Industry', 'options' => 'sql:SELECT id, name FROM industries ORDER BY sort_order'],
                    'client_label' => ['type' => 'text', 'label' => 'Client label', 'help' => 'e.g. "Illustrative scenario" or an approved client description', 'max' => 160],
                    'challenge' => ['type' => 'textarea', 'label' => 'Business challenge', 'required' => true],
                    'assessment' => ['type' => 'textarea', 'label' => 'Assessment performed', 'required' => true],
                    'improvements' => ['type' => 'lines', 'label' => 'Identified improvement areas', 'required' => true],
                    'remediation' => ['type' => 'lines', 'label' => 'Remediation approach', 'required' => true],
                    'outcome' => ['type' => 'textarea', 'label' => 'Outcome', 'required' => true],
                    'image' => ['type' => 'image', 'label' => 'Image'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_authorized' => ['type' => 'bool', 'label' => 'Client has authorised publication'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published'],
                ],
            ],
            'testimonials' => [
                'label' => 'Testimonials', 'singular' => 'Testimonial', 'table' => 'testimonials', 'icon' => 'quote', 'group' => 'Credibility',
                'order' => 'sort_order', 'search' => ['client_name', 'company'],
                'columns' => ['client_name' => 'Client', 'company' => 'Company', 'is_verified' => 'Verified', 'is_published' => 'Published'],
                'notice' => 'Only publish testimonials the client has approved. Unverified items show a "Sample" label on the website.',
                'fields' => [
                    'client_name' => ['type' => 'text', 'label' => 'Client name', 'required' => true, 'max' => 120],
                    'designation' => ['type' => 'text', 'label' => 'Designation', 'max' => 120],
                    'company' => ['type' => 'text', 'label' => 'Company', 'max' => 160],
                    'content' => ['type' => 'textarea', 'label' => 'Testimonial', 'required' => true],
                    'image' => ['type' => 'image', 'label' => 'Profile image'],
                    'rating' => ['type' => 'number', 'label' => 'Rating (1–5)'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_verified' => ['type' => 'bool', 'label' => 'Verified & approved by client'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published'],
                ],
            ],
            'trust-items' => [
                'label' => 'Trust & Logos', 'singular' => 'Trust item', 'table' => 'trust_items', 'icon' => 'badge', 'group' => 'Credibility',
                'order' => 'type, sort_order', 'search' => ['name'],
                'columns' => ['name' => 'Name', 'type' => 'Type', 'is_verified' => 'Verified', 'is_published' => 'Published'],
                'notice' => 'Do not publish certifications, memberships, partnerships or client logos unless verified and permitted.',
                'fields' => [
                    'type' => ['type' => 'select', 'label' => 'Type', 'required' => true, 'options' => ['certification' => 'Certification / accreditation', 'association' => 'Industry association', 'partner' => 'Technology partner', 'client' => 'Client logo', 'framework' => 'Framework / standard']],
                    'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 160],
                    'logo' => ['type' => 'image', 'label' => 'Logo'],
                    'url' => ['type' => 'url', 'label' => 'Link', 'max' => 255],
                    'note' => ['type' => 'text', 'label' => 'Internal note / tooltip', 'max' => 255],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_verified' => ['type' => 'bool', 'label' => 'Verified'],
                    'permission_on_file' => ['type' => 'bool', 'label' => 'Written permission on file (clients/partners)'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published'],
                ],
            ],
            'stats' => [
                'label' => 'Statistics', 'singular' => 'Statistic', 'table' => 'stats', 'icon' => 'chart', 'group' => 'Credibility',
                'order' => 'sort_order', 'search' => ['label'],
                'columns' => ['label' => 'Label', 'value' => 'Value', 'is_verified' => 'Verified', 'is_published' => 'Published'],
                'notice' => 'Only the first four published statistics appear on the homepage. Never publish unverified achievement figures.',
                'fields' => [
                    'label' => ['type' => 'text', 'label' => 'Label', 'required' => true, 'max' => 120],
                    'value' => ['type' => 'number', 'label' => 'Value', 'required' => true],
                    'suffix' => ['type' => 'text', 'label' => 'Suffix', 'max' => 10, 'help' => 'e.g. + or %'],
                    'description' => ['type' => 'text', 'label' => 'Description', 'max' => 200],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_verified' => ['type' => 'bool', 'label' => 'Verified figure'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published'],
                ],
            ],
            'faq-categories' => [
                'label' => 'FAQ Categories', 'singular' => 'FAQ category', 'table' => 'faq_categories', 'icon' => 'tag', 'group' => 'FAQ',
                'order' => 'sort_order', 'search' => ['name'], 'columns' => ['name' => 'Name', 'slug' => 'Slug'],
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 120],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'name', 'max' => 80],
                    'icon' => ['type' => 'icon', 'label' => 'Icon'],
                    'sort_order' => ['type' => 'number', 'label' => 'Display order'],
                ],
            ],
            'faqs' => [
                'label' => 'FAQs', 'singular' => 'FAQ', 'table' => 'faqs', 'icon' => 'info', 'group' => 'FAQ',
                'order' => 'category_id, sort_order', 'search' => ['question', 'answer'], 'sort_scope' => 'category_id',
                'list_join' => 'SELECT t.*, c.name AS category_name FROM faqs t LEFT JOIN faq_categories c ON c.id = t.category_id',
                'columns' => ['question' => 'Question', 'category_name' => 'Category', 'is_published' => 'Visible'],
                'fields' => [
                    'question' => ['type' => 'text', 'label' => 'Question', 'required' => true, 'max' => 300],
                    'answer' => ['type' => 'rich', 'label' => 'Answer', 'required' => true],
                    'category_id' => ['type' => 'select', 'label' => 'Category', 'required' => true, 'options' => 'sql:SELECT id, name FROM faq_categories ORDER BY sort_order'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_published' => ['type' => 'bool', 'label' => 'Published', 'default' => 1],
                ],
            ],
            'pages' => [
                'label' => 'Legal Pages', 'singular' => 'Page', 'table' => 'pages', 'icon' => 'file', 'group' => 'Content',
                'order' => 'title', 'search' => ['title'], 'preview' => 'legal/{slug}', 'columns' => ['title' => 'Title', 'slug' => 'Slug', 'is_published' => 'Published'],
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'required' => true, 'max' => 190],
                    'slug' => ['type' => 'slug', 'label' => 'URL slug', 'from' => 'title', 'max' => 120],
                    'content' => ['type' => 'rich', 'label' => 'Content'],
                    'meta_description' => ['type' => 'textarea', 'label' => 'SEO description', 'max' => 300, 'rows' => 2],
                    'is_published' => ['type' => 'bool', 'label' => 'Published', 'default' => 1],
                ],
            ],
            'users' => [
                'label' => 'Users & Roles', 'singular' => 'User', 'table' => 'users', 'icon' => 'users', 'group' => 'System', 'role' => 'super_admin',
                'order' => 'name', 'search' => ['name', 'email'],
                'list_join' => 'SELECT t.*, r.name AS role_name FROM users t JOIN roles r ON r.id = t.role_id',
                'columns' => ['name' => 'Name', 'email' => 'Email', 'role_name' => 'Role', 'is_active' => 'Active', 'last_login_at' => 'Last login'],
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 120],
                    'email' => ['type' => 'email', 'label' => 'Email', 'required' => true, 'max' => 190],
                    'role_id' => ['type' => 'select', 'label' => 'Role', 'required' => true, 'options' => 'sql:SELECT id, name FROM roles ORDER BY level'],
                    'designation' => ['type' => 'text', 'label' => 'Designation (shown on articles)', 'max' => 120],
                    'bio' => ['type' => 'textarea', 'label' => 'Short bio', 'rows' => 3],
                    'password' => ['type' => 'password', 'label' => 'Password', 'help' => 'Min. 10 characters with letters and numbers. Leave blank to keep the current password.'],
                    'is_active' => ['type' => 'bool', 'label' => 'Active', 'default' => 1],
                ],
            ],
        ];
    }

    public static function get(string $key): ?array
    {
        $all = self::all();
        return $all[$key] ?? null;
    }

    /** Resolve select options to [value => label]. */
    public static function options($spec): array
    {
        if (is_array($spec)) {
            return $spec;
        }
        if (is_string($spec) && strpos($spec, 'sql:') === 0) {
            $out = [];
            foreach (db()->fetchAll(substr($spec, 4)) as $row) {
                $vals = array_values($row);
                $out[$vals[0]] = $vals[1];
            }
            return $out;
        }
        return [];
    }
}
