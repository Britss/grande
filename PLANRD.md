# Finalize Home Page Middle Section

## Summary
Finalize only the homepage area shown between the hero/top section and the footer. Preserve the existing header, hero, info band position, and footer structure/content. The work is to replace the unfinished placeholder section with polished, customer-facing Grande content.

Visual thesis: warm neighborhood bakery and coffee shop, using real product imagery, concise copy, and clear paths to menu and reservation.

## Key Changes
- Leave unchanged:
  - Fixed navigation/header.
  - Hero section at the top.
  - Existing footer markup, content, layout, links, icons, and styling.
- Replace the current placeholder “Current Step / Visual Direction” content with a finished cafe-focused section.
- Keep the three-item band above the middle section, but update only its copy if needed so it reads like customer-facing homepage content:
  - `Beside Puregold, Sindalan`
  - `Fresh bread and coffee 24/7`
  - `Dine in, take out, or reserve`
- Add a polished middle section with:
  - Product imagery from existing local assets.
  - Short copy about pan de sal, premium coffee, 24/7 service, and Sindalan location.
  - CTAs linking to `Menu` and `Reserve`.

## Implementation Changes
- Update [home.php](C:/xampp/htdocs/grande/app/Views/pages/home.php) only in the content after the hero and before the existing footer include.
- Update [app.css](C:/xampp/htdocs/grande/public/assets/css/app.css) only for homepage middle-section styles and any responsive rules needed for that section.
- Do not edit [footer.php](C:/xampp/htdocs/grande/app/Views/partials/footer.php).
- Do not alter existing `.footer`, `.social-links`, `.footer-icon`, or footer responsive CSS unless a new middle-section style accidentally conflicts with it, in which case fix the conflict outside the footer rules.
- Reuse existing images from `public/images/menu-items/`, such as `classic_pan_de_sal.png`, `grande_coffee.jpg`, and a pastry or bundle image.

## Content Direction
- Remove all design/rewrite/project wording such as `Architecture first`, `Visual Direction`, `old codebase`, and `sample`.
- Proposed middle-section copy:
  - Eyebrow: `Fresh Every Hour`
  - Heading: `Pan de sal, coffee, and comfort any time of day.`
  - Body: `Drop by in Sindalan for warm bread, premium coffee, and a table ready whenever the craving hits.`
- Proposed feature labels:
  - `Fresh Pan De Sal`
  - `Premium Coffee`
  - `Open 24/7`
- Proposed actions:
  - `View Menu`
  - `Reserve Table`

## Test Plan
- Open `http://localhost:8080/grande/`.
- Verify the header and hero remain visually unchanged.
- Verify the footer remains visually unchanged.
- Check the middle section at desktop width around `1920x1080`.
- Check mobile width around `390x844`:
  - No horizontal overflow.
  - Text remains readable.
  - Product images load correctly.
  - CTAs remain tappable.
- Confirm `View Menu`, `Reserve Table`, and existing footer links still work.

## Assumptions
- “Preserve the footer” means no intentional visual, structural, copy, or CSS changes to the existing footer.
- The only design work should happen in the homepage middle content visible above the footer.
- Existing local product images are acceptable for the finalized design.
