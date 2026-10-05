---
name: JDIH Kota Kendari
description: Legal documentation portal of the Kendari City Government, on the web and on Android. Fast, accurate, inclusive access to regional law.
colors:
  primary: "#ff891e"
  primary-hover: "#ea8221"
  primary-ink: "#a65307"
  accent: "#015ba5"
  accent-hover: "#014f8e"
  ink: "#0f172a"
  ink-soft: "#1e293b"
  ink-muted: "#475569"
  surface: "#ffffff"
  surface-muted: "#f8fafc"
  surface-subtle: "#f1f5f9"
  line: "#e2e8f0"
  line-strong: "#7c8aa0"
  status-active: "#166534"
  status-active-bg: "#dcfce7"
  status-changed: "#854d0e"
  status-changed-bg: "#fef9c3"
  status-revoked: "#991b1b"
  status-revoked-bg: "#fee2e2"
  overlay: "#000000a6"
typography:
  display:
    fontFamily: "Roboto, sans-serif"
    fontSize: "36px"
    fontWeight: 700
    lineHeight: 1.1
    fontFeature: "tnum"
  judul-detail:
    fontFamily: "Roboto, sans-serif"
    fontSize: "22px"
    fontWeight: 700
    lineHeight: 1.3
  judul:
    fontFamily: "Roboto, sans-serif"
    fontSize: "20px"
    fontWeight: 700
    lineHeight: 1.3
  angka:
    fontFamily: "Roboto, sans-serif"
    fontSize: "20px"
    fontWeight: 700
    lineHeight: 1.15
    fontFeature: "tnum"
  subjudul:
    fontFamily: "Roboto, sans-serif"
    fontSize: "17px"
    fontWeight: 700
    lineHeight: 1.35
  judul-item:
    fontFamily: "Roboto, sans-serif"
    fontSize: "15px"
    fontWeight: 700
    lineHeight: 1.35
  bacaan:
    fontFamily: "Roboto, sans-serif"
    fontSize: "16.5px"
    fontWeight: 400
    lineHeight: 1.65
  isi:
    fontFamily: "Roboto, sans-serif"
    fontSize: "15px"
    fontWeight: 400
    lineHeight: 1.5
  isi-kecil:
    fontFamily: "Roboto, sans-serif"
    fontSize: "13px"
    fontWeight: 400
    lineHeight: 1.45
  label-besar:
    fontFamily: "Roboto, sans-serif"
    fontSize: "15px"
    fontWeight: 600
    lineHeight: 1.25
  label:
    fontFamily: "Roboto, sans-serif"
    fontSize: "13px"
    fontWeight: 600
    lineHeight: 1.25
  label-kecil:
    fontFamily: "Roboto, sans-serif"
    fontSize: "12px"
    fontWeight: 600
    lineHeight: 1.25
rounded:
  sm: "4px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "24px"
  xxl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.ink}"
    typography: "{typography.label-besar}"
    rounded: "{rounded.sm}"
    padding: "12px 20px"
    height: "48px"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
    textColor: "{colors.ink}"
  button-outlined:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    typography: "{typography.label-besar}"
    rounded: "{rounded.sm}"
    padding: "12px 20px"
    height: "48px"
  button-text:
    textColor: "{colors.accent}"
    typography: "{typography.label-besar}"
    height: "48px"
  input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    typography: "{typography.isi}"
    rounded: "{rounded.sm}"
    padding: "12px 14px"
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "12px"
  chip-jenis:
    backgroundColor: "#015ba514"
    textColor: "{colors.accent}"
    typography: "{typography.label-kecil}"
    rounded: "{rounded.sm}"
    padding: "3px 8px"
  badge-status-active:
    backgroundColor: "{colors.status-active-bg}"
    textColor: "{colors.status-active}"
    typography: "{typography.label-kecil}"
    rounded: "{rounded.sm}"
    padding: "3px 8px"
  nav-item-active:
    backgroundColor: "#ff891e29"
    textColor: "{colors.primary-ink}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    size: "52px 30px"
  nav-ai-action:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    size: "48px"
  bubble-user:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.surface}"
    typography: "{typography.isi}"
    rounded: "{rounded.sm}"
    padding: "10px 14px"
---

# Design System: JDIH Kota Kendari

One system, two surfaces: the public website (Laravel + Tailwind 4, `resources/css/app.css`) and the Android app (Flutter, `mobile-flutter/app/lib/theme.dart`). They share the palette, the UI font, and the corner. Where a surface differs, the section says so under **Web** or **Android app**.

## Overview

**Creative North Star: "The Civic Reading Room"**

JDIH Kota Kendari behaves like a well-run public reading room for legal records: calm, bright, and orderly. Every screen exists for retrieval. A citizen or a legal practitioner arrives looking for a specific regulation, ruling, or document, and the interface walks them to it with as little friction as possible. Hierarchy does the guiding and the tool disappears into the task. The app is the same room carried in a pocket. It reads more than the website does, so long-form text gets its own reading role (Bacaan). Its chrome is quieter still: flat bordered surfaces, one corner radius, and no decoration that does not carry meaning.

The palette is disciplined. Kendari Amber is the room's signage. It points to the main action, marks where you are, and stays rare enough to keep that meaning. Civic Blue is the institutional voice for links, information, form controls, and data. Everything else is quiet: white content on a cool slate canvas, readable ink, and hairline borders. Warmth comes from the amber signage and from generous, legible type, never from a tinted page.

The system rejects the old-government-portal feel: dense raw tables without hierarchy, cramped small type, and documents buried behind layered menus. It is not a cold archive that intimidates a first-time visitor, and it is not a marketing site. The register is a functional public tool, and familiarity is a feature.

**Key Characteristics:**
- Retrieval-first: every screen shortens the path to a document.
- One warm signal (amber) used sparingly, one institutional voice (blue) for everything informational.
- Flat at rest, separated by hairlines and a white-on-slate tonal step.
- One family per surface: Roboto (the Android system font) for everything in the app, reading text included; Source Sans 3 on the web.
- One corner: 4px.
- Accessible by default: WCAG 2.1 AA contrast, 48dp targets, text scaling to 200%, reduced motion, four languages.

## Colors

A civic palette of two branded accents on white surfaces and cool slate neutrals, with legal status colors kept deliberately outside the brand.

### Primary
- **Kendari Amber** (`primary`): the signage. It fills primary buttons, the active navigation indicator (at 16% tint with a 55% amber border), the Ask-AI action, progress indicators, the section-header bar, and selected chips. It is a fill, not a text color. On white it reaches only 2.4:1. The web's hover state deepens it to **Amber Deep** (`primary-hover`).
- **Amber Ink** (`primary-ink`): the same hue darkened for text and small icons on light surfaces. It reaches 5.4:1 on white and 4.7:1 on the amber tint. The app uses it for the active nav label and icon, the selected tab label, and the AI sparkle mark.

### Secondary
- **Civic Blue** (`accent`): the institutional voice, safe as text (6.9:1) and as a fill under white text. It carries links and text buttons, the document-type chip and category icons (8 to 12% tint), selected radios and checkboxes, focus rings and cursor, chart bars, the user's question bubble in AI chat, and the selected document in a master-detail list (2px border). **Civic Blue Deep** (`accent-hover`) is the web's hover state and the app's document-detail header band. In the app it is named `C.accentDeep`.

### Neutral
- **Ink** (`ink`, slate-900): primary text on light surfaces, and the dark surfaces too: the web's `darkbg` footer and the app's snackbar. (The app's AI promo card now sits on the `PolaBiru` blue pattern, like the app bar.)
- **Ink Soft** (`ink-soft`, slate-800): secondary dark tone. It is the band behind an app article that has no image.
- **Ink Muted** (`ink-muted`, slate-600): metadata, captions, placeholders, and inactive navigation, at 7.6:1. Nothing body-sized goes lighter.
- **Reading Room White** (`surface`): cards, panels, inputs, sheets, and the app bar.
- **Cool Page** (`surface-muted`, slate-50): the canvas behind white content. The app's splash and the ring around the nav's AI action use it.
- **Second Layer** (`surface-subtle`, slate-100): the layer under toolbars, the chip rest state, and the segmented-control track.
- **Hairline** (`line`, slate-200): card borders, dividers, the web's `ring-slate-200`.
- **Control Edge** (`line-strong`): borders of small controls (checkboxes, unselected radios), at 3.5:1 on white (WCAG 1.4.11).
- **Field Edge** (`line-field`, slate-300 `#cbd5e1`): borders of inputs and outlined buttons in the app, as on the web forms. The owner found Control Edge too dark there; fields stay recognizable through their icon, placeholder, or label, and focus is a 2dp Civic Blue border.

### Status (outside the brand)
Legal status uses fixed semantic pairs: **Berlaku** is green (`status-active` on `status-active-bg`), **Diubah** is yellow (`status-changed` on `status-changed-bg`), and **Dicabut** is red (`status-revoked` on `status-revoked-bg`). They never borrow amber or blue, so "amended" can never be mistaken for "act here". `status-revoked` doubles as the error color. Chart series on the web stay multi-color because they encode data, not chrome.

### Named Rules
**The Amber Sparingly Rule.** Amber marks the primary action, the current position, and AI. Nothing else. On any screen it covers well under 10% of the surface. Once amber fills a whole section, it stops meaning "act here".

**The Ink-On-Amber Rule.** Text and icons on an amber fill are Ink (7.5:1), never white (2.4:1). The app holds this everywhere. The web still has white-on-amber buttons from before; they fail AA and should move to ink when touched.

**The No-Washed-Text Rule.** Body text on white is Ink or Ink Muted, never lighter. Gray text never sits on amber or blue. Use Ink on amber, white on blue.

**The Light Room Rule.** Both surfaces ship light only. The app keeps dark constants in `theme.dart` for a future pass, but it is locked to light, and its splash stays light even when the system is in dark mode. The web adds a high-contrast mode (`body.high-contrast`) that pushes muted text and hairlines to near-black.

## Typography

**UI Font:** Roboto in the Android app, taken from the system rather than bundled (every Android phone ships it, offline included; iOS falls back to its system font); Source Sans 3 on the web (Google Fonts). Both fall back to `sans-serif`. In the app, Han and Hangul fall back to Noto Sans SC and Noto Sans KR.

**Character:** A plain sans carries every label, control, and card, so the tool stays unpretentious. In the app, reading text stays in Roboto and is set apart by size and generous leading (Bacaan), not by a second face.

### Hierarchy
The frontmatter roles are the app's `T` class in `theme.dart`. The scale steps by about 1.15 (12 · 13 · 15 · 17 · 20 · 36), and roles that share a size differ by weight. Body text sits at 15 and nothing goes below 12. **Hero** (600, 26, 1.2) is the one headline on the app's Home header.
- **Display** (700, 36, 1.1, tabular figures): the single headline number on a screen, such as the collection total.
- **Judul Detail** (600, 22, 1.3): the title of a document, article, or other detail page. It pairs with Bacaan.
- **Judul** (700, 20, 1.3): screen and section titles, app bar titles.
- **Angka** (700, 20, 1.15, tabular): standout data such as the document number and KPI values.
- **Subjudul** (700, 17, 1.35): subsection titles and featured-card titles.
- **Judul Item** (700, 15, 1.35): card titles in lists.
- **Bacaan** (400, 16, 1.65): long reading text: article bodies, abstracts, PUU and disability descriptions, AI answers. It sits in a column of at most 720dp.
- **Isi** (400, 15, 1.5): ordinary UI text. Input text and list-tile titles take 16.
- **Isi Kecil** (400, 13, 1.45): metadata, summaries, notes.
- **Label Besar / Label / Label Kecil** (600, 15 / 13 / 12): button labels, chips and pills, then badges and chart axes.

Tracking is normal (0) on every role, as on the web. Each `T` role sets `letterSpacing: 0`, which overrides Material 3's default 0.25 to 0.5px tracking.

**Web:** the web uses Tailwind steps in the same family. Body is mostly `text-sm` (14) with `text-xs` (12) metadata. The hero headline runs `text-2xl → md:text-4xl → lg:text-5xl`, bold, `leading-[1.1]`, `tracking-tight`, `text-balance`. All headings h1 to h6 are 700 via the base layer.

### Named Rules
**The One-Family UI Rule.** One sans carries every interface element on each surface: Roboto in the app, Source Sans 3 on the web. No extra display face, and no Poppins or Inter, which were legacy on two web screens.

**The Reading-Role Rule.** Long text that is read (article bodies, abstracts, AI answers) uses Bacaan, never Isi: the larger size and 1.65 leading are what make it readable. The app had Source Serif 4 for this, then briefly Kanit for everything; then Source Sans 3; since 2026-10-05 the owner is trying Roboto everywhere in the app.

**The Role-Not-Size Rule.** Every app text uses a `T` role. A literal `fontSize:` outside `theme.dart` fails `test/tipografi_test.dart`. The app once had 21 ad-hoc sizes, and this test keeps that from coming back.

## Layout

**Spacing:** a 4-based scale (`xs` 4 · `sm` 8 · `md` 12 · `lg` 16 · `xl` 24 · `xxl` 32). Groups are tight and sections are separated generously. A section header opens with 28 of space above and 14 below. Screen gutters are 16.

**Android app: window classes, not devices.** Layout follows the window width (Material 3 classes), so a rotated phone, an unfolded foldable, and split-screen all follow the same rules.
- **Under 600dp (compact):** single column; the full-width bottom nav with the raised circular Ask-AI action.
- **600dp and up (medium):** a NavigationRail replaces the bottom nav, with Ask AI as a 56dp amber circle at the top. Card lists become a grid of 2 columns from 720dp of content width and 3 columns from 1200dp. The grid is capped at 1200dp and centered.
- **840dp and up (expanded):** document lists and text-search results go master-detail: a 400dp list on the left, detail on the right, and a tap swaps the panel instead of pushing a page. Home splits into two columns.
- **Reading column:** detail pages, profile, survey, menu, and AI chat cap their content at 720dp (video at 960dp) and center it, while the scroll area stays full width.
- Tab state survives rotation, and AI conversations, search results, and scroll positions are never lost when the window class changes.
- Text scaling to 200% is supported. Fixed-height containers scale with text through `skalaTeks`, and nav and rail labels clamp at 1.5×.

**Web:** Tailwind's default breakpoints (`sm` 640 · `md` 768 · `lg` 1024 · `xl` 1280). Content sits in centered containers on the slate-50 canvas, and the hero is a single search field with two actions (Cari Dokumen, Tanya AI).

### Named Rules
**The Reading Column Rule.** Long text never runs wider than 720dp, whatever the window. A tablet makes the margins wider, not the measure.

## Elevation & Depth

Flat at rest. Surfaces sit flat on the Cool Page canvas and are separated by hairline borders (`line`) and the white-on-slate tonal step. Shadow is reserved for things that genuinely float or that respond to state.

### Shadow Vocabulary
- **Floating nav** (`0 4px 16px rgba(15, 23, 42, 0.08)`): the app's floating bottom bar, the only resting shadow in the app.
- **Ask-AI lift** (Material elevation, shadow `rgba(15, 23, 42, 0.3)`): the raised amber center action, which floats above the bar inside a 4dp ring of canvas color that reads as a notch.
- **Segment** (`0 1px 3px rgba(15, 23, 42, 0.06)`): the selected segment of the AI-mode switch.
- **Web resting card** (`shadow-sm` with `ring-1 ring-slate-200`): the default for web cards and panels.
- **Web hover / float** (`shadow-lg`; `shadow-xl` to `shadow-2xl` for dropdowns, suggestion panels, and modals): a state response or a true floating layer.

### Named Rules
**The Flat-At-Rest Rule.** A shadow is a response to floating or to state. It is never decoration baked into every card. App cards have a border and no elevation (`elevation: 0`, no surface tint).

## Shapes

One corner for everything: **4px** (`rounded.sm`). It covers cards, buttons, inputs, chips, badges, sheets, dialogs, snackbars, and nav indicators on both surfaces. The app expresses it as `AppRadius` (chip, button, card, sheet = 4), and the web uses the `rounded` class, by explicit owner convention. `rounded.full` is only for dots, avatars, and true pills on the web. Edges and accent lines take no radius. The section-header bar (4×20, amber) is square. The one exception is the app's Ask-AI action, a circle (owner request, 2026-10-05).

### Named Rules
**The One-Corner Rule.** If it is rounded, it is 4px. Larger radii on cards or buttons (8, 12, 16) are pre-redesign leftovers and should not spread.

## Components

Clear and efficient: familiar shapes, 48dp targets, and the full state set (default, pressed, focused, disabled, loading, error, empty). The vocabulary stays identical from screen to screen, so the tool feels like one place.

### Buttons
- **Shape:** 4px, minimum 48×48.
- **Primary (filled):** amber fill with ink label (Label Besar), padding 12×20. It is for the one main action on a screen: search, submit, view document. Disabled drops to amber at 35% with ink at 55%.
- **Outlined:** white with a Field Edge border and ink label. Use it for secondary actions such as Muat ulang or Salin.
- **Text:** a Civic Blue label with no container, for links and tertiary actions (Lihat semua, Muat lanjutan).
- **Web:** primary buttons are usually `px-4` with `py-2` to `py-3`, use a `transition` of about 150 to 200ms to `primary-hover`, and show `focus-visible:ring-2` (often `ring-primary/50` with `ring-offset-2`). Apply the Ink-On-Amber Rule to their labels.

### Chips & Badges
- **Document type chip:** Civic Blue at 8% fill, blue Label Kecil, no border, padding 3×8.
- **Status badge:** the semantic status pair, Label Kecil, single line, right-aligned in its card header.
- **Filter pills and filter dropdowns:** white with a hairline border at rest, 48dp tall. Selected takes the same current-position marker as the nav: amber at 16% with a 55% amber border and an Amber Ink label at 700. Never a solid amber fill, which reads as a primary button.

### Cards / Containers
- **Corner Style:** 4px.
- **Background:** white on the Cool Page canvas, with a Hairline border.
- **Shadow Strategy:** none in the app; the web's resting card uses `shadow-sm` (see Elevation & Depth).
- **Internal Padding:** 12 in app list cards, 16 on larger panels and promo cards.
- **Press:** interactive app cards scale to 0.985 while pressed, together with the ink ripple. The press releases once the finger moves past the touch slop.

### Inputs / Fields
- **Style:** white fill, a Field Edge border, 4px, padding 12×14, text Isi at 16, placeholder Ink Muted.
- **Focus:** a 2dp Civic Blue border. The cursor and selection handles are also blue, with selection at 25%. On the web: `focus:ring-2` in blue. Never remove the focus affordance.
- **Error:** a `status-revoked` border with an inline message under the field. Forms scroll to the first error. A character counter appears past 80% of `maxLength`.
- **Forms (app):** labels sit above the field in Label Besar; optional fields carry a muted "Opsional" tag instead of required fields carrying an asterisk. Long forms group fields into white bordered panels, each led by a 32dp blue-tinted icon tile and a Subjudul heading. Single choices with a few options are choice chips; a 1-5 rating is a row of five 48dp numbered boxes (the chosen one amber with ink text, the ones below it tinted amber). The submit button lives in a white bar pinned to the bottom with a count of filled required fields and a thin progress line. The survey is the reference.

### Navigation
- **App bar (app):** `PolaBiru`: a Civic Blue → Blue Night (`#02315c`) gradient with a dot grid fading from the top-right corner, three thin white rings, and one short amber arc. White Judul title on one line, light status-bar icons. Tabs sit on a white strip beneath it. The same pattern is the app's Home header and the document-detail hero, and the Home AI promo card. The Home header stacks: a brand row (the white-reversed JDIHN logo on a translucent white tile like the language pill, "JDIH Kota Kendari" with its full name beneath), the greeting and Hero title, the Tolaki motto as a quote (a hanging amber quote mark, italic white text, its translation below; same order as the web hero), then the white search card overlapping the blue edge. It replaced the old 3dp amber-to-blue signature line.
- **Loading (app):** every page's skeleton is the page itself: `LoadView(contoh: …)` renders the real builder with sample data inside Skeletonizer, and lists render real cards with `kSkeletonItem`. The PDF viewer shows a download bar over an A4 page skeleton. No generic card-list skeleton.
- **Bottom nav (app, under 600dp):** a full-width white bar with a hairline top edge and a soft upward shadow, extending under the system gesture area. Labels always show (Label, clamped at 1.5×). The active item has a 24×3 amber marker on the bar's top edge, a slightly enlarged icon, and Amber Ink for its icon and label. Inactive items are Ink Muted.
- **Ask-AI center action (app):** a 56dp amber circle with an ink sparkle icon and an amber glow, raised 24dp above the bar inside a 5dp white ring, labeled like the other items. Its accessible label is "Tanya AI dan pencarian". The bar hides inside the AI screen, so the conversation gets the full height.
- **Rail (app, 600dp and up):** a NavigationRail with a white background, a minimum width of 88, and the same amber indicator and Amber Ink labels. Ask AI leads it as a 56dp amber circle.
- **Transitions (app):** switching bottom-nav tabs is instant, because a tab is not a journey; only Ask AI slides up from the bottom and back down. Pushed pages slide in from the right while the page beneath shifts slightly left, WhatsApp-style, and can be closed with an edge swipe. Reduced motion shows pages without movement.
- **System back (app):** from any tab other than Home, Back returns to Home first, and from Ask AI it returns to the originating tab. Only Home exits.
- **Web:** a horizontal top bar with ink links, an amber active marker, the language switcher (id / en / zh / ko), and accessibility controls (text-to-speech, voice search). On small screens it collapses to a toggle menu with targets of 44px or more.

### Section Header (signature)
A 4×20 amber bar, then a Judul title, then an optional "Lihat semua" text link in Civic Blue, held to 45% of the row and right-aligned. It is the app's one recurring brand mark inside content.

### Document Card (signature)
The heart of retrieval. A 56-wide number block on the canvas color shows "No." in Label Kecil, the number in Angka, and the year. Beside it sit the type chip and status badge in one row, the Judul Item title, and the view and download counts with a chevron. There is no colored side stripe; status lives only in the badge. In master-detail, the selected card takes a 2px Civic Blue border.

### AI Chat (signature)
- **User question:** a Civic Blue bubble with white Isi text, padding 10×14.
- **AI answer:** a white bordered card with an Ink Muted label beside the Amber Ink sparkle, answer text in Bacaan, and a typing reveal at 12ms per character, held between 0.8 and 6 seconds.
- **Attachments:** a document attachment list that expands in place without scrolling the view.
- **Mode switch:** a 52-high track on Second Layer, with the selected segment white, bordered, and lifted by the Segment shadow.

### Motion
Motion is one quiet grammar, an ease-out rise.
- **App:** pages fade in and rise 3%, with ease-out-cubic. Tabs only fade, over 200ms. A list fades in once as a whole (opacity over 200ms, a 1.5% rise over 240ms) with no per-card stagger. Cards press to 0.985.
- **Web:** `animate-rise` runs 0.7s `cubic-bezier(0.16, 1, 0.3, 1)` from a 16px offset, with stagger via `animation-delay` at 0.08s per item.
- **Both:** honor reduced motion; the app checks `disableAnimations` and the web uses `prefers-reduced-motion`. AOS is not loaded on the web, so leftover `data-aos` attributes are inert.

## Do's and Don'ts

### Do:
- **Do** keep Kendari Amber to primary action, current position, and AI, under about 10% of any screen.
- **Do** put Ink (`#0f172a`) on amber fills, and use Amber Ink (`#a65307`) whenever amber must be text.
- **Do** use Civic Blue for links, information chips, form controls, focus, data, and the user's own chat bubble.
- **Do** hold body text at Ink or Ink Muted on white, at 4.5:1 or better (3:1 for large text and UI component edges).
- **Do** use the `T` roles in the app, Roboto for every UI element in the app, and Source Sans 3 on the web.
- **Do** set reading text and detail titles in the app with the Bacaan and Judul Detail roles.
- **Do** use a 4px corner on everything rounded, and keep edge lines square.
- **Do** keep surfaces flat at rest with hairline borders; shadow only what floats or responds to state.
- **Do** cap reading content at 720dp and switch layout by window width (600 / 840), not by device.
- **Do** give every interactive element a visible focus state and a 48dp target, and test at 200% text.
- **Do** honor reduced motion everywhere.

### Don't:
- **Don't** recreate the old-government-portal feel: dense raw tables without hierarchy, cramped small type, or documents buried behind layered menus.
- **Don't** put white text on amber; it is 2.4:1.
- **Don't** flood sections with amber fills, or use amber for status. Legal status keeps its green, yellow, and red pairs.
- **Don't** use colored side stripes (a left or right border over 1px) on cards, answers, or alerts; all of them were removed from the app. Use a badge, a tint, or a full border.
- **Don't** introduce a display face or a second font in the app.
- **Don't** write literal font sizes in app screens; add or reuse a `T` role.
- **Don't** use radii above 4px on cards and buttons, or uppercase-tracked labels above headings.
- **Don't** stagger cards one by one or animate whole sections in; one rise per list is enough.
- **Don't** let a tablet stretch phone cards or text lines; use the grid, master-detail, and the reading column.
- **Don't** clip suggestion or dropdown panels inside `overflow-hidden` containers on the web; float them above the page.
