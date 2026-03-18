# IT Release Gate Checklist

Project: AidData Training Hub
Date: 2026-03-18
Owner: Release Engineering (handoff to IT Operations)
Environment: Production
Decision: NO-GO (pending operational drill evidence and final approvals)

## 1. Security Gate (Mandatory)

- [ ] Confirm no real secrets are committed in repository history for DB/user/password, salts, SMTP/API keys.
- [x] Confirm production secrets are injected via environment variables only.
- [x] Confirm production fails fast when placeholder secrets are present.
- [x] Confirm dangerous utility scripts are not present in runtime image.
- [x] Confirm Nginx blocks direct access to maintenance/diagnostic script patterns.
- [x] Confirm XML-RPC endpoint policy is approved (blocked by default).
- [x] Confirm wp-config.php file access is denied at web layer.
- [x] Confirm security headers are present in HTTP responses:
  - X-Content-Type-Options
  - X-Frame-Options
  - Referrer-Policy
  - Permissions-Policy
- [ ] Confirm admin uses HTTPS and FORCE_SSL_ADMIN behavior is validated.

Evidence links:
- Dockerfile
- docker/nginx/default.conf
- app/public/wp-config.php
- docs/evidence/Operational-Evidence-2026-03-18.md

## 2. Platform Support Gate (Mandatory)

- [x] Confirm PHP base image is currently supported by upstream security lifecycle.
- [ ] Confirm MySQL image/version is approved by platform standards.
- [ ] Confirm container image vulnerability scan has no Critical findings.
- [ ] Confirm container image vulnerability scan has no High findings without approved exception.

Evidence links:
- Dockerfile
- docker-compose.yml
- Vulnerability scan report: docs/evidence/Operational-Evidence-2026-03-18.md (pending scanner output link)

## 3. Configuration Gate (Mandatory)

- [x] Confirm WP_HOME and WP_SITEURL are explicitly set for production and use https.
- [ ] Confirm WP_ENVIRONMENT_TYPE=production in deployment environment.
- [x] Confirm WP_DEBUG=false, WP_DEBUG_DISPLAY=false in production.
- [ ] Confirm cron strategy is defined (WP cron enabled/disabled with external scheduler).
- [ ] Confirm outbound network policy (WP_HTTP_BLOCK_EXTERNAL and allowed hosts) is approved.

Evidence links:
- app/public/wp-config.php
- Railway service variables / secret store screenshots (pending)
- docs/evidence/Operational-Evidence-2026-03-18.md

## 4. Data and Recovery Gate (Mandatory)

- [ ] Confirm full backup exists for DB and uploads prior to deployment.
- [ ] Confirm restore test completed successfully in non-production.
- [x] Confirm rollback plan documented with RTO/RPO expectations.
- [x] Confirm rollback owner and on-call approver are assigned.

Evidence links:
- Backup job logs: pending link in docs/evidence/Operational-Evidence-2026-03-18.md
- Restore test evidence: pending link in docs/evidence/Operational-Evidence-2026-03-18.md
- Rollback runbook: docs/Rollback-Checklist.md

## 5. Reliability Gate (Mandatory)

- [x] Confirm healthcheck validates both runtime and DB connectivity.
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
- Smoke test workflow: .github/workflows/ci-smoke.yml
- Smoke test report: docs/evidence/Operational-Evidence-2026-03-18.md (pending green default-branch run URL)

## 6. Operations Gate (Mandatory)

- [x] Confirm deployment runbook is current and approved.
- [ ] Confirm incident response contact list is current.
- [ ] Confirm change ticket references this checklist and evidence.
- [x] Confirm post-deploy verification checklist is assigned.

Evidence links:
- Deployment runbook: docs/Production-Runbook-Template.md
- Incident contacts: pending in docs/evidence/Operational-Evidence-2026-03-18.md
- Change ticket ID: pending

## 7. Plugin and Application Surface Gate

- [x] Confirm only required plugins are active in production.
- [x] Confirm migration/debug plugins are disabled or removed in production.
- [ ] Confirm theme and MU plugin versions are pinned/controlled.

Evidence links:
- app/public/wp-content/plugins
- app/public/wp-content/mu-plugins
- docs/evidence/Plugin-Allowlist-2026-03-18.md
- Dockerfile
- .github/workflows/ci-smoke.yml

## 8. Final Approval

Security Approver: Security Lead (name required) Date: __________
Platform Approver: Platform Lead (name required) Date: __________
Application Approver: Application Owner (name required) Date: __________
IT Operations Approver: IT Operations Manager (name required) Date: __________

Final Decision: NO-GO (operational evidence pending)
Notes: See docs/evidence/Operational-Evidence-2026-03-18.md for current status and completion requirements.
