/*
ts:2017-06-20 11:45:00
*/

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`) VALUES
(
  1,
 'courses_bookings_see_seating_numbers',
 'Courses / Bookings / See seating numbers',
 'See the number of seats booked and seats available on the front end',
 (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses')
);
