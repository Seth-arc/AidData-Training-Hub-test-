# IT Handoff Email + Acceptance Criteria

Date: 2026-03-18
Project: AidData Training Hub
Target production domain: training.aiddata.org
Release state: Conditionally ready, pending final IT operational closures

## 1. Sendable Handoff Email

Subject: AidData Training Hub handoff for production deployment to training.aiddata.org

To: IT Operations, Platform Engineering, Security
CC: Application Owner, Release Engineering, DBA Lead

Hello IT team,

The AidData Training Hub release package is ready for IT deployment execution to training.aiddata.org, with technical hardening and CI smoke validation completed.

Release artifact and readiness documents are available at:
- IT gate checklist: IT-Release-Gate-Checklist.md
- Remaining actions list: docs/IT-Remaining-Actions-Punch-List.md
- Operational evidence log: docs/evidence/Operational-Evidence-2026-03-18.md
- Current release assessment: Go No Go.md
- CI smoke workflow and latest green run:
  - Workflow: .github/workflows/ci-smoke.yml
  - Run: https://github.com/Seth-arc/AidData-Training-Hub-test-/actions/runs/23252871892

Deployment instruction:
- Deploy using the hardened containerized path only.
- Do not deploy direct source/public path bypassing container hardening controls.

Please complete the remaining acceptance criteria below and update the linked evidence files with approver names, dates, and artifact URLs.

Thank you.

## 2. IT Acceptance Criteria (Required Before GO)

1. Security validation completed
- Repository secret hygiene scan completed and signed off.
- FORCE_SSL_ADMIN behavior validated in production path.

2. Platform validation completed
- MySQL version approved by Platform.
- Vulnerability scan completed with no unapproved Critical/High findings.

3. Production configuration confirmed
- WP_ENVIRONMENT_TYPE set to production in hosting platform.
- WP_HOME and WP_SITEURL set for https://training.aiddata.org.
- Outbound policy (WP_HTTP_BLOCK_EXTERNAL and allowlist) confirmed.
- Cron strategy confirmed.

4. Data and recovery readiness proven
- Full backup captured before cutover.
- Backup/restore drill completed with owner sign-off.

5. Reliability and operations readiness proven
- Restart policy validated in production runtime.
- Centralized log access validated for IT support.
- Incident contact list finalized.
- Change ticket ID linked to gate and evidence documents.

6. Application surface control completed
- Theme and MU plugin version control approach documented and approved.

7. Final approvals recorded
- Security Approver signed.
- Platform Approver signed.
- Application Approver signed.
- IT Operations Approver signed.

## 3. GO Decision Rule

Switch final decision from NO-GO to GO only when every required item above is complete and evidence is attached in:
- IT-Release-Gate-Checklist.md
- docs/evidence/Operational-Evidence-2026-03-18.md
- docs/evidence/Plugin-Allowlist-2026-03-18.md
