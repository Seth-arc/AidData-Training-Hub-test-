# IT Remaining Actions Punch List

Date: 2026-03-18
Scope: Remaining mandatory actions before GO
Status: Open

## Action Items

1. Validate repository secret hygiene and record evidence.
Owner: Security Lead
Deliverable: Secret scan report and sign-off.
Evidence target: docs/evidence/Operational-Evidence-2026-03-18.md
Gate mapping: Security Gate item 1

2. Validate FORCE_SSL_ADMIN behavior in production path.
Owner: Application Owner + Platform Lead
Deliverable: Test record proving admin auth/cookies work over HTTPS.
Evidence target: docs/evidence/Operational-Evidence-2026-03-18.md
Gate mapping: Security Gate item 10

3. Approve MySQL version and complete vulnerability scan review.
Owner: Platform Lead
Deliverable: MySQL approval note plus vulnerability scan output showing no unapproved Critical/High findings.
Evidence target: docs/evidence/Operational-Evidence-2026-03-18.md
Gate mapping: Platform Support Gate items 2-4

4. Confirm production environment variable posture in hosting platform.
Owner: Platform Lead
Deliverable: Screenshot/export showing WP_ENVIRONMENT_TYPE=production and approved outbound policy.
Evidence target: docs/evidence/Operational-Evidence-2026-03-18.md
Gate mapping: Configuration Gate items 2, 4, 5

5. Execute backup and restore drill with owner sign-off.
Owner: DBA Lead + Application Owner
Deliverable: Backup job ID, restore validation report, sign-off names/dates.
Evidence target: docs/evidence/Operational-Evidence-2026-03-18.md
Gate mapping: Data and Recovery Gate items 1-2

6. Validate restart policy and centralized log access.
Owner: IT Operations Manager
Deliverable: Runtime restart validation note and log platform access confirmation.
Evidence target: docs/evidence/Operational-Evidence-2026-03-18.md
Gate mapping: Reliability Gate items 2, 4

7. Finalize incident contact list and change ticket linkage.
Owner: IT Operations Manager
Deliverable: Current incident roster and change ticket ID referencing checklist and artifacts.
Evidence target: docs/evidence/Operational-Evidence-2026-03-18.md
Gate mapping: Operations Gate items 2-3

8. Confirm theme and MU plugin version control policy.
Owner: Application Owner
Deliverable: Version pinning/management note for theme and MU plugins.
Evidence target: docs/evidence/Plugin-Allowlist-2026-03-18.md
Gate mapping: Plugin and Application Surface Gate item 3

9. Complete final approver signatures and switch decision to GO when all items are closed.
Owner: Security Lead, Platform Lead, Application Owner, IT Operations Manager
Deliverable: Signed approvals with dates.
Evidence target: IT-Release-Gate-Checklist.md
Gate mapping: Final Approval section

## Completion Rule

All actions above must be marked complete with evidence links before changing final decision from NO-GO to GO.
