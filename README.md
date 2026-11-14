# Yojo Solutions — Audit, GRC & Cybersecurity Platform

A full-stack PHP 7.4+ / MySQL (MariaDB 10.6) website with a public frontend, JSON API,
enquiry and consultation workflows, an Audit Readiness self-assessment, and a role-based admin dashboard.
No frameworks: HTML5, CSS3, vanilla ES6, PHP PDO.

## Folder structure

```
app/
  Core/         bootstrap, Router, Database (PDO), View, Auth, Csrf, Validator, Logger, helpers
  Controllers/  public pages, Api/ (JSON), Admin/ (dashboard)
  Models/       Service, Post, Industry, Enquiry, Assessment
  Services/     Mailer (SMTP), EnquiryService, AssessmentScorer, SpamGuard, HtmlSanitizer, Uploader, Icons, AdminResources
  Middleware/   AuthMiddleware, RoleMiddleware, CsrfMiddleware
config/         app.php, database.php, mail.php (+ optional *.local.php overrides)
database/       schema.sql, seed.sql, install.php, build_seed.php, data/ (seed content source)
public/         index.php (front controller), .htaccess, assets/  ← THE ONLY WEB-ROOT FOLDER
resources/views layouts/, partials/ (header, footer, forms…), pages/, admin/, emails/
routes/         web.php, api.php
storage/        logs/, uploads/ (private, served via /media/), cache/
```

## Deploying to your hosting

1. **Upload** the whole project folder to the server (e.g. `/home/USER/yojo-audit`).
2. **Point the domain's document root to `public/`.**
   If your host does not allow that, upload the project into `public_html/` — the root `.htaccess`
   rewrites everything into `public/` and blocks `app/`, `config/`, `storage/`, `database/` etc.
3. **Create the database** (cPanel → MySQL Databases) and a user with all privileges on it.
4. **Credentials:** copy `config/database.local.example.php` → `config/database.local.php` and
   `config/mail.local.example.php` → `config/mail.local.php`, then fill in real values.
   (`*.local.php` files are blocked from web access and should not be committed.)
5. **Import the data**, either:
   - phpMyAdmin: import `database/schema.sql`, then `database/seed.sql`; **or**
   - SSH: `php database/install.php --no-create --admin-email=you@yojosolutions.com --admin-password='StrongPass123'`
   - **Upgrading an existing database** (keeps all enquiries and edits): `php database/migrate.php`.
     It is idempotent and adds the homepage slides, frameworks, FAQ categories, the five service divisions,
     industry storytelling fields and the pop-up / footer / Who We Are settings. If you use phpMyAdmin only,
     run it once from any machine that can reach the database with the same `config/database.local.php`.
6. **Permissions:** `storage/logs`, `storage/uploads` and `storage/cache` must be writable by PHP (755/775).
7. **HTTPS:** enable SSL (AutoSSL / Let's Encrypt), then uncomment the HTTPS redirect in `public/.htaccess`
   and set `'url' => 'https://your-domain'` in `config/app.local.php`.
8. Visit `/admin`, sign in and **change the default password immediately** (My profile).

Default admin (seed only): `admin@yojosolutions.com` / `ChangeMe@2026`

## Using the admin dashboard

Open `/admin` (e.g. `https://your-domain/admin`, locally http://127.0.0.1:8090/admin) and sign in.

| What you want to change | Where in the admin |
|---|---|
| Homepage slides — headings, text, buttons/links, background image, colour theme, seconds per slide, order, visibility | **Homepage → Homepage Slides** (↑/↓ arrows reorder) |
| Framework cards and their pop-up/detail content | **Homepage → Frameworks** |
| Service divisions (the 5 main categories in the menu and Services page) | **Content → Service Divisions** |
| Individual services, their division, images, SEO and detail-page sections | **Content → Services** |
| Industries — image, risk indicators, overview, risks, governance, compliance, solutions, approach, recommended services | **Content → Industries** |
| FAQs (rich-text answers with links) and FAQ categories | **FAQ → FAQs / FAQ Categories** |
| Articles for Latest Insights, categories and tags | **Latest Insights → Articles / Article Categories** |
| Enquiry pop-up (on/off, minutes, max per visit, texts, button links) | **Configuration → Site settings → Enquiry pop-up** |
| Footer description, CTA, Google Maps link, phone, email, address, social links | **Site settings → Footer / Contact details / Social media** |
| Who We Are text, offerings, mission, vision, core values | **Site settings → Who We Are page** |

Roles: Viewer (read only) · Content Editor (edit content, handle enquiries) · Administrator (+ settings, export) · Super Administrator (+ users).

## Local development (XAMPP)

```bash
php database/install.php
php -S 127.0.0.1:8090 -t public server.php
```
Then open http://127.0.0.1:8090 and http://127.0.0.1:8090/admin.
With `MAIL_ENABLED` off, emails are written to `storage/logs/mail-YYYY-MM.log` instead of being sent.

## Key features

| Area | What it does |
|---|---|
| Homepage | 15 sections — hero dashboard visual, trust marquee, about, 4 core practices, why-us, 6-step timeline, industries, animated stats, case studies, testimonial slider, insights, searchable FAQ, CTA, rich footer |
| Services | 4 categories, 38 dedicated pages, mega-menu in 6 groups. Compliance pages show intro, meaning, need, requirements, methodology, scope, deliverables, benefits, FAQs; VAPT pages show scope, testing methodology, security areas, reporting, remediation |
| Industries | Listing + 8 detail pages (risks, compliance, challenges, recommended services, outcomes) |
| Latest Insights | `/insights` (old `/resources` links redirect) — search, category, tag, date filter, sort, pagination, article pages, social sharing, sitemap.xml |
| Enquiries | Contact, Book a Consultation (date/time/mode), Request a Proposal. Multi-service picker, client + server validation, unique reference (e.g. `YJ-CON-261110-Q5SV`), SMTP notification + acknowledgement, honeypot, timing check, rate limit, 24-hour duplicate detection, CSRF |
| Readiness assessment | 5-step wizard, transparent scoring (Yes 2 / Partial 1 / No 0, domain averages, published bands), clearly labelled preliminary, saved to DB, emailed, reviewed in admin |
| Admin | Dashboard KPIs and charts, enquiry pipeline (status, assignment, notes, CSV export), assessment review, CRUD for services, service FAQs, categories, industries, articles (rich-text editor, image upload, tags, scheduling), article categories, case studies, testimonials, trust logos, statistics, FAQs, legal pages, users; site settings; activity log |
| Roles | Viewer (read), Content Editor (edit content, handle enquiries), Administrator (settings, export, delete enquiries), Super Administrator (users) |

## Honesty safeguards (content you must verify)

- Certifications, associations, partners and client logos are seeded **unpublished** as `[Placeholder]` items.
- Testimonials are seeded as `[Sample]` and display a "Sample" badge until marked *Verified*.
- Case studies are seeded as *Illustrative scenarios* and labelled as such until marked *Client-approved*.
- Homepage statistics show factual figures about the offering (service lines, frameworks, industries, stages).
  Achievement figures (organisations supported, assessments completed…) are seeded unpublished at 0 —
  enter verified numbers in **Admin → Statistics** before publishing.
- Legal pages are placeholders — have them reviewed by counsel.
- Add your LinkedIn URL in **Admin → Site settings** (none was listed on yojosolutions.com).

## Security notes

PDO prepared statements everywhere · CSRF on every state-changing request · bcrypt password hashing with rehash ·
login throttling (5 failures / 15 min) · session regeneration, HttpOnly + SameSite cookies, idle timeout ·
role-based middleware · allow-list HTML sanitizer for rich text · MIME-checked uploads stored outside the web root ·
security headers · CSV formula-injection protection · error details hidden in production and logged to `storage/logs`.
