ALTER TABLE attendees 
ADD COLUMN room_att_after INT DEFAULT 0 AFTER room_att,
ADD COLUMN ship_att_after INT DEFAULT 0 AFTER ship_att,
ADD COLUMN night_att_after INT DEFAULT 0 AFTER night_att;
