# DVC Web Development Internship Assessment

## Contact Information
**Name:** Syam Suhith Dondapati
**Email:** syamsuhithd@gmail.com
**Phone:** +91 9347941234
**LinkedIn:** https://www.linkedin.com/in/syam-suhith-dondapati-/
**Portfolio:** https://portfolio-one-gray-40.vercel.app/
**Fake Store Website:** https://fake-store-three-delta.vercel.app/


---

## Question 1: Responsive Product Card Component
**Approach:**
I built this component using semantic HTML5 and a mobile-first CSS approach without any external frameworks. I utilized CSS Variables (Custom Properties) to establish a flexible design system, which allowed me to seamlessly implement a Dark Mode toggle. For the layout, I used Flexbox to ensure the card scales elegantly across mobile, tablet, and desktop breakpoints. The JavaScript logic strictly enforces the 1-10 quantity constraints, updates the UI interactively, and includes a fixed Toast notification and a floating bottom cart to improve user experience without causing layout shifts.

**Estimated Time Spent:** 1.5 Hours

---

## Question 2: WordPress Custom Functionality
**Approach:**
I developed a self-contained, object-oriented-inspired PHP plugin to handle the testimonials management system. For the backend, I registered a custom post type (`dvc_testimonial`) with Gutenberg REST API support and a custom Dashicon. I implemented secure meta boxes for the custom fields using nonces (`wp_nonce_field`) and strict sanitization (`sanitize_text_field`, `absint`) before saving to the database. For the frontend, I created the `[testimonials]` shortcode that accepts the requested parameters (`count`, `orderby`, `order`). To ensure the plugin works smoothly on any theme, I scoped the CSS and Vanilla JavaScript directly within the shortcode output to power the responsive carousel.

**Estimated Time Spent:** 2 Hours

---

## Question 3: API Integration & Data Handling
**Approach:**
I created a real-time weather dashboard using Vanilla JavaScript, HTML5, and CSS. The layout uses CSS Grid and Flexbox for a clean, responsive, modern UI. I utilized `async/await` and the `fetch` API to concurrently request current weather and 5-day forecast data from the OpenWeatherMap API. State management is handled through DOM manipulation, toggling loading spinners, and dynamically displaying customized error messages (e.g., catching 404 City Not Found vs. Network Errors). I also integrated the `localStorage` API to persistently save and automatically load the user's last searched city upon initialization.

**Estimated Time Spent:** 2.5 Hours

---

## Assumptions Made
* **Browser Support:** Assumed users are utilizing modern browsers that support ES6 syntax, CSS Variables, and CSS Grid/Flexbox.
* **API Key Handling (Q3):** Assumed the reviewer will provide their own OpenWeatherMap API key. I implemented a safety check in the JavaScript that intentionally throws a UI error if the default `'YOUR_API_KEY_HERE'` placeholder string is not replaced, preventing unnecessary failed network requests.
* **WordPress Environment (Q2):** Assumed the plugin will be run on a modern WordPress installation (5.8+) supporting the Gutenberg block editor and running PHP 7.4+.

---

## Live Demos
* **Question 1 (Product Card):** 
* **Question 2 (WordPress Plugin):** 
* **Question 3 (Weather Dashboard):** 