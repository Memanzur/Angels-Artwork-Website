# Angel's Artwork - Site Handoff Guide

## Quick Overview
- **Domain:** angelsartwork.com
- **Hosting:** GitHub Pages (free)
- **Repo:** github.com/Memanzur/Angels-Artwork-Website
- **Shop:** Square (kerris-art-kreations.square.site)
- **Contact form:** Formspree (sends to kerrikreations@gmail.com)

## How the Site Works
Everything is static HTML/CSS/JS. No server, no database, no monthly hosting fees. GitHub Pages serves it for free.

- `index.html` - the main page layout
- `styles.css` - all the styling
- `main.js` - gallery filters, lightbox, animations
- `data/site.json` - site text, shop URL, about section, contact info
- `data/artworks.json` - all artwork entries (title, image, shop link, category)
- `images/` - all artwork images organized by category
- `CNAME` - tells GitHub Pages to use angelsartwork.com

## Editing Content (No Code Required)
Go to **angelsartwork.com/admin** (or the GitHub Pages URL + /admin).
This opens a visual editor (Decap CMS) where you can:
- Change hero text, about section, contact info
- Add/remove/reorder artworks
- Upload new images
- Update shop links

You'll need to log in with the GitHub account that owns the repo.

## Editing Content (Manual)
To change site text, edit `data/site.json`.
To add/remove artwork, edit `data/artworks.json`.
Each artwork entry looks like:
```json
{
  "title": "Artwork Name",
  "description": "Short description",
  "image": "/images/category/filename.jpg",
  "category": "angels",
  "purchase_url": "https://kerris-art-kreations.square.site/..."
}
```
Categories: `angels`, `spirit`, `bookmarks`

## DNS Setup (on Hostinger)
The domain DNS must point to GitHub Pages. In Hostinger DNS settings:

| Type  | Name | Value               |
|-------|------|---------------------|
| A     | @    | 185.199.108.153     |
| A     | @    | 185.199.109.153     |
| A     | @    | 185.199.110.153     |
| A     | @    | 185.199.111.153     |
| CNAME | www  | memanzur.github.io  |

Delete any existing A records for @ before adding these.
After DNS propagates (up to 48 hours, usually faster), go to GitHub repo Settings > Pages and check "Enforce HTTPS."

## Transferring Ownership
To hand this site to someone else:
1. Add them as a collaborator on the GitHub repo (Settings > Collaborators)
2. Transfer the repo to their GitHub account (Settings > General > Transfer)
3. Transfer the domain on Hostinger (or update DNS to their new hosting)
4. Update the CNAME file if the GitHub username changes
5. Update `admin/config.yml` to reflect the new repo name

## Costs
- Hosting: $0 (GitHub Pages)
- Domain: ~$12/year (renew on Hostinger)
- SSL/HTTPS: $0 (GitHub provides it)
- Square shop: managed separately by the artist
