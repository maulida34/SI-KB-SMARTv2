---
name: Laravel HTTPS proxy assets
description: Replit previews need Laravel to trust proxy headers so Vite asset URLs use HTTPS.
---

Laravel can see the backend request as HTTP while Replit serves the public preview over HTTPS. When proxy headers are not trusted, `@vite` may render HTTP asset URLs; browsers block those stylesheets and scripts as mixed content even when the Vite build and manifest are valid.

**Why:** Checking only `npm run build` and the Blade `@vite` directive does not catch a wrong URL scheme in rendered HTML.

**How to apply:** Preserve Laravel trusted-proxy configuration in `bootstrap/app.php`. After changes, restart the Laravel workflow and verify that the HTTPS preview emits HTTPS asset URLs and that the CSS/JS requests return 200. Do not hardcode the preview domain.