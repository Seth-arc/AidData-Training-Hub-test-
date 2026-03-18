# Production Plugin Allowlist - 2026-03-18

Project: AidData Training Hub
Environment: Production container image

## Allowed plugins in production image

- classic-editor
- google-site-kit
- learnpress
- learnpress-auto-certificates

## Removed from production image

- all-in-one-wp-migration
- duplicator
- string-locator

## Enforcement

- Dockerfile removes nonessential migration/debug plugins during image build.
- CI smoke workflow asserts removed plugin directories are absent in runtime container.

## Notes

Source repository may still retain these plugins for local migration or debugging workflows.
Production deployment path must use the hardened container image.
