# Rollback Checklist

Project: AidData Training Hub
Environment: Production
Trigger Time: ____________________
Incident ID / Change Ticket: ____________________
Rollback Owner: ____________________
Approver: ____________________

## 1. Rollback Triggers

Execute rollback when any of the following occurs:
- Healthcheck endpoint fails consistently for 5+ minutes.
- Login/authentication flow is broken for users.
- Data integrity risk is detected.
- Critical/high alert threshold is breached and not mitigated quickly.

## 2. Pre-Rollback Controls

- [ ] Incident bridge opened.
- [ ] Stakeholders notified.
- [ ] Current logs and metrics snapshot captured.
- [ ] Confirm target rollback version is available.
- [ ] Confirm last known good backup snapshot ID.

## 3. Rollback Execution Steps

1. Stop current release rollout.
2. Re-deploy last known good release artifact.
3. Revert any release-specific environment variable changes.
4. If schema/data changes were applied:
   - execute approved DB rollback plan, or
   - restore from validated backup snapshot.
5. Restart services and verify health endpoint.
6. Run smoke checks:
   - /healthcheck.php returns OK
   - /wp-login.php loads
   - key course page loads
7. Monitor for 15-30 minutes to confirm stabilization.

## 4. Data Recovery Validation

- [ ] Database state validated.
- [ ] Uploads/media integrity validated.
- [ ] User authentication and password reset validated.
- [ ] No data loss indicators in logs.

## 5. Closure

- [ ] Incident status updated.
- [ ] Stakeholders notified of rollback completion.
- [ ] Follow-up RCA task created.
- [ ] Preventive action items assigned.

Rollback completed at: ____________________
Final status: STABLE / UNSTABLE
Recorded by: ____________________
