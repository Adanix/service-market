# ServiceLocal - Local Services & Freelance Directory Marketplace

ServiceLocal is a location-based services marketplace platform connecting customers with verified local service professionals and contractors. The architecture supports a three-sided operational model: customers seeking home or technical services, verified service providers offering expertise, and platform administrators maintaining operational quality and payout disbursements.

---

## Key Modules & Capabilities

### 1. Service Catalog & Regional Taxonomy
* Categorized directory covering residential maintenance, plumbing, electrical installations, cleaning, and digital consulting.
* Regional county and district geographical classification enabling localized search results.
* Detailed service listings including base rates, service scopes, and prerequisites.

### 2. Verified Service Provider Profiles
* Provider business profiles featuring trade badges, insurance verification flags, operational regions, and portfolio showcases.
* Direct inquiry submission allowing customers to describe project scopes and preferred dates.
* Provider performance metrics, completed booking counts, and customer feedback.

### 3. Booking & Dispatch Workflow
* Structured booking request lifecycle (Pending, Confirmed, In Progress, Completed, Cancelled).
* Date and timeslot selection for scheduled home visits.
* Automated customer status updates and service confirmation logging.

### 4. Settlement & Payout Engine
* Secure transaction recording for customer service deposits and balance settlements.
* Platform fee calculation and deduction rules.
* Provider payout record generation for bank disbursement tracking and financial audits.

### 5. In-Platform Customer Messaging
* Contact and inquiry messaging allowing direct exchange between customer and assigned provider.
* Administrative visibility over open inquiries for dispute resolution and quality assurance.

### 6. Audit & System Governance
* Activity audit logs recording provider status changes (approval, suspension, re-verification).
* System-wide configuration parameters for marketplace commissions and booking windows.

---

## Technical Stack

* Framework: Laravel 13
* Runtime: PHP 8.5
* Database: MySQL 8.0
* Frontend: Tailwind CSS, Blade Templating Engine
* Containerization: Docker, Nginx

---

## Project Structure

```
app/
├── Http/Controllers/     Directory, Booking, Provider, Payment, and Admin controllers
├── Models/               Eloquent models (Service, Category, ProviderProfile, Booking, Payment, Payout)
resources/
├── views/                Blade templates (Service catalog, Booking forms, Provider profiles)
routes/
├── web.php               Marketplace route groups and inquiry endpoints
database/
├── migrations/           Database schema definitions
├── seeders/              County registries, service categories, and verified provider seeders
```

---

## Installation & Deployment

### Prerequisites
* Docker and Docker Compose
* Git

### Setup Instructions
1. Clone the repository into your local development environment:
   ```bash
   git clone <repository_url>
   cd <repository_folder>
   ```

2. Copy the sample environment configuration:
   ```bash
   cp .env.example .env
   ```

3. Configure database settings in `.env`:
   ```env
   APP_NAME=ServiceLocal
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel13_service_market
   DB_USERNAME=usr_service_market
   DB_PASSWORD=your_secure_password
   ```

4. Launch the application container:
   ```bash
   docker compose up -d
   ```

5. Install application dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

6. Generate the encryption key:
   ```bash
   php artisan key:generate
   ```

7. Run database migrations and seed sample services:
   ```bash
   php artisan migrate --seed
   ```

8. Cache configuration:
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## License

This software is distributed under the proprietary license of the author. All rights reserved.
