---
name: JDIH Kota Kendari
description: Portal Jaringan Dokumentasi dan Informasi Hukum Pemerintah Kota Kendari — akses dokumen hukum yang cepat, akurat, dan inklusif.
colors:
  primary: "#ff891e"
  primary-hover: "#ea8221"
  accent: "#015ba5"
  accent-hover: "#014f8e"
  darkbg: "#0f172a"
  surface: "#ffffff"
  surface-muted: "#f8fafc"
  ink: "#1e293b"
  ink-muted: "#475569"
  border: "#d1d5db"
  overlay: "#000000a6"
typography:
  display:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "clamp(1.5rem, 4vw, 2.25rem)"
    fontWeight: 700
    lineHeight: 1.15
    letterSpacing: "normal"
  headline:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "normal"
  title:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "normal"
  body:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.6
    letterSpacing: "normal"
  label:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: "0.1em"
rounded:
  sm: "0.25rem"
  lg: "0.5rem"
  xl: "0.75rem"
  "2xl": "1rem"
  full: "9999px"
spacing:
  xs: "0.5rem"
  sm: "0.75rem"
  md: "1rem"
  lg: "1.5rem"
  xl: "2rem"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    padding: "0.75rem 2rem"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
    textColor: "{colors.surface}"
  button-accent:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.surface}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    padding: "0.75rem 2rem"
  button-accent-hover:
    backgroundColor: "{colors.accent-hover}"
    textColor: "{colors.surface}"
  input-search:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.sm}"
    padding: "0.875rem 1rem"
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    rounded: "{rounded.xl}"
    padding: "1.5rem"
---

# Design System: JDIH Kota Kendari

## 1. Overview

**Creative North Star: "The Civic Reading Room"**

JDIH Kota Kendari behaves like a well-run public reading room for legal records: calm, bright, and orderly. The primary job of every screen is retrieval — a citizen or a legal practitioner arrives looking for a specific regulation, ruling, or document, and the interface exists to walk them to it with as little friction as possible. Layouts stay open and legible; hierarchy does the guiding; the tool disappears into the task rather than performing for it.

The palette is disciplined. Kendari Amber (`#ff891e`) is the room's signage — used to point, mark the current place, and label the primary action, never to decorate a whole surface. Civic Blue (`#015ba5`) is the institutional voice for secondary actions and links. Everything else is quiet: white content surfaces, cool slate neutrals, and readable ink. Warmth comes from the amber accent and from clear, generous typography — not from a tinted body background.

This system explicitly rejects the old-government-portal feel: dense raw tables with no hierarchy, cramped small type, and documents buried behind layered menus. It is not a cold archive that intimidates a first-time visitor, and it is not a flashy marketing site — the register is a functional public tool, and familiarity is a feature.

**Key Characteristics:**
- Retrieval-first: every screen shortens the path to a document.
- One warm accent (amber) used sparingly, one institutional accent (blue) for support.
- Flat surfaces lifted by soft shadows only when they separate content or respond to state.
- Single type family (Open Sans) carrying the full hierarchy — clarity over ornament.
- Accessible by default: WCAG 2.1 AA contrast, keyboard paths, `prefers-reduced-motion`, multi-language.

## 2. Colors

A disciplined civic palette: two branded accents against white surfaces and cool slate neutrals.

### Primary
- **Kendari Amber** (`#ff891e`): The signage color. Primary action buttons, the current/selected state, active nav indicator, key highlights, and the short amber underline that punctuates a hero heading. Its rarity is what makes it read as "act here." Hover deepens to **Amber Deep** (`#ea8221`).

### Secondary
- **Civic Blue** (`#015ba5`): The institutional voice. Secondary buttons, links, and informational accents where amber would over-signal. Hover deepens to **Civic Blue Deep** (`#014f8e`).

### Neutral
- **Reading Room White** (`#ffffff`): The default content surface — cards, panels, form fields.
- **Cool Page** (`#f8fafc`, slate-50): The page and section backgrounds that sit one step behind white content, and the second neutral layer under toolbars/panels.
- **Ink** (`#1e293b`, slate-800): Primary body and heading text on light surfaces.
- **Ink Muted** (`#475569`, slate-600): Secondary text, metadata, captions. Never lighter than this for body-sized copy on white — light gray is a readability failure, not elegance.
- **Hairline** (`#d1d5db`, gray-300): Input borders, dividers, card outlines.
- **Night Slate** (`#0f172a`, slate-900): The dark footer / dark-surface base, and hero image overlays (as a black wash at ~50–65% opacity, e.g. `bg-black/65`).

### Named Rules
**The Amber Sparingly Rule.** Kendari Amber marks primary action, current position, and highlight — nothing else. On any given screen it covers well under 10% of the surface. The moment amber becomes a background fill for whole sections, it stops meaning "act here."

**The No-Washed-Text Rule.** Body text on white is Ink or Ink Muted (`#1e293b` / `#475569`), never lighter. Gray text on amber or blue is forbidden — put white on those, or a darker shade of the same hue.

## 3. Typography

**Display Font:** Open Sans (weights 300–800), with `sans-serif` fallback.
**Body Font:** Open Sans — one family carries the whole hierarchy.
**Icon Font:** Font Awesome 6.5 (solid/regular), for functional glyphs only.

**Character:** One humanist sans, tuned across weight rather than paired with a second family. This keeps a public-service tone — plain, legible, unpretentious — and sidesteps the mismatch of two similar sans-serifs. Contrast in the hierarchy comes from weight and size, not from a display face. (Poppins and Inter appear on two isolated legacy screens — login and the survey thank-you — and should migrate to Open Sans, not spread.)

### Hierarchy
- **Display** (700, `clamp(1.5rem, 4vw, 2.25rem)`, line-height 1.15): Hero and page headline. Deliberately restrained — a public portal informs, it does not shout. Use `text-wrap: balance`.
- **Headline** (700, ~1.5rem/`text-2xl`, line-height 1.25): Section titles on landing and index pages.
- **Title** (600, ~1.125rem/`text-lg`, line-height 1.4): Card titles, document titles in lists.
- **Body** (400, ~0.875rem/`text-sm`, line-height 1.6): The workhorse. Descriptions, metadata, prose. Cap prose blocks at 65–75ch; document tables may run denser.
- **Label** (600, `0.75rem`/`text-xs`, letter-spacing 0.1em, often UPPERCASE): Button text, eyebrows (e.g. the hero's "Selamat Datang"), tags, and small functional labels.

### Named Rules
**The One-Family Rule.** Open Sans in multiple weights carries display through label. Do not introduce a display or secondary font for flavor; hierarchy is a weight-and-size problem here, not a font-pairing problem.

## 4. Elevation

Flat by default, lifted softly. Surfaces sit flat on the cool page background and are separated primarily by whitespace, hairline borders, and the white-on-slate-50 tonal step. Shadows are a light touch — they group a card or answer a hover, never carry the whole layout. Depth is quiet, matching the reading-room calm.

### Shadow Vocabulary
- **Resting card** (`shadow-sm` → `shadow-md`, roughly `0 1px 3px rgb(0 0 0 / 0.1)`): Separates a white card from the page beneath it. The default for document cards and panels.
- **Hover lift** (`shadow-lg`, roughly `0 10px 15px -3px rgb(0 0 0 / 0.1)`): Applied on hover to interactive cards, often paired with a small `translateY(-2px)`.
- **Floating layer** (`shadow-xl` → `shadow-2xl`): Reserved for elements that truly float above the page — the hero search box, dropdowns/suggestion panels, and modals.

### Named Rules
**The Flat-At-Rest Rule.** Surfaces are flat when idle. A shadow that grows is a response to state (hover, float, focus) — not decoration baked into every card. If a static list of cards already carries `shadow-lg` at rest, it is too heavy; drop to `shadow-sm`.

## 5. Components

Clear and efficient: familiar shapes, comfortable targets, and complete state coverage. The vocabulary stays identical screen to screen so the tool feels like one place.

### Buttons
- **Shape:** Small radius (`rounded`, 4px) on inline/search actions; `rounded-lg` (8px) acceptable on larger CTAs. Consistent within a surface.
- **Primary:** Kendari Amber background, white text, `font-semibold`, padding `0.75rem 2rem` (`px-8 py-3`), often a leading Font Awesome icon. This is the "do the main thing" button — search, submit, primary CTA.
- **Accent:** Civic Blue background, white text, same metrics — for secondary-but-prominent actions where amber would over-signal.
- **Hover / Focus:** Background deepens (`primary` → `primary-hover`, `accent` → `accent-hover`) via `transition` (~150–200ms). Focus must show a visible ring (e.g. `focus:ring-2`) — keyboard users need to see the target.
- **Ghost / Text:** Transparent with Civic Blue or Ink text for tertiary actions and links.

### Cards / Containers
- **Corner Style:** `rounded-lg` (8px) for compact list cards, `rounded-xl` (12px) for feature/section cards, `rounded-2xl` (16px) for prominent panels like the hero search box.
- **Background:** Reading Room White on the Cool Page background.
- **Shadow Strategy:** `shadow-sm`/`shadow-md` at rest, lift to `shadow-lg` on hover for clickable cards (see Elevation).
- **Border:** Optional Hairline (`#d1d5db`) when a shadow alone doesn't separate enough.
- **Internal Padding:** `1.5rem` (`p-6`) typical; `2rem` (`p-8`) on larger panels.

### Inputs / Fields
- **Style:** White background, Hairline border (`border-gray-300`), `rounded` (4px), padding `~0.875rem 1rem` (`py-3.5 px-4`). Search fields carry a leading magnifier icon inset on the left.
- **Focus:** Border/ring shift to Civic Blue (`focus:ring-2 focus:ring-blue-500`), outline removed only when a visible ring replaces it. Never remove focus affordance entirely.
- **Placeholder:** Ink Muted, meeting the same 4.5:1 contrast as body text — not a faint gray.

### Navigation
- **Style:** Horizontal top bar; Open Sans, Title/Label weights. Default links in Ink, hover to Civic Blue or Kendari Amber, active item marked with an amber indicator. Includes a language switcher (id / en / zh / ko) and accessibility controls (text-to-speech, voice search).
- **Mobile:** Collapses to a toggle menu; touch targets stay ≥44px.

### Signature: Document Search & Result List
The heart of the product. A prominent search field (hero: floating white-on-dark, `rounded-2xl`, backdrop-blurred over the Kendari photo overlay) leads into result lists of document cards. Each card leads with the document title (Title weight), followed by type/number/year metadata in Ink Muted, and clear actions (view, download, QR). Optimize this path relentlessly — it is where success is won or lost.

## 6. Do's and Don'ts

### Do:
- **Do** keep Kendari Amber (`#ff891e`) to primary action, current state, and highlight — under ~10% of any screen.
- **Do** use Civic Blue (`#015ba5`) for secondary actions, links, and focus rings.
- **Do** hold body text at Ink (`#1e293b`) or Ink Muted (`#475569`) on white; verify ≥4.5:1 (≥3:1 for large/bold text).
- **Do** carry the whole type hierarchy with Open Sans in different weights.
- **Do** keep surfaces flat at rest; add shadow only on hover, float, or focus.
- **Do** give every interactive element a visible focus state and keep touch targets ≥44px.
- **Do** honor `prefers-reduced-motion` for every AOS/scroll reveal (already wired globally) and keep transitions ~150–250ms.

### Don't:
- **Don't** recreate the old-government-portal feel: dense raw tables with no hierarchy, cramped small type, or documents buried behind layered menus.
- **Don't** flood whole sections with amber fills — it destroys the "act here" signal.
- **Don't** use light/faint gray for body or placeholder text "for elegance"; it fails contrast and readability.
- **Don't** introduce a display or second body font (no spreading Poppins/Inter); it breaks the One-Family Rule.
- **Don't** put gray text on amber or blue backgrounds — use white or a darker shade of the same hue.
- **Don't** use `border-left`/`border-right` thicker than 1px as a colored accent stripe on cards or alerts; use a full border, a tint, or a leading icon instead.
- **Don't** ship interactive components with half their states — default, hover, focus, active, disabled, loading, and error all matter.
- **Don't** render suggestion/dropdown panels inside `overflow-hidden` containers where they clip; float them properly (fixed/portal) above the page.
