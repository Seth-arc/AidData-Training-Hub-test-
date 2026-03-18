# Operational Evidence - 2026-03-18

Project: AidData Training Hub
Prepared by: Release Engineering
Status: In progress

## 1. Deployment Runbook Execution Evidence

Artifact:
- docs/Production-Runbook-Template.md

Execution status:
- Pre-production runbook walk-through prepared.
- Formal production-window execution evidence pending IT change window.

Approver assignment:
- Runbook owner: IT Operations Lead (name required)
- Technical approver: Platform Engineer Lead (name required)

## 2. Backup/Restore Drill Evidence

Artifacts:
- docs/Production-Runbook-Template.md
- docs/Rollback-Checklist.md

Execution status:
- Backup/restore drill evidence pending scheduled non-production drill.
- Required outputs for sign-off: backup job ID, restore duration, restore validation checks.

Owner sign-off assignment:
- Data owner approver: DBA Lead (name required)
- Application owner approver: Product Engineering Lead (name required)

## 3. Rollback Drill and On-Call Assignment

Artifacts:
- docs/Rollback-Checklist.md

Execution status:
- Rollback checklist documented and ready.
- Live rollback drill evidence pending rehearsal window.

On-call assignment:
- Primary on-call: SRE Primary (name required)
- Secondary on-call: SRE Secondary (name required)
- Incident commander: IT Operations Manager (name required)

## 4. CI Smoke Evidence

Artifact:
- .github/workflows/ci-smoke.yml

Current status:
- Workflow exists and checks build plus hardened endpoints.
- GitHub Actions CI Smoke run is green on release commit.

Green run evidence:
- Run URL: https://github.com/Seth-arc/AidData-Training-Hub-test-/actions/runs/23252871892
- Job URL: https://github.com/Seth-arc/AidData-Training-Hub-test-/actions/runs/23252871892/job/67599478700
- Result: passed

Local attempt details:
- Date: 2026-03-18
- Command class: docker compose build/up + endpoint checks
- Result: failed
- Failure detail: could not connect to Docker Desktop Linux engine pipe.

Required completion for gate:
- Green CI smoke run on default branch release commit.
- Link to workflow run URL.
- Status: completed.
