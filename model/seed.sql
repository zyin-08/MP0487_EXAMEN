-- Knockout Zone Database - Seed Data
-- This script inserts sample data for testing and development

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ========================================
-- USERS TABLE - Sample Data (5 records)
-- ========================================

-- Delete existing data (optional - comment out if you want to keep existing records)
-- DELETE FROM events;
-- DELETE FROM users;
-- ALTER TABLE users AUTO_INCREMENT = 1;
-- ALTER TABLE events AUTO_INCREMENT = 1;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `path_pfp`, `created_at`) VALUES
(1, 'admin', 'admin@email.com', '123admin', NULL, NOW()),
(2, 'john_fighter', 'john@email.com', 'password123', '1234567890_profile1.jpg', NOW()),
(3, 'maria_coach', 'maria@email.com', 'securepass456', '1234567891_profile2.png', NOW()),
(4, 'alex_promoter', 'alex@email.com', 'promo789', NULL, NOW()),
(5, 'sarah_trainer', 'sarah@email.com', 'trainer2024', '1234567892_profile3.jpg', NOW());

-- ========================================
-- EVENTS TABLE - Sample Data (5 records)
-- ========================================

INSERT INTO `events` (`id`, `title`, `event_date`, `location`, `description`, `image_path`, `created_by`, `created_at`) VALUES
(1, 'Championship Finals 2026', '2026-04-15 19:00:00', 'Madison Square Garden, New York', 'Annual knockout zone championship finals featuring top fighters from around the world. High stakes matches with prize pool of $500,000.', 'images/championship_finals.jpg', 1, NOW()),
(2, 'Spring Training Tournament', '2026-03-20 18:30:00', 'Los Angeles Convention Center, California', 'Spring training tournament for aspiring fighters. Great opportunity to showcase skills and compete against new opponents.', 'images/spring_tournament.jpg', 1, NOW()),
(3, 'Welterweight Championship', '2026-05-10 20:00:00', 'Barclays Center, Brooklyn', 'Exclusive welterweight division championship. Join us for an evening of intense combat sports action and entertainment.', 'images/welterweight_champ.jpg', 1, NOW()),
(4, 'Regional Knockout Series', '2026-03-28 17:00:00', 'American Airlines Center, Dallas', 'Regional series featuring emerging talent from Texas and surrounding states. Early bird tickets available now.', 'images/regional_knockout.jpg', 1, NOW()),
(5, 'International Fighters Summit', '2026-06-05 19:30:00', 'Dubai World Trade Center, UAE', 'Premier international event bringing together the best fighters and coaches from every continent. Multi-day event with seminars and competitions.', 'images/international_summit.jpg', 1, NOW());

-- ========================================
-- Update AUTO_INCREMENT counters
-- ========================================

ALTER TABLE `users` AUTO_INCREMENT = 6;
ALTER TABLE `events` AUTO_INCREMENT = 6;

COMMIT;
