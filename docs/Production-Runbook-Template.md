# Production Runbook Template

Project: AidData Training Hub
Environment: Production
Change Ticket: ____________________
Planned Window: ____________________
Release Owner: ____________________
Technical Approver: ____________________
Operations Approver: ____________________

## 1. Preconditions

- [ ] IT-Release-Gate-Checklist.md mandatory items are complete and approved.
- [ ] CI smoke workflow passed on release commit.
- [ ] Backup snapshot completed for DB and uploads.
- [ ] Rollback owner and incident bridge contacts confirmed.
- [ ] Production secrets validated (DB, salts, SMTP/API keys).

Evidence:
- CI run URL: ____________________
- Backup job ID: ____________________
- Secret validation record: ____________________

## 2. Deployment Scope

Release version/tag: ____________________
Included changes:
- ____________________
- ____________________
- ____________________

Out of scope:
- ____________________

## 3. Deployment Steps

1. Confirm current production health endpoint status.
2. Confirm monitoring dashboards and alert channels are active.
3. Deploy release artifact/container to production.
4. Confirm service starts successfully and restart policy is active.
5. Validate endpoint checks:
   - /healthcheck.php returns OK.
   - /wp-login.php loads.
   - /xmlrpc.php returns 403.
6. Validate key business workflow:
   - user login
   - core course page render
   - password reset initiation
7. Confirm no critical errors in application and web logs.

## 4. Post-Deploy Verification

- [ ] Health endpoint stable for 15 minutes.
- [ ] Error rate and response time within baseline.
- [ ] Authentication workflow healthy.
- [ ] Mail flow for password reset validated.
- [ ] No high-severity alerts triggered.

Verification Evidence:
- Monitoring dashboard snapshot: ____________________
- Log query links: ____________________

## 5. Communications

Pre-deploy notification sent at: ____________________
Post-deploy success/failure notice sent at: ____________________
Incident channel (if needed): ____________________

## 6. Decision

Deployment result: SUCCESS / FAILED / ROLLED BACK
Approved by: ____________________
Timestamp: ____________________

## 7. Notes

- ____________________
- ____________________
