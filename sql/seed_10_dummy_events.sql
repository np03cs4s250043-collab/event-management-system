-- Seed: 10 dummy events for Eventify
-- Usage:
--   1) Make sure database `eventify` is selected.
--   2) Run this file in phpMyAdmin SQL tab or mysql client.

USE `eventify`;

-- Pick an organizer user if available; otherwise fall back to first admin.
SET @organizer_id := (
  SELECT id FROM users WHERE role = 'organizer' ORDER BY id LIMIT 1
);
SET @organizer_id := COALESCE(
  @organizer_id,
  (SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1)
);

-- Insert 10 events (idempotent by unique slug via INSERT IGNORE).
INSERT IGNORE INTO events (
  organizer_id, category_id, title, slug, description, venue, city,
  date_start, date_end, capacity, cover_image, is_recurring, recurrence, status
) VALUES
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'concert' LIMIT 1),
  'Kathmandu Indie Nights Vol. 1',
  'kathmandu-indie-nights-vol-1-dummy-2026',
  'A live indie music showcase featuring emerging Nepali artists.',
  'Naxal Open Grounds',
  'Kathmandu',
  '2026-05-03 18:30:00',
  '2026-05-03 22:00:00',
  300,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'conference' LIMIT 1),
  'Tech Horizons Nepal 2026',
  'tech-horizons-nepal-2026-dummy-2026',
  'A one-day technology conference on AI, cloud, and cybersecurity.',
  'Bhrikutimandap Hall A',
  'Kathmandu',
  '2026-05-10 09:00:00',
  '2026-05-10 17:00:00',
  500,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'workshop' LIMIT 1),
  'Hands-on Laravel Bootcamp',
  'hands-on-laravel-bootcamp-dummy-2026',
  'Practical workshop covering MVC, APIs, and deployment workflows.',
  'Pulchowk Innovation Lab',
  'Lalitpur',
  '2026-05-15 10:00:00',
  '2026-05-15 16:00:00',
  120,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'webinar' LIMIT 1),
  'Remote Product Management Masterclass',
  'remote-product-management-masterclass-dummy-2026',
  'Interactive online session on modern product management practices.',
  'Zoom Live Session',
  'Online',
  '2026-05-20 19:00:00',
  '2026-05-20 21:00:00',
  1000,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'sports' LIMIT 1),
  'Valley 5K Community Run',
  'valley-5k-community-run-dummy-2026',
  'A fun and inclusive 5K run across the city for all age groups.',
  'Tundikhel Ground',
  'Kathmandu',
  '2026-05-24 06:00:00',
  '2026-05-24 09:00:00',
  800,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'festival' LIMIT 1),
  'Spring Food & Culture Fest',
  'spring-food-culture-fest-dummy-2026',
  'Street food, local crafts, and cultural performances all weekend.',
  'Bhaktapur Durbar Square Perimeter',
  'Bhaktapur',
  '2026-05-30 11:00:00',
  '2026-05-30 22:00:00',
  1500,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'exhibition' LIMIT 1),
  'Digital Art Expo Nepal',
  'digital-art-expo-nepal-dummy-2026',
  'An exhibition of interactive digital artworks and installations.',
  'Nepal Art Council',
  'Kathmandu',
  '2026-06-04 12:00:00',
  '2026-06-04 19:00:00',
  350,
  NULL,
  0,
  'none',
  'draft'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'networking' LIMIT 1),
  'Startup Founders Mixer',
  'startup-founders-mixer-dummy-2026',
  'Curated networking for founders, investors, and builders.',
  'Maitighar Business Lounge',
  'Kathmandu',
  '2026-06-09 17:30:00',
  '2026-06-09 21:00:00',
  180,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'football' LIMIT 1),
  'Intercity Football Showdown',
  'intercity-football-showdown-dummy-2026',
  'A friendly but competitive football fixture with fan activities.',
  'Dasarath Stadium',
  'Kathmandu',
  '2026-06-14 15:00:00',
  '2026-06-14 18:00:00',
  1200,
  NULL,
  0,
  'none',
  'published'
),
(
  @organizer_id,
  (SELECT id FROM event_categories WHERE slug = 'cricket' LIMIT 1),
  'Summer T20 Charity Cup',
  'summer-t20-charity-cup-dummy-2026',
  'A T20 cricket event supporting youth sports development programs.',
  'TU Cricket Ground',
  'Kirtipur',
  '2026-06-21 09:30:00',
  '2026-06-21 17:30:00',
  1000,
  NULL,
  0,
  'none',
  'published'
);

-- Optional check:
-- SELECT id, title, status, date_start FROM events WHERE slug LIKE '%dummy-2026' ORDER BY id;

-- Seed ticket prices for the dummy events.
-- This is what the app reads as event ticket price.
INSERT INTO tickets (event_id, name, price, quantity, quantity_sold, sale_start, sale_end)
SELECT
  e.id,
  'General Admission',
  CASE e.slug
    WHEN 'kathmandu-indie-nights-vol-1-dummy-2026' THEN 1200.00
    WHEN 'tech-horizons-nepal-2026-dummy-2026' THEN 2500.00
    WHEN 'hands-on-laravel-bootcamp-dummy-2026' THEN 1800.00
    WHEN 'remote-product-management-masterclass-dummy-2026' THEN 900.00
    WHEN 'valley-5k-community-run-dummy-2026' THEN 700.00
    WHEN 'spring-food-culture-fest-dummy-2026' THEN 500.00
    WHEN 'digital-art-expo-nepal-dummy-2026' THEN 650.00
    WHEN 'startup-founders-mixer-dummy-2026' THEN 1500.00
    WHEN 'intercity-football-showdown-dummy-2026' THEN 1100.00
    WHEN 'summer-t20-charity-cup-dummy-2026' THEN 1300.00
    ELSE 1000.00
  END,
  e.capacity,
  0,
  DATE_SUB(e.date_start, INTERVAL 14 DAY),
  e.date_start
FROM events e
WHERE e.slug LIKE '%dummy-2026'
AND NOT EXISTS (
  SELECT 1 FROM tickets t WHERE t.event_id = e.id
);

-- Optional check for price:
-- SELECT e.id, e.title, t.price FROM events e JOIN tickets t ON t.event_id = e.id
-- WHERE e.slug LIKE '%dummy-2026' ORDER BY e.id;
