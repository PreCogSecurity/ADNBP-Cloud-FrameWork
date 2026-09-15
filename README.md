# ADNBP Cloud FrameWork

CloudFrameWork to create Portals, eCommerce, Social Networks, CRMs, ERPs and more.

## Architecture

```
ADNBP/                   Core framework library
  class/                 Framework classes (ADNBP.php, DataStore, email, etc.)
  api/                   Internal API endpoints
  logic/                 Business logic
  templates/             Framework templates
webapp/                  Application-level overrides
  config/                App-specific configuration
  logic/                 App-specific business logic
  templates/             App-specific templates
cloudframework_appengine_php/  Lightweight core for App Engine
  src/Core.php           Core, Session, System, Config, Security, User classes
  src/class/             DataStore, Email, CloudSQL, RESTful classes
  tests/                 Existing integration test scripts
tests/                   PHPUnit test suite
```

## Requirements

- PHP 7.2 or later (tested with PHP 8.2)
- Composer
- (Optional) Docker & Docker Compose

## Install

```bash
# Clone the repository
git clone https://github.com/PreCogSecurity/ADNBP-Cloud-FrameWork.git
cd ADNBP-Cloud-FrameWork

# Install PHP dependencies
composer install
```

## Environment Variables

Copy `.env.example` to `.env` and fill in your credentials:

```bash
cp .env.example .env
# Edit .env with your actual values
```

Key variables:

| Variable | Description |
|---|---|
| `GOOGLE_PUBLIC_API_CREDENTIAL` | Google API key for Maps/Geocoding |
| `FACEBOOK_OAUTH_APP_ID` | Facebook OAuth application ID |
| `TWITTER_OAUTH_KEY` | Twitter consumer API key |
| `LINKEDIN_OAUTH_KEY` | LinkedIn API key |
| `PINTEREST_OAUTH_CLIENT_ID` | Pinterest client ID |
| `SENDGRID_USER` | SendGrid username for email |
| `DB_SERVER` | MySQL/CloudSQL server address |

## Run

### With Docker Compose (recommended)

```bash
docker compose up
```

The application will be available at `http://localhost:8080`.

### With PHP built-in server (for development)

```bash
php -S localhost:8080
```

## Test

```bash
# Run the PHPUnit test suite
vendor/bin/phpunit

# Or use the composer script
composer test
```

## Lint

```bash
# Run PHP CodeSniffer
vendor/bin/phpcs --standard=phpcs.xml

# Or use the composer script
composer lint
```

## CI

This repository uses GitHub Actions for continuous integration. Every push and pull request runs:

1. **PHP Syntax Check** (`php -l`) across all source files
2. **PHPUnit** test suite with coverage reporting
3. **PHPCS** linting against the PSR-12 ruleset (lenient for legacy code)
4. **Gitleaks** secret scanning to prevent credential leaks

## License

MIT
