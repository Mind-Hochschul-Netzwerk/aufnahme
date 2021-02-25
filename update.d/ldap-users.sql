ALTER TABLE antraege ADD COLUMN statusaenderung_username VARCHAR(255) AFTER statusaenderung_uid;
UPDATE antraege SET statusaenderung_username = (SELECT username FROM users WHERE userid=statusaenderung_uid);
ALTER TABLE antraege DROP COLUMN statusaenderung_uid;

ALTER TABLE mails ADD COLUMN username VARCHAR(255) AFTER userid;
UPDATE mails SET username = (SELECT username FROM users WHERE userid=mails.userid);
ALTER TABLE mails DROP COLUMN userid;

ALTER TABLE voten ADD COLUMN username VARCHAR(255) AFTER user_id;
UPDATE voten SET username = (SELECT username FROM users WHERE userid=voten.user_id);
ALTER TABLE voten DROP COLUMN user_id;

DROP TABLE users;
