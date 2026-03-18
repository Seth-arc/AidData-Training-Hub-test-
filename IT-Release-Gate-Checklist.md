# IT Release Gate Checklist

Project: AidData Training Hub
Date: 2026-03-18
Owner: ____________________
Environment: Production
Decision: GO / NO-GO

## 1. Security Gate (Mandatory)

- [ ] Confirm no real secrets are committed in repository history for DB/user/password, salts, SMTP/API keys.
- [ ] Confirm production secrets are injected via environment variables only.
- [ ] Confirm production fails fast when placeholder secrets are present.
- [ ] Confirm dangerous utility scripts are not present in runtime image.
- [ ] Confirm Nginx blocks direct access to maintenance/diagnostic script patterns.
- [ ] Confirm XML-RPC endpoint policy is approved (blocked by default).
- [ ] Confirm wp-config.php file access is denied at web layer.
- [ ] Confirm security headers are present in HTTP responses:
  - X-Content-Type-Options
  - X-Frame-Options
  - Referrer-Policy
  - Permissions-Policy
- [ ] Confirm admin uses HTTPS and FORCE_SSL_ADMIN behavior is validated.

Evidence links:
- Dockerfile
- docker/nginx/default.conf
- app/public/wp-config.php

## 2. Platform Support Gate (Mandatory)

- [ ] Confirm PHP base image is currently supported by upstream security lifecycle.
- [ ] Confirm MySQL image/version is approved by platform standards.
- [ ] Confirm container image vulnerability scan has no Critical findings.
- [ ] Confirm container image vulnerability scan has no High findings without approved exception.

Evidence links:
- Dockerfile
- docker-compose.yml
- Vulnerability scan report: ____________________

## 3. Configuration Gate (Mandatory)

- [ ] Confirm WP_HOME and WP_SITEURL are explicitly set for production and use https.
- [ ] Confirm WP_ENVIRONMENT_TYPE=production in deployment environment.
- [ ] Confirm WP_DEBUG=false, WP_DEBUG_DISPLAY=false in production.
- [ ] Confirm cron strategy is defined (WP cron enabled/disabled with external scheduler).
- [ ] Confirm outbound network policy (WP_HTTP_BLOCK_EXTERNAL and allowed hosts) is approved.

Evidence links:
- app/public/wp-config.php
- Railway service variables / secret store screenshots

## 4. Data and Recovery Gate (Mandatory)

- [ ] Confirm full backup exists for DB and uploads prior to deployment.
- [ ] Confirm restore test completed successfully in non-production.
- [ ] Confirm rollback plan documented with RTO/RPO expectations.
- [ ] Confirm rollback owner and on-call approver are assigned.

Evidence links:
- Backup job logs: ____________________
- Restore test evidence: ____________________
- Rollback runbook: ____________________

## 5. Reliability Gate (Mandatory)

- [ ] Confirm healthcheck validates both runtime and DB connectivity.
- [ ] Confirm restart policy is configured and validated.
- [ ] Confirm synthetic smoke test passes after deploy:
  - homepage
  - login page
  - key course page
  - password reset flow
- [ ] Confirm error logs are centralized and accessible to IT support.

Evidence links:
- app/public/healthcheck.php
- railway.toml
- Smoke test report: ____________________

## 6. Operations Gate (Mandatory)

- [ ] Confirm deployment runbook is current and approved.
- [ ] Confirm incident response contact list is current.
- [ ] Confirm change ticket references this checklist and evidence.
- [ ] Confirm post-deploy verification checklist is assigned.

Evidence links:
- Deployment runbook: ____________________
- Incident contacts: ____________________
- Change ticket ID: ____________________

## 7. Plugin and Application Surface Gate

- [ ] Confirm only required plugins are active in production.
- [ ] Confirm migration/debug plugins are disabled or removed in production.
- [ ] Confirm theme and MU plugin versions are pinned/controlled.

Evidence links:
- app/public/wp-content/plugins
- app/public/wp-content/mu-plugins

## 8. Final Approval

Security Approver: ____________________ Date: __________
Platform Approver: ____________________ Date: __________
Application Approver: ____________________ Date: __________
IT Operations Approver: ____________________ Date: __________

Final Decision: GO / NO-GO
Notes: _______________________________________________
