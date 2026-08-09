---
name: Eco-Emerald Glass
colors:
  surface: '#05170e'
  surface-dim: '#05170e'
  surface-bright: '#2a3d33'
  surface-container-lowest: '#011109'
  surface-container-low: '#0d1f16'
  surface-container: '#11231a'
  surface-container-high: '#1b2e24'
  surface-container-highest: '#26392f'
  on-surface: '#d2e8d9'
  on-surface-variant: '#c1cab0'
  inverse-surface: '#d2e8d9'
  inverse-on-surface: '#22342b'
  outline: '#8c947d'
  outline-variant: '#424936'
  surface-tint: '#93da2c'
  primary: '#c8ff80'
  on-primary: '#203600'
  primary-container: '#9ee638'
  on-primary-container: '#3e6400'
  inverse-primary: '#426900'
  secondary: '#bdcac0'
  on-secondary: '#28332c'
  secondary-container: '#404c44'
  on-secondary-container: '#afbcb2'
  tertiary: '#e7f0e9'
  on-tertiary: '#2a322e'
  tertiary-container: '#cbd4cd'
  on-tertiary-container: '#535c56'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#aef849'
  primary-fixed-dim: '#93da2c'
  on-primary-fixed: '#112000'
  on-primary-fixed-variant: '#304f00'
  secondary-fixed: '#d9e6dc'
  secondary-fixed-dim: '#bdcac0'
  on-secondary-fixed: '#131e18'
  on-secondary-fixed-variant: '#3e4942'
  tertiary-fixed: '#dce5dd'
  tertiary-fixed-dim: '#c0c9c2'
  on-tertiary-fixed: '#151d19'
  on-tertiary-fixed-variant: '#404944'
  background: '#05170e'
  on-background: '#d2e8d9'
  surface-variant: '#26392f'
  glass-fill: rgba(22, 37, 30, 0.65)
  glass-border: rgba(255, 255, 255, 0.08)
  lime-glow: rgba(158, 230, 56, 0.25)
  muted-forest: '#1A2E23'
  status-success: '#1E3A21'
  status-warning: '#3D2E14'
  status-error: '#3D1E1E'
typography:
  headline-xl:
    fontFamily: Space Grotesk
    fontSize: 56px
    fontWeight: '700'
    lineHeight: '1.15'
    letterSpacing: -0.02em
  headline-xl-mobile:
    fontFamily: Space Grotesk
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Space Grotesk
    fontSize: 36px
    fontWeight: '700'
    lineHeight: '1.25'
  headline-md:
    fontFamily: Space Grotesk
    fontSize: 22px
    fontWeight: '600'
    lineHeight: '1.35'
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-mono:
    fontFamily: JetBrains Mono
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.4'
    letterSpacing: 0.05em
  caption:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '500'
    lineHeight: '1.4'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 32px
  section-gap: 100px
---

## Brand & Style

This design system embodies the "Modern Dark Agritech" philosophy, blending the organic richness of agricultural heritage with futuristic digital transparency. It is designed to evoke a sense of professional reliability, environmental stewardship, and data-driven progress. 

The aesthetic is centered on **Eco-Glassmorphism**: a sophisticated mix of deep, dark forest tones and translucent "glass" layers. This style avoids the clinical coldness of traditional corporate dark modes by using high-saturation green accents and soft backdrop blurs that suggest depth and vitality. The interface should feel like a high-end command center for ecological and community management—precise, illuminated, and accessible.

**Key Visual Pillars:**
- **Technological Nature:** High-contrast lime accents against obsidian-green backgrounds.
- **Layered Transparency:** Significant use of backdrop-blurs to maintain context and depth.
- **Data Precision:** Sharp, geometric headings paired with highly legible, humanistic body text.

## Colors

The palette is rooted in **Deep Forest Black** for the foundation, providing an OLED-optimized canvas that makes interactive elements "pop." 

- **Primary (Sprout Lime):** Used exclusively for call-to-actions, active indicators, and critical data highlights. It represents growth and technological energy.
- **Secondary (Dark Emerald):** Used for structural grounding, such as section backgrounds and sidebar containers.
- **Glass Fill:** The signature material of the system, providing a semi-transparent surface for cards and widgets.
- **Functional Accents:** Status colors (Success, Warning, Error) are derived from the same forest-inspired saturation levels to ensure they feel part of the ecosystem rather than jarring interruptions.
- **Sage Gray:** Reserved for secondary information and body text to reduce eye strain against the dark background.

## Typography

This system employs a dual-typeface strategy to balance brand personality with technical utility.

1.  **Space Grotesk (Headings):** Used for all major titles. Its geometric, slightly eccentric letterforms communicate the "Agritech" persona—modern and slightly technical.
2.  **Plus Jakarta Sans (Body):** A warm, approachable sans-serif that ensures long-form content is easy to read. It softens the technical edges of the dark mode.
3.  **JetBrains Mono (Data/Stats):** Used specifically for numerical data, inventory codes, and technical labels to emphasize accuracy and transparency.

**Usage Rules:**
- Use **Headline XL** for Hero sections only.
- **Label Mono** should always be uppercase when used for tags or status indicators.
- Maintain generous line-heights (1.6) for body text to ensure legibility on high-glow displays.

## Layout & Spacing

The layout follows a **Fluid Grid** system within a max-width container for desktop, ensuring the data-rich dashboards feel expansive but controlled.

- **Grid Model:** 12-column layout for desktop, 8-column for tablet, and 4-column for mobile.
- **Sectioning:** Use large `section-gap` units to allow the Deep Forest Black background to act as a visual palette cleanser between dense glass modules.
- **Dashboard Layout:** Utilizes a persistent sidebar for admin views, which collapses into a bottom-navigation bar on mobile devices to maintain thumb-zone accessibility.
- **Safe Areas:** Cards and widgets should utilize `stack-lg` (32px) internal padding to maintain the "airy" feel essential for glassmorphism.

## Elevation & Depth

Depth in this system is not created through traditional shadows, but through **Tonal Layering and Material Properties**.

1.  **The Base:** `#0A120E` (Deep Forest Black) is the lowest level.
2.  **Section Tiers:** `#111C16` (Dark Emerald) is used for slight elevation shifts in section backgrounds.
3.  **The Glass Layer:** Elements that require focus (Cards, Navigation, Modals) use the `Dark Glass Fill` with a `16px` backdrop-blur and a `1px` subtle white border.
4.  **The Glow (Interaction):** Upon hover, elements do not just lift; they emit a `Lime Glow`. This simulates a light source being placed behind or within the glass, indicating interactivity through "bioluminescent" feedback.

## Shapes

The shape language balances organic softness with structural rigidity.

- **Main Containers:** Information cards and dashboard widgets use **20px (rounded-lg)** corners to feel friendly and modern.
- **Interactive Elements:** Buttons, search bars, and navigation pills use **Pill (999px)** shapes to provide a clear contrast against the rectangular content blocks.
- **Small Accents:** Icon boxes and input fields use **12px (soft)** corners to maintain a compact, precise appearance within larger layouts.

## Components

### Buttons
- **Primary:** Pill-shaped, Sprout Lime background with Deep Forest Black text. Includes a `lime-glow` shadow on hover.
- **Secondary (Glass):** Pill-shaped, transparent with a `glass-border` and white text.
- **Icon Buttons:** 12px rounded squares with category-specific backgrounds (e.g., Agriculture: Dark Green).

### Cards (Glassmorphic)
- **Base:** 20px radius, Dark Glass Fill, 16px blur, 1px subtle white border.
- **Hover State:** 3px vertical lift and the border transitions to 40% Sprout Lime opacity.
- **Header Accents:** For organizational charts, use a 3px top-border in Sprout Lime to indicate hierarchy.

### Data Visualization Widgets
- **Gauges & Charts:** Use Sprout Lime for primary data lines with a gradient fill transitioning from 15% lime opacity to transparent.
- **Stats:** Large JetBrains Mono numbers paired with Sage Gray labels.

### Inputs & Form Fields
- **Style:** 12px rounded, Dark Emerald background, subtle 1px border.
- **Focus:** 2px Sprout Lime border with a soft lime outer glow.

### Chips & Badges
- **Status:** Pill-shaped with a "Pulse Dot" on the left.
- **Live Insights:** Sprout Lime text with a `muted-forest` background pill.