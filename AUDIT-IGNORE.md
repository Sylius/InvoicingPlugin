# AUDIT-IGNORE

This document explains why specific advisories are added to `composer.json` → `config.audit.ignore`.

**PKSA-gs8r-6kz6-pp56** — `api-platform/core` CVE-2025-31485; affected versions < 3.4.17, 4.0.0–4.0.21, 4.1.0–4.1.4 are pulled by Sylius dependency constraints. GraphQL property security grant caching issue allows unauthorized access.
https://www.cve.org/CVERecord?id=CVE-2025-31485

**PKSA-gnn4-pxdg-q76m** — `api-platform/core` CVE-2025-31481; same affected versions as above. GraphQL security bypass via Relay `node` type allows unauthorized entity access.
https://www.cve.org/CVERecord?id=CVE-2025-31481

**PKSA-4g5g-4rkv-myqs** — PKSA-4g5g-4rkv-myqs