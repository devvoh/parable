CREATE TABLE `user` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`username`	TEXT NOT NULL,
	`password`	TEXT NOT NULL,
	`email`	TEXT NOT NULL,
	`created_at`	TEXT NOT NULL,
	`updated_at`	TEXT
);
INSERT INTO `user` (id,username,password,email,created_at,updated_at) VALUES (1,'parable','plaintextpasswordsarebad','parable@test.dev','2017-01-01 10:00:00',NULL);
